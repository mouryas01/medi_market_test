<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Orders;
use Session;
use Hash;
use App\Menu;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('firebase');
        self::$database = self::firebaseDatabaseInstance();
    }


    public static function get_userinfo()
    {
        $user_key = Session::get('uid');
        $userinfo = self::$database->getReference('users/'.$user_key)->getValue();
        return $userinfo;
    }
    

    /**
     * Show the vendor dashboard.
     *
     */
    public function index()
    {     
        $uid = Session::get('uid'); 
        //fetch all orders (vendor wise)                        
        
        $salestotal = 0;
        $cancelorders = 0;
        $totalorders = 0;
        $getallOrders = (new OrdersController)->fetchVendorOrders($uid);
        
        if(!empty($getallOrders)){
            foreach($getallOrders as $skey => $salesdata){
                //filter total sales 
                if(!empty($salesdata['order_status']) && $salesdata['order_status'] == 4)   /* 4 = Delivered */
                {     
                    $salestotal += $salesdata['total_amount'];
                }    
    
                //filter total cancel orders
                if(!empty($salesdata['order_status']) && $salesdata['order_status'] == 3)   /* 3 = Cancelled */
                {     
                    $cancelorders += 1;
                } 
    
                //filter total Orders
                if(!empty($salesdata['order_key'])){
                    $totalorders += 1;
                }
            }
        }

        //fetch all products (vendor wise)  
        $totalproducts = 0;
        $prodresult = (new ReportsController)->fetchVendorProducts($uid);            
        $products = (new ProductsController)->getProduct($prodresult);         
        if(!empty($products)){
            foreach($products as $pkey => $product){           
                //filter total products
                if(!empty($product['product_key'])) 
                {     
                    $totalproducts += 1;
                } 
            }
        }
        
        $data = [
            'title' => "Dashboard",  
            'total_sales' => $salestotal,
            'cancel_orders' => $cancelorders,  
            'total_products' => $totalproducts,   
            'total_orders' => $totalorders,          
        ];          
       
        return view('home')->with($data); 

    }


    /**
     * Show the Admin dashboard.
     *     
     */
    public function adminHome()
    {                    
        //fetch all orders    
        $getallOrders = (new OrdersController)->getOrders();     
        $salestotal = 0;
        $cancelorders = 0;
        foreach($getallOrders as $key => $salesdata){

            //filter total sales 
            if(!empty($salesdata['order_status']) && $salesdata['order_status'] == 4)   /* 4 = Delivered */
            {     
                $salestotal += $salesdata['total_amount'];
            }    

            //filter total cancel orders
            if(!empty($salesdata['order_status']) && $salesdata['order_status'] == 3)   /* 3 = Cancelled */
            {     
                $cancelorders += 1;
            } 
        }

        $category = self::$database->getReference(CategoriesController::$reference)->getSnapshot()->numChildren();
        $product = self::$database->getReference(ProductsController::$reference)->getSnapshot()->numChildren();
        $users = self::$database->getReference('fd_users')->getSnapshot()->numChildren();
        $vendors = self::$database->getReference(VendorController::$reference)->orderByChild('user_type')->equalTo('2')->getSnapshot()->numChildren();
        $orders = self::$database->getReference(OrdersController::$reference)->getSnapshot()->numChildren();

        $data = [
            'title' => "Dashboard",
            'product' => $product,
            'vendors' => $vendors,
            'users' => $users,            
            'total_sales' => $salestotal,
            'cancel_orders' => $cancelorders,           
        ];        

        return view('adminHome')->with($data);    

    }



    /**
     * Show the profile page
     *
     */
    public function profile()
    {        
        $user_key = Session::get('uid');
        $userInfo = self::$database->getReference('users/'.$user_key)->getValue();
        $storeInfo = self::$database->getReference('stores')->orderByChild('vendor_id')->equalTo($user_key)->getValue();
    
        if($userInfo['user_type'] == 1){
            $user_type = 'Admin';
        }elseif($userInfo['user_type'] == 2){
            $user_type = 'Vendor';
        }
        $profile = new User();
        $profile->user_key = $user_key;
        $profile->name = $userInfo['name'];
        $profile->email = $userInfo['email'];  
        $profile->picture = $userInfo['picture'];   
        $profile->user_type = $user_type;       
        $profile->created_at = Carbon::createFromTimestamp($userInfo['created_at']);   

        $data = [
            'title' => "Profile",
            'userinfo' => $profile
            //'storeInfo' => $storeInfo
        ];
        return view('profile')->with($data);
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editProfile($id)
    {        
        $user_key = $id;
        $userInfo = self::$database->getReference('users/'.$user_key)->getValue();
        $storeInfo = self::$database->getReference('stores')->orderByChild('vendor_id')->equalTo($user_key)->getValue();
    
        if($userInfo['user_type'] == 1){
            $user_type = 'Admin';
        }elseif($userInfo['user_type'] == 2){
            $user_type = 'Vendor';
        }
        $profile = new User();
        $profile->user_key = $user_key;
        $profile->name = $userInfo['name'];
        $profile->email = $userInfo['email'];  
        $profile->picture = $userInfo['picture'];   
        $profile->user_type = $user_type;       
        $profile->created_at = Carbon::createFromTimestamp($userInfo['created_at']);   

        $data = [
            'title' => "Edit Profile",
            'userinfo' => $profile,
            'edit' => 'editProfile',        
        ];
        return view('profile')->with($data);
    }



    public function updateProfile(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'name' => "required",           
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $id = $request->input("user_key");   
            $name = $request->input("name");   
            $email = $request->input("email");         
            $image = $request->input("old_picture"); 
            $fileNameToStore = "";    
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {

                //Remove previous pic
                if(file_exists($image)) 
                {
                    unlink($image);
                    //echo "File Successfully Delete."; 
                }

                //Upload new pic
                $picName = trim(str_replace(' ', '', $name));
                $fileNameToStore = $picName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/profile'), $fileNameToStore); 
                $image = 'uploads/profile/'.$fileNameToStore;   
                $updateImage = self::$database->getReference("users/".$id."/picture")->set($image);               
            }

            $updateName = self::$database->getReference("users/".$id."/name")->set($name);  
            if(!empty($email)){
                $updateName = self::$database->getReference("users/".$id."/email")->set($email);  
            }                      

            return response()->json(['success'=> "Profile updated successfully"]);
        }
    }



    public function change_password(Request $request)
    {        
        $validator = Validator::make($request->all(), [                
            'password' => 'required|min:6|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
        
        if($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {            
            $uid = Session::get('uid');
            $users = self::$database->getReference('users/'.$uid)->getValue();                           
            $password = trim($request->input('password'));    

            if(!empty($users)){                
                $email = !empty($users['email']) ? $users['email']:'';
                $password =  Hash::make($password);                
                $changepassword = self::$database->getReference('users/'.$uid)->getChild('password')->set($password);
                            
                return response()->json(['success'=> "Your password has been changed successfully"]); 			                            
            }
        }
    }


    public function menus()
    {
        $menuData = self::$database->getReference('menus')->getValue();
        $menus = [];
        if(!empty($menuData)){
            foreach ($menuData as $key => $value){                
                if(empty($value)) {
                    continue;
                }                                             
                $menu = new Menu();
                $menu->menu_key = $key;
                $menu->id = $value['id'];
                $menu->menu = $value['menu'];
                $menu->status = $value['status'];                                              
                array_push($menus, $menu);
            }
        }               		
        $data = [
            'title' => "Manage Menu Permission",
            'menus' => $menus,                  
        ];
        return view('menu')->with($data);
    }


    public function setpermission(Request $request)
    {
        $message = '';
        $rules = array(
            'ids' => 'required',                      
        );    
        $messages = array(
            'ids.required' => 'Please select at least one menu',           
        );        
        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()) 
        {
            //return back()->withInput()->withErrors($validator); 
            return response()->json(['error'=> $validator->errors()->all()]);     
        }
        else
        {   
            $ids = $request->input('ids');       
            $menuIds = explode(",",$ids);
            $action = $request->input('action');
            
            if(!empty($menuIds)){
                foreach($menuIds as $mmenu_key){                 
                    $checkstatus = self::$database->getReference('menus/'.$mmenu_key)->getValue();
                    if($checkstatus){
                        if($action == 'activate'){
                            self::$database->getReference('menus/'.$mmenu_key)->getChild('status')->set(1); 
                        }
                        // status : 0 - deactivate
                        if($action == 'deactivate'){                        
                            self::$database->getReference('menus/'.$mmenu_key)->getChild('status')->set(0);  
                        }                        
                    } 
                }               
            }
            if($action == 'activate'){            
                $message = 'Menu Activated Successfully';
            }elseif($action == 'deactivate'){
                $message = 'Menu De-Activated Successfully';
            }else{
                $message = '';
            }
        }
        return response()->json(['status'=>true, 'message'=>$message]);
    }


}
