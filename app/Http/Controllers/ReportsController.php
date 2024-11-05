<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Product;
use App\Category;
use App\Store;
use App\Orders;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Session;

class ReportsController extends Controller
{
    private $viewPath = "report.";

    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }

    public function salesReport()
    {                
        $uid = Session::get('uid');   
        if(Session::get('user_type') == 1){
            $orders = (new OrdersController)->getOrders();             
        }else{            
            $orders = (new OrdersController)->fetchVendorOrders($uid);           
        }        
        
        $data = [
            'title' => "Sales Orders",
            'orders' => $orders,
            'user_id' => $uid
        ]; 
        return view($this->viewPath."sales")->with($data);
    }



    public function salesReportFilterByDate(request $request)
    {      
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
                           
                            $order_key = !empty($orderkey) ? $orderkey:''; 
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue(); 
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date
                            if(strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))	                           
                            {
                                $order = new Orders();
                                $order->customer_id = $customer_id;
            
                                //Order key
                                $order->order_key = $order_key; 

                                //Order Info like date, time, status and  amount etc                             
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
                $store_keys = [];            
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
                        
                            $order_key = !empty($orderkey) ? $orderkey:'';                          
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();  
                            $store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date, store wise products (related to vendor)
                            if(in_array($store_id, $store_keys) && strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))
                            {                                              
                                $order = new Orders();
                                $order->customer_id = $customer_id;
            
                                //Order key
                                $order->order_key = $order_key;  
                                $order->store_id = $store_id; 

                                //Order Info like date, time, status and  amount etc   
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
            'title' => "Sales Orders",
            'orders' => $orders,
            'user_id' => $uid
        ]; 
        return view($this->viewPath."sales")->with($data);
    }



    //Fetch vendor products 
    public function fetchVendorProducts($uid)
    {
        //fetch vendor stores
        $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue(); 
        $store_codes = [];            
        foreach($getStores as $storekey => $storeArray){
            if(empty($storeArray)){
                continue;
            }  
            $store_codes[] = $storeArray['store_code'];                
        }    

        //fetch all products (vendor wise)
        $result = [];
        $checkProductStock = self::$database->getReference('product_supplier')->getValue();
        if(!empty($checkProductStock)){
            foreach($checkProductStock as $pskey => $productArray){
                if(empty($productArray)){
                    continue;
                }  
                $productkey = !empty($productArray['product_id']) ? trim($productArray['product_id']):'';
                $store_code = !empty($productArray['supplier']) ? trim($productArray['supplier']):'';

                //filter store wise products (related to vendor)          
                if(in_array($store_code, $store_codes)){               
                    $result[$productkey] = self::$database->getReference('products/'.$productkey)->getValue();
                }   
            } 
        }

        /*
        $products = self::$database->getReference('products')->getValue();         
        foreach($products as $productkey => $productArray){
            if(empty($productArray)){
                continue;
            }  
            $store_code = !empty($productArray['supplier']) ? $productArray['supplier']:'';

            //filter store wise products (related to vendor)          
            if(in_array($store_code, $store_codes)){                
                $result[$productkey] = self::$database->getReference('products/'.$productkey)->getValue();
            }   
        }
        */              
        return $result;
    }
    



    public function inventoryReport()
    {     
        $uid = Session::get('uid');            
        if(Session::get('user_type') == 1)  //Admin
        { 
            $result = self::$database->getReference('products')->getValue();    
        }
        elseif(Session::get('user_type') == 2)   //Vendor
        {    
            $result = $this->fetchVendorProducts($uid);   
                   
            /*$result = [];
            //fetch vendor stores
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue(); 
            $store_codes = [];            
            foreach($getStores as $storekey => $storeArray){
                if(empty($storeArray)) {
                    continue;
                }  
                $store_codes[] = $storeArray['store_code'];                
            }     

            //fetch all products            
            $products = self::$database->getReference('products')->getValue();         
            foreach($products as $productkey => $productArray){
                if(empty($productArray)) {
                    continue;
                }  
                $store_code = !empty($productArray['supplier']) ? $productArray['supplier']:'';

                //filter store wise products (related to vendor)          
                if(in_array($store_code, $store_codes)){
                    $result[$productkey] = self::$database->getReference('products/'.$productkey)->getValue();
                }   
            }  */                                      
        }
        else{
            $result = array();
        }  
                       
        $products = (new ProductsController)->getProduct($result);                             
        $data = [
            'title' => "Product",
            "products" => $products,             
        ];
        //dd($products);
        return view($this->viewPath."inventory")->with($data);
    }



    public function inventoryReportFilterByDate(request $request)
    {     
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

            if(Session::get('user_type') == 1)  //Admin
            {                  
                //fetch all products
                $result = [];
                $products = self::$database->getReference('products')->getValue();         
                foreach($products as $productkey => $productArray){
                    if(empty($productArray)) {
                        continue;
                    }  

                    $store_code = !empty($productArray['supplier']) ? $productArray['supplier']:'';
                    $created_at = !empty($productArray['created_at']) ? $productArray['created_at'] : '';

                    //filter by created date
                    if(strtotime($startdate) <= $created_at && strtotime($enddate) >= $created_at){                    
                        $result[$productkey] = self::$database->getReference('products/'.$productkey)->getValue();
                    }   
                }     
            }
            elseif(Session::get('user_type') == 2)   //Vendor
            {     
                $result = [];                       
                //fetch vendor stores
                $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue(); 
                $store_codes = [];            
                foreach($getStores as $storekey => $storeArray){
                    if(empty($storeArray)) {
                        continue;
                    }  
                    $store_codes[] = $storeArray['store_code'];                
                }     

                //fetch all products                
                $products = self::$database->getReference('products')->getValue();         
                foreach($products as $productkey => $productArray){
                    if(empty($productArray)) {
                        continue;
                    }  
                    
                    $store_code = !empty($productArray['supplier']) ? $productArray['supplier']:'';
                    $created_at = !empty($productArray['created_at']) ? $productArray['created_at'] : '';

                    //filter by created date, store wise products (related to vendor)
                    if(in_array($store_code, $store_codes) && strtotime($startdate) <= $created_at && strtotime($enddate) >= $created_at){
                        $result[$productkey] = self::$database->getReference('products/'.$productkey)->getValue();
                    }   
                }                                               
            }
            else{
                $result = array();
            }                 
        }  

        $products = (new ProductsController)->getProduct($result);                      
        $data = [
            'title' => "Product",
            "products" => $products,             
        ];
        dd($products);
        return view($this->viewPath."inventory")->with($data);
    }


    
    public function returnReport()
    {                        
        $uid = Session::get('uid');   
        if (Session::get('user_type') == 1) {
            $orders = (new OrdersController)->getOrders();             
        }else{
            $orders = (new OrdersController)->fetchVendorOrders($uid);            
        }        
        
        $data = [
            'title' => "Cancel Orders",
            'orders' => $orders,
            'user_id' => $uid
        ];            
        return view($this->viewPath."return")->with($data);
    }



    public function returnReportFilterByDate(request $request)
    {      
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
                           
                            $order_key = !empty($orderkey) ? $orderkey:''; 
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue(); 
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date
                            if(strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))	                           
                            {
                                $order = new Orders();
                                $order->customer_id = $customer_id;
            
                                //Order key
                                $order->order_key = $order_key; 

                                //Order Info like date, time, status and  amount etc                             
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
                $store_keys = [];            
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
                        
                            $order_key = !empty($orderkey) ? $orderkey:'';                          
                            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();  
                            $store_id = !empty($getOrderDate['store_id']) ? $getOrderDate['store_id']:'';
                            $order_date = !empty($getOrderDate['date']) ? $getOrderDate['date'] : '';

                            //filter by order date, store wise products (related to vendor)
                            if(in_array($store_id, $store_keys) && strtotime($startdate) <= strtotime($order_date) && strtotime($enddate) >= strtotime($order_date))
                            {                                              
                                $order = new Orders();
                                $order->customer_id = $customer_id;
            
                                //Order key
                                $order->order_key = $order_key;  
                                $order->store_id = $store_id; 

                                //Order Info like date, time, status and  amount etc   
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
            'title' => "Cancel Orders",
            'orders' => $orders,
            'user_id' => $uid
        ]; 
        return view($this->viewPath."return")->with($data);
    }


    /*
    public function viewReturn($customer_id, $order_key)
    {                     
        $order = array();             
        $getCartItems = self::$database->getReference('orders'."/".$customer_id."/".$order_key)->getValue();                               
        if(!empty($getCartItems)){    

            $order['customer_id'] = $customer_id;
            $order['order_key'] = $order_key;
            $order['cart_items'] = $getCartItems;

            //Order Info like date, time, status and  amount etc
            $getOrderDate = self::$database->getReference('order_info/'.$order_key)->getValue();                              
            $order['order_date'] = !empty($getOrderDate['date']) ? $getOrderDate['date']:'';
            $order['order_time'] = !empty($getOrderDate['time']) ? $getOrderDate['time']:'';
            $order['order_status'] = !empty($getOrderDate['order_status']) ? $getOrderDate['order_status']:'';                       
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
        return view($this->viewPath."viewReturn")->with($data);
    }
    */
    
}
