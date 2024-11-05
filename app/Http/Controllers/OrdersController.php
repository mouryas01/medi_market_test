<?php

namespace App\Http\Controllers;

use App\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
//use Kreait\Firebase\Auth;
use Session;

class OrdersController extends Controller
{
    public static $reference = "orders";
    private $viewPath = "order.";

    /**
     * CategoriesController constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();        
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                     
        $store_keys = []; 
        $uid = Session::get('uid');   
        if (Session::get('user_type') == 1) {

            $orders = self::getOrders();

        }elseif (Session::get('user_type') == 2){

            //fetch vendor stores
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue();                           
            foreach($getStores as $storekey => $storeArray){
                $store_keys[] = $storekey;                
            }            

            //Fetch all orders 
            $fetchOrders = self::$database->getReference('orders')->getValue();  
            $orders = [];
            if(!empty($fetchOrders)){  

                //Set as descending order 
                $fetchOrders = array_reverse($fetchOrders, true); 
                foreach ($fetchOrders as $customer_id => $orderArray){  
                    
                    if(empty($orderArray)) {
                        continue;
                    }   

                    $customer_id = trim(trim('"'.$customer_id.'"','"'));                   
                    foreach ($orderArray as $orderkey => $cartArray){ 

                        if(empty($cartArray)){
                            continue;
                        } 
                    
                        $order_key = !empty($orderkey) ? $orderkey:'';                          
                        $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();  
                        $store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';
                        $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                        //filter by order date, store wise products (related to vendor)
                        if(in_array($store_id, $store_keys))
                        {                                              
                            $order = new Orders();
                            $order->customer_id = $customer_id;
    
                            //Order Info like date, time, status and  amount etc   
                            $order->order_key = $order_key;  
                            $order->store_id = $store_id; 
                            $order->payment_status = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
                            $order->payment_type = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                
                            $order->order_date = $order_date;
                            $order->order_time = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
                            $order->order_status = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
                            $order->total_items = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
                            $order->total_amount = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
                            $order->status = !empty($getOrderDate['status']) ? $getOrderDate['status']:''; 
                            $order->created_at = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                
                                                
                            //Cart items
                            $order->cart_items = !empty($cartArray) ? $cartArray:'';                    
        
                            array_push($orders, $order);
                        }
                    }                               
                }                 
            } 

        }else{
            $orders = array();            
        }  
         
        $data = [
            'title' => "Orders",
            'orders' => $orders,
            'user_id' => $uid,
            'storeIds' => $store_keys,
        ];            
        //dd($data);
        return view($this->viewPath."index")->with($data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($customer_id, $order_key)
    {                     
        $order = array();             
        $getCartItems = self::$database->getReference('orders'."/".$customer_id."/".$order_key)->getValue();                               
        if(!empty($getCartItems)){    

            $order['customer_id'] = $customer_id;
            $order['order_key'] = $order_key;
            $order['cart_items'] = $getCartItems;

            //Order Info like date, time, status and  amount etc
            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();
            $order['store_id'] = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';    
            $order['payment_status'] = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
            $order['payment_type'] = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                              
            $order['order_date'] = !empty($getOrderDate['date']) ? $getOrderDate['date']:'';
            $order['order_time'] = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
            $order['order_status'] = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:''; 
            $order['reason'] = !empty($getOrderDate['reason']) ? $getOrderDate['reason']:'';                                             
            $order['total_items'] = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
            $order['total_amount'] = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
            $order['created_at'] = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                            

            //Fetch delivery address from address Info table based on address id
            $order['address_info'] = '';
            if(!empty($getOrderDate['address_id']))
            {
                $address_id = $getOrderDate['address_id'];
                if($address_id == 'Self Pickup'){
                    $order['address_info'] = $address_id;
                }else{
                    $getAddress = self::$database->getReference('address_info/'.$customer_id.'/'.$address_id)->getValue();            
                    $order['address_info'] = !empty($getAddress) ? $getAddress:'';
                }
            } 
        }           
        $data = [
            'title' => "View Order",
            'orders' => $order            
        ];  
        //dd($data);     
        return view($this->viewPath."show")->with($data);
    }


    /**
     * Fetch all Orders llst
     */
    public function getOrders() : array {      
                         
        $fetchOrders = self::$database->getReference(self::$reference)->getValue();  
        $orders = [];
        if(!empty($fetchOrders)){

            //Set as descending order 
            $fetchOrders = array_reverse($fetchOrders, true); 
           
            foreach ($fetchOrders as $customer_id => $orderArray){  
                
                if(empty($orderArray)) {
                    continue;
                }   

                $customer_id = trim(trim('"'.$customer_id.'"','"'));                   
                foreach ($orderArray as $order_key => $cartArray){ 
                   
                    if(empty($cartArray)) {
                        continue;
                    } 

                    $order = new Orders();
                    $order->customer_id = $customer_id;

                    //Order key
                    $order->order_key = !empty($order_key) ? $order_key:''; 

                    //Order Info like date, time, status and  amount etc
                    $getOrderDate = self::$database->getReference('order_info/'.$order->order_key)->getValue();  
                    $order->store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';    
                    $order->payment_status = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
                    $order->payment_type = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                
                    $order->order_date = !empty($getOrderDate['date']) ? $getOrderDate['date']:'';
                    $order->order_time = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
                    $order->order_status = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
                    $order->total_items = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
                    $order->total_amount = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
                    $order->status = !empty($getOrderDate['status']) ? $getOrderDate['status']:''; 
                    $order->created_at = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                
                                        
                    //Cart items
                    $order->cart_items = !empty($cartArray) ? $cartArray:'';                    

                    array_push($orders, $order);
                }                               
            } 
            //dd($orders);
        }        
        return $orders;
    }



    public function fetchVendorOrders($uid)
    {                    
        //fetch vendor stores
        $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue(); 
        $store_keys = [];            
        foreach($getStores as $storekey => $storeArray){
            $store_keys[] = $storekey;                
        }            

        //Fetch all orders 
        $fetchOrders = self::$database->getReference('orders')->getValue();  
        $orders = [];
        if(!empty($fetchOrders)){            
            foreach ($fetchOrders as $customer_id => $orderArray){                  
                if(empty($orderArray)) {
                    continue;
                }   

                $customer_id = trim(trim('"'.$customer_id.'"','"'));                   
                foreach ($orderArray as $orderkey => $cartArray){ 
                                    
                    $order_key = !empty($orderkey) ? $orderkey:'';                          
                    $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();  
                    $store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';

                    //filter store wise orders (related to vendor)
                    if(in_array($store_id, $store_keys))
                    {                                              
                        $order = new Orders();                        
                        $order->customer_id = $customer_id;
        
                        //Order key
                        $order->order_key = $order_key;  
                        $order->store_id = $store_id; 

                        //Order Info like date, time, status and  amount etc   
                        $order->payment_status = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
                        $order->payment_type = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                
                        $order->order_date = !empty($getOrderDate['date']) ? $getOrderDate['date']:'';
                        $order->order_time = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
                        $order->order_status = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
                        $order->total_items = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
                        $order->total_amount = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
                        $order->status = !empty($getOrderDate['status']) ? $getOrderDate['status']:''; 
                        $order->created_at = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                
                                            
                        //Cart items
                        $order->cart_items = !empty($cartArray) ? $cartArray:''; 
                        
                        array_push($orders, $order);
                    }
                }                               
            }                 
        } 
        
        return $orders;
    }



    /**
     * Fetch all Orders, filter by date
     */
    public function orderFilterByDate(request $request)
    {      
        $store_keys = [];  
        $uid = Session::get('uid');             
        $validator = Validator::make($request->all(), [
            'startdate' => "required",
            'enddate' => "required"  
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors()->all());       
        }
        else
        {                        
            $startdate = $request->input("startdate");
            $enddate = $request->input("enddate"); 
            if(Session::get('user_type') == 1){

                //Fetch all orders 
                $fetchOrders = self::$database->getReference('orders')->getValue();  
                $orders = [];
                if(!empty($fetchOrders)){
        
                    //Set as descending order 
                    $fetchOrders = array_reverse($fetchOrders, true);                    
                    foreach ($fetchOrders as $customer_id => $orderArray){  
                        
                        if(empty($orderArray)) {
                            continue;
                        }   

                        $customer_id = trim(trim('"'.$customer_id.'"','"'));                   
                        foreach ($orderArray as $orderkey => $cartArray){ 

                            if(empty($cartArray)){
                                continue;
                            } 
                           
                            $order_key = !empty($orderkey) ? $orderkey:''; 
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue(); 
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date
                            if(strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))	                           
                            {
                                $order = new Orders();
                                $order->customer_id = $customer_id;

                                //Order Info like date, time, status and  amount etc    
                                $order->order_key = $order_key;                          
                                $order->store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';    
                                $order->payment_status = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
                                $order->payment_type = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                
                                $order->order_date = $order_date;
                                $order->order_time = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
                                $order->order_status = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
                                $order->total_items = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
                                $order->total_amount = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
                                $order->status = !empty($getOrderDate['status']) ? $getOrderDate['status']:''; 
                                $order->created_at = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                

                                //Cart items
                                $order->cart_items = !empty($cartArray) ? $cartArray:'';                    
            
                                array_push($orders, $order);
                            }
                        }                               
                    } 
                }   

            }else{
                                
                //fetch vendor stores
                $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue();                           
                foreach($getStores as $storekey => $storeArray){
                    $store_keys[] = $storekey;                
                }            

                //Fetch all orders 
                $fetchOrders = self::$database->getReference('orders')->getValue();  
                $orders = [];
                if(!empty($fetchOrders)){  

                    //Set as descending order 
                    $fetchOrders = array_reverse($fetchOrders, true); 
                    foreach ($fetchOrders as $customer_id => $orderArray){  
                        
                        if(empty($orderArray)) {
                            continue;
                        }   

                        $customer_id = trim(trim('"'.$customer_id.'"','"'));                   
                        foreach ($orderArray as $orderkey => $cartArray){ 

                            if(empty($cartArray)){
                                continue;
                            } 
                        
                            $order_key = !empty($orderkey) ? $orderkey:'';                          
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();  
                            $store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date, store wise products (related to vendor)
                            if(in_array($store_id, $store_keys) && strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))
                            {                                              
                                $order = new Orders();
                                $order->customer_id = $customer_id;
        
                                //Order Info like date, time, status and  amount etc   
                                $order->order_key = $order_key;  
                                $order->store_id = $store_id; 
                                $order->payment_status = !empty($getOrderDate['payment_status']) ? $getOrderDate['payment_status']:'';  
                                $order->payment_type = !empty($getOrderDate['payment_type']) ? $getOrderDate['payment_type']:'';                
                                $order->order_date = $order_date;
                                $order->order_time = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
                                $order->order_status = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
                                $order->total_items = !empty($getOrderDate['total_items']) ? $getOrderDate['total_items']:''; 
                                $order->total_amount = !empty($getOrderDate['total_amount']) ? $getOrderDate['total_amount']:''; 
                                $order->status = !empty($getOrderDate['status']) ? $getOrderDate['status']:''; 
                                $order->created_at = !empty($getOrderDate['created_at']) ? Carbon::createFromTimestamp($getOrderDate['created_at']):'';                
                                                    
                                //Cart items
                                $order->cart_items = !empty($cartArray) ? $cartArray:'';                    
            
                                array_push($orders, $order);
                            }
                        }                               
                    }                 
                } 
            }
        }        
        
        $data = [
            'title' => "Orders",
            'orders' => $orders,
            'user_id' => $uid,
            'storeIds' => $store_keys,
        ]; 
        return view($this->viewPath."index")->with($data);
    }


    
    /**
     * Product approve / reject   
     */
    public function payment(request $request)
    {                            
        $order_id = trim($request->input("order_id"));    
        $customer_id = trim($request->input("customer_id")); 
        $payment_type = trim($request->input("payment_type"));      
        
        //Get customer device token 
        $getCustomerInfo = self::$database->getReference('fd_users')->orderByChild("uid")->equalTo($customer_id)->getValue();	
        if(!empty($getCustomerInfo)){
            foreach($getCustomerInfo as $key => $value){
            
                $checkstatus = self::$database->getReference('order_info/'.$order_id)->getValue();
                if($checkstatus){
                    // Payment_type : cash, online
                    self::$database->getReference('order_info/'.$order_id)->getChild('payment_type')->set($payment_type);
                    // 1 = Payment Done (payment_status)
                    self::$database->getReference('order_info/'.$order_id)->getChild('payment_status')->set(1); 
                    // 4 = Delivered (order_status)
                    self::$database->getReference('order_info/'.$order_id)->getChild('order_status')->set(4);  
                }  
                
                $lang = !empty($checkstatus['lang']) ? $checkstatus['lang']:'';
                if(trim($payment_type) == 'cash'){
                    if($lang == 'it'){
                        $payment_type = 'Contanti';
                        $title = 'Pagamento in contanti';
                    }else{                        
                        $title = 'Cash Payment';
                    }                     
                }else{
                    if($lang == 'it'){
                        $payment_type = 'in linea';
                        $title = 'Pagamento online';
                    }else{
                        $title = 'Online Payment';  
                    }
                }
                if($lang == 'it'){
                    $description = 'Il tuo ordine no.'. $order_id . ' il pagamento è stato ricevuto da '.$payment_type;
                }else{
                    $description = 'Your Order no.'. $order_id . ' payment has been received by '.$payment_type;
                }
                
                //Send notification to customer
                $this->send_notification($value['device_token'], $title, $description); 
            }

            $ostatus = '';
            $order_status = self::$database->getReference('order_info/'.$order_id)->getChild('order_status')->getValue();
            if($order_status){
                $ostatus = 'Delivered';
                $button = '<button class="btn btn-info btn-sm">Delivered</button>';
            }
            $message = 'Order Delivered Successfully';
        }
        return response()->json(['success'=> $message,'arbtn'=> $button,'ostatus'=> $ostatus]);        
    }


    /**
     * Product approve / reject / shipped  
     */
    public function approve(request $request)
    {                               
        $order_id = trim($request->input("order_id")); 
        $customer_id = trim($request->input("customer_id"));
        $reason = trim($request->input("reason")); 
        
        $action = $request->input("action"); 
        if($action == 'approve'){
            $status = 1;    //Accept
            $ostatus = 'Packed'; 
            $message = 'Order Approved';
            $button = '';             
        }elseif($action == 'shipped'){
            $status = 2;    //Shipped
            $ostatus = 'Shipped'; 
            $message = 'Order Shipped';
            $button = '';            
        }else{       
            $status = 3;    //Reject (Cancel)
            $ostatus = 'Cancelled'; 
            $message = 'Order Rejected';
            $button = '<button class="btn btn-warning btn-sm">Rejected</button>';
        }        

        $checkstatus = self::$database->getReference('order_info/'.$order_id)->getValue();        
        if($checkstatus){
            //If status 3 (Reject), then set rejection reason
            if($status == 3){
                self::$database->getReference('order_info/'.$order_id)->getChild('reason')->set($reason);
            }
			self::$database->getReference('order_info/'.$order_id)->getChild('order_status')->set($status);
                        
            //Get customer device token 
            $getCustomerInfo = self::$database->getReference('fd_users')->orderByChild("uid")->equalTo($customer_id)->getValue();	
            if(!empty($getCustomerInfo)){
                foreach($getCustomerInfo as $key => $value){

                    $lang = !empty($checkstatus['lang']) ? $checkstatus['lang']:'';
                    if($action == 'approve'){                                                      
                        if($lang == 'it'){                
                            $title = 'Ordine approvato';
                            $sendmessage = 'Il tuo ordine è stato accettato dal venditore';
                        }else{                        
                            $title = 'Order Approved';
                            $sendmessage = 'Your order has been accepted by vendor';
                        } 
                    }elseif($action == 'shipped'){
                        if($lang == 'it'){                
                            $title = 'Ordine spedito';
                            $sendmessage = 'Il tuo ordine è stato spedito dal venditore';
                        }else{                        
                            $title = 'Order Shipped';
                            $sendmessage = 'Your order has been shipped by vendor';
                        }    
                    }else{                                 
                        if($lang == 'it'){                
                            $title = 'Ordine rifiutato';
                            $sendmessage = 'Il tuo ordine è stato rifiutato dal venditore';
                        }else{                        
                            $title = 'Order Rejected';
                            $sendmessage = 'Your order has been rejected by vendor';
                        }        
                    }                
                    
                    //Send notification to customer
                    $this->send_notification($value['device_token'], $title, $sendmessage); 
                }            
            }
        }     

        return response()->json(['success'=> $message,'arbtn'=> $button,'status'=>$status,'ostatus'=> $ostatus]);        
    }


    public static function get_storeInfo($store_id)
    {
        $storeinfo = self::$database->getReference('stores/'.$store_id)->getValue();	        
        //$vendorinfo = self::$database->getReference('users/'.$storeinfo['vendor_id'])->getValue();
        return $storeinfo;         
    }

}
