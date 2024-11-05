<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File; 
use Session;

class StoreController extends Controller
{
    public static $reference = "stores";
    private $viewPath = "store.";

     /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Display a listing of Stores.    
     */
    public function index()
    {                                                       
        $allstores = self::getStores();
        $data = [
            'title' => "Stores",
            'stores' => $allstores,
        ];        
        return view($this->viewPath."index")->with($data);
    }    
   

    /**
     * Add new store.          
     */
    public function create()
    {                    
        $users = self::$database->getReference('users')->orderByChild('user_type')->equalTo('2')->getValue();  
        $data = [
            'title' => "Store",
            'users' => $users,
        ];
        //dd($data);
        return view($this->viewPath."create")->with($data);
    }


    /**
     * Store a newly created resource in storage.    
     */
    public function store(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'store_code' => "required|alpha_num",
            'store_name' => "required|string",
            'address' => "required|string",
            'store_type' => "required",
            'vendor'  => "required",
            'phone' => "required",   
            'latitude' => "required", 
            'longitude' => "required",          
            'picture' => "required|image|max:1999",              
            'option' => 'required|min:1'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $store_code = ucwords($request->input("store_code"));
            $store_name = $request->input("store_name");           
            $store_type = $request->input("store_type");
            $vendor_id = $request->input("vendor"); 
            $paylink = $request->input("payment_link");                  
            $phone = $request->input("phone");
            $phone = str_pad($phone, 11, '0', STR_PAD_LEFT);  
            $address = $request->input("address");    

            if(in_array('deliverto', $request->get('option'))){                
                $deliverto = 1;
            }else{               
                $deliverto = 0;
            }

            if(in_array('self_pickup', $request->get('option'))){               
                $self_pickup = 1;
            }else{
                $self_pickup = 0;
            }
            
            $fileNameToStore = "";
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                $fileNameToStore = $store_code.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/stores'), $fileNameToStore); 
            }
        
            //Fetch IP based latitude and longitude
            //$clientIP = request()->ip();  //'122.173.126.120'; 
            //$new_arr= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientIP));
            //$latitude = !empty($new_arr['geoplugin_latitude']) ? $new_arr['geoplugin_latitude']:'';
            //$longitude = !empty($new_arr['geoplugin_longitude']) ? $new_arr['geoplugin_longitude']:'';
            $latitude = $request->input("latitude"); 
            $longitude = $request->input("longitude");
                            
            $checkstore = self::$database->getReference(self::$reference)->orderByChild('store_code')
                            ->equalTo($store_code)->getValue();
            if(!empty($checkstore))
            {  
                $store_key = key($checkstore); 
                $error_msg = ["msg" => "Store already exist"];
                $msg = ['error'=> $error_msg];
            }
            else
            {
                $newPost = self::$database
                        ->getReference(self::$reference)
                        ->push([
                            'vendor_id' => $vendor_id,
                            'store_code' => $store_code,
                            'store_name' => $store_name,
                            'store_type' => $store_type,                    
                            'image' => 'uploads/stores/'.$fileNameToStore,                    
                            'address' => $address,
                            'phone' => $phone,
                            'latitude' => $latitude,
                            'logitude' => $longitude,
                            'deliverto' => $deliverto,
                            'selfpickup' => $self_pickup,
                            'paylink' => $paylink,
                            'rating' => '',
                            'created_at' => time()   
                        ]);

                $msg = [
                    'success'=> "Store created successfully",                    
                    'uploaded_image' => '<img src="'.url('uploads/stores/'.$fileNameToStore).'" width="50px" height="50px"/>'
                ];
            }
                                
            return response()->json($msg);
        }
    }


/**
     * Display the specified resource.     
     */
    public function show($id)
    {        
        $storeData = self::$database->getReference(self::$reference."/".$id)->getValue(); 
        $store = [];
        if(!empty($storeData)){   

            //Store info
            $store['store_code'] = $storeData['store_code'];                
            $store['store_name'] = $storeData['store_name'];                     
            $store['store_type'] = $storeData['store_type'];   
            $store['address'] = $storeData['address'];    
            $store['phone'] = $storeData['phone'];  
            $store['rating'] = $storeData['rating'];   
            $store['image'] = $storeData['image'];  
            $store['latitude'] = $storeData['latitude'];   
            $store['logitude'] = $storeData['logitude'];    
            $store['paylink'] = $storeData['paylink'];      
            $store['created_at'] = Carbon::createFromTimestamp($storeData['created_at']); 

            //Vendor info
            $store['vendor_id'] = $storeData['vendor_id'];
            $vendorData = self::$database->getReference('users'."/".$storeData['vendor_id'])->getValue(); 
            if(!empty($vendorData)){
                $store['vendor_email'] = $vendorData['email'];
                $store['vendor_name'] = $vendorData['name'];
                $store['vendor_pic'] = $vendorData['picture'];                
            }
        }          
        $storeinfo = json_decode(json_encode($store), FALSE);                    
        $data = [
            'title' => "Store",
            'store' =>  $storeinfo,           
            'id' => $id
        ];    

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit Product info.     
     */
    public function edit($id)
    {                         
        $users = self::$database->getReference('users')->orderByChild('user_type')->equalTo('2')->getValue();               
        $stores = self::$database->getReference(self::$reference."/".$id)->getValue();                                
        $data = [
            'title' => "Store",
            'stores' => $stores, 
            'users' => $users,           
            'id' => $id
        ];                                  
        return view($this->viewPath."edit")->with($data);
    }


    /**
     * Update the specified resource in storage.   
     */
    public function update(Request $request, $id)
    {           
        $validator = Validator::make($request->all(), [
            'store_code' => "required|alpha_num",
            'store_name' => "required|string",
            'address' => "required|string",
            'store_type' => "required",
            'vendor'  => "required",
            'phone' => "required",
            'latitude' => "required", 
            'longitude' => "required",
            'option' => 'required|min:1'                         
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $store_code = ucwords($request->input("store_code"));
            $store_name = $request->input("store_name");           
            $store_type = $request->input("store_type");
            $vendor_id = $request->input("vendor"); 
            $paylink = $request->input("payment_link");                  
            $phone = $request->input("phone");
            $phone = str_pad($phone, 11, '0', STR_PAD_LEFT);  
            $address = $request->input("address");    

            if(in_array('deliverto', $request->get('option'))){                
                $deliverto = 1;
            }else{               
                $deliverto = 0;
            }

            if(in_array('self_pickup', $request->get('option'))){               
                $self_pickup = 1;
            }else{
                $self_pickup = 0;
            }

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
                $fileNameToStore = $store_code.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/stores'), $fileNameToStore); 
                $image = 'uploads/stores/'.$fileNameToStore;               
            }

            //Fetch IP based latitude and longitude
            //$clientIP = request()->ip();  //'122.173.126.120'; 
            //$new_arr= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientIP));
            //$latitude = !empty($new_arr['geoplugin_latitude']) ? $new_arr['geoplugin_latitude']:'';
            //$longitude = !empty($new_arr['geoplugin_longitude']) ? $new_arr['geoplugin_longitude']:'';
            $latitude = $request->input("latitude"); 
            $longitude = $request->input("longitude"); 

            $updateProduct = self::$database
                ->getReference(self::$reference."/".$id)
                ->update([
                    'vendor_id' => $vendor_id,
                    'store_code' => $store_code,
                    'store_name' => $store_name,
                    'store_type' => $store_type,                    
                    'image' => $image,                    
                    'address' => $address,
                    'phone' => $phone,
                    'latitude' => $latitude,
                    'logitude' => $longitude,
                    'deliverto' => $deliverto,
                    'selfpickup' => $self_pickup,
                    'paylink' => $paylink,
                    'rating' => '',
                    'created_at' => time()                 
                ]);

            return response()->json(['success'=> "Store Info updated successfully"]);
        }
    }


    /**
     * Remove the specified resource from storage.   
     */
    public function destroy($id)
    {
        $data = self::$database->getReference(self::$reference."/".$id)->getValue();
        if(!empty($data['image']) && file_exists(public_path($data['image']))){
            unlink(public_path($data['image']));
        }        
        self::$database->getReference(self::$reference."/".$id)->remove(); 
        session()->flash("success", "Store Deleted successfully");       
        return redirect()->back();  
    }


     /**
     * Fetch all categories llst
     */
    public function getStores() : array {

        if(Session::get('user_type') == 2){
            $vendor_id = Session::get('uid');
            $fetchStores = self::$database->getReference(self::$reference)->orderByChild('vendor_id')->equalTo($vendor_id)->getValue();
        }else{
            $fetchStores = self::$database->getReference(self::$reference)->getValue();
        }

        $stores = [];
        if(!empty($fetchStores)){

            //Set as descending order 
            $fetchStores = array_reverse($fetchStores, true);  

            foreach ($fetchStores as $key => $value){                
                if(empty($value)) {
                    continue;
                }                   
                $store_key = trim(trim('"'.$key.'"','"'));   
                $store = new Store();
                $store->store_key = $store_key;
                $store->vendor_id = $value['vendor_id']; 
                $store->store_code = $value['store_code'];                
                $store->store_name = $value['store_name'];                     
                $store->store_type = $value['store_type'];   
                $store->address = $value['address'];    
                $store->phone = $value['phone'];  
                $store->rating = $value['rating'];   
                $store->image = $value['image'];  
                $store->latitude = !empty($value['latitude']) ? $value['latitude']:'';   
                $store->logitude = !empty($value['logitude']) ? $value['logitude']:'';                   
                $store->created_at = Carbon::createFromTimestamp($value['created_at']);                            
                array_push($stores, $store);
            }                   
        }        
        return $stores;
    }
    
    
    public static function getStoreName($store_code)
    {
        $storeinfo = self::$database->getReference('stores')->orderByChild('store_code')
                    ->equalTo($store_code)->getValue();	
        $store_key = !empty($storeinfo) ? key($storeinfo):''; 
        if(!empty($store_key)){
            $storeInfo = self::$database->getReference('stores/'.$store_key)->getValue();                  
            return $storeInfo; 
        }                
    }

}
