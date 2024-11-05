<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use Session;
use Illuminate\Support\Facades\Storage;


class ProductsController extends Controller
{
    public static $reference = "products";
    private $viewPath = "product.";

    /**
     * ProductsController constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Fetch product details   
     */
    public function getProduct($result, $id = false)
    {         
        $stores =  (new StoreController)->getStores();              
        $products = [];
        if(!empty($result) && is_array(array_values($result)[0])) {

            //Set as descending order
            $result = array_reverse($result, true);    
            foreach ($result as $key => $value) {
            
                $product = new Product();
                $product->product_key = $key;
                $product->product_code = !empty($value['product_code']) ? $value['product_code']:'';
                $product->name = !empty($value['name']) ? $value['name']:'';
                $product->description = !empty($value['description']) ? $value['description']:'';
                $product->price = !empty($value['price']) ? $value['price']:'';
                $product->sale_price = !empty($value['sale_price']) ? $value['sale_price']:'';
                $product->vat = !empty($value['vat']) ? $value['vat']:'';                              
                $product->category_id = !empty($value['category_id']) ?$value['category_id']:'';                
                $product->subcategory_id = !empty($value['subcategory_id']) ? $value['subcategory_id']:'';
                $product->sub_subcategory_id = !empty($value['sub_subcategory_id']) ? $value['sub_subcategory_id']:'';
                $product->stock = !empty($value['stock']) ? $value['stock']:'';
                $product->picture = !empty($value['image']) ? $value['image']:'';
                $product->status = !empty($value['status']) ? $value['status']:'';
                $product->created_at = !empty($value['created_at']) ? Carbon::createFromTimestamp($value['created_at']):'';     
                
                //Check category name
                if(!empty($value['sub_subcategory_id']) && $value['sub_subcategory_id'] != 0){
                    $category_name = self::getCategory($value['sub_subcategory_id']);
                }elseif(!empty($value['subcategory_id']) && $value['subcategory_id'] != 0){
                    $category_name = self::getCategory($value['subcategory_id']);
                }else{                    
                    $category_name = !empty($value['category_id']) ? self::getCategory($value['category_id']):'';
                }
                $product->category_name = $category_name;

                // foreach ($stores as $store) {
                //     if(!empty($value['supplier']) && trim($value['supplier']) == trim($store['store_code'])) {
                //         $product->supplier = $value['supplier'];  
                //         $product->store_key = !empty($store['store_key']) ? $store['store_key']:'';
                //         $product->store_name = !empty($store['store_name']) ? $store['store_name']:'';
                //     }
                // }                
                array_push($products, $product);
            }            
            return $products;
        } else {
            $product = new Product();
            $product->product_key = $id;
            $product->product_code = !empty($result['product_code']) ? $result['product_code']:'';
            $product->name = !empty($result['name']) ? $result['name']:'';
            $product->description = !empty($result['description']) ? $result['description']:'';
            $product->price = !empty($result['price']) ? $result['price']:'';
            $product->sale_price = !empty($result['sale_price']) ? $result['sale_price']:'';
            $product->vat = !empty($result['vat']) ? $result['vat']:'';                           
            $product->category_id = !empty($result['category_id']) ? $result['category_id']:'';            
            $product->subcategory_id = !empty($result['subcategory_id']) ? $result['subcategory_id']:'';
            $product->sub_subcategory_id = !empty($result['sub_subcategory_id']) ? $result['sub_subcategory_id']:'';
            $product->stock = !empty($result['stock']) ? $result['stock']:'';
            $product->picture = !empty($result['image']) ? $result['image']:'';
            $product->status = !empty($result['status']) ? $result['status']:'';
            $product->created_at = !empty($result['created_at']) ? Carbon::createFromTimestamp($result['created_at']):'';   
            
            //Check category name
            if(!empty($result['sub_subcategory_id']) && $result['sub_subcategory_id'] != 0){
                $category_name = self::getCategory($result['sub_subcategory_id']);
            }elseif(!empty($result['subcategory_id']) && $result['subcategory_id'] != 0){
                $category_name = self::getCategory($result['subcategory_id']);
            }else{
                $category_name = !empty($result['subcategory_id']) ? self::getCategory($result['category_id']):'';
            }
            $product->category_name = $category_name;     

            // foreach ($stores as $store) {
            //     if(!empty($result['supplier']) && trim($store['store_code']) == trim($result['supplier'])) {
            //         $product->supplier = $result['supplier'];  
            //         $product->store_key = !empty($store['store_key']) ? $store['store_key']:'';
            //         $product->store_name = !empty($store['store_name']) ? $store['store_name']:'';
            //     }
            // } 
            return $product;
        }
    }


    /**
     * Display a listing of the resource.          
     */
    public function index()
    {            
        $uid = Session::get('uid');  
        $categories = (new CategoriesController)->getCategories();            
        if(Session::get('user_type') == 1)  //Admin
        { 
            $result = self::$database->getReference(self::$reference)->getValue();    
        }
        elseif(Session::get('user_type') == 2)   //Vendor
        { 
            //fetch vendor stores
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue(); 
            $store_codes = []; 
            if(!empty($getStores)){       
                foreach($getStores as $storekey => $storeArray){
                    if(empty($storeArray)) {
                        continue;
                    }  
                    $store_codes[] = $storeArray['store_code'];                
                }    
            }           

            //fetch all products
            $result = [];
            $products = self::$database->getReference('product_supplier')->getValue();
            if(!empty($products)){                     
                foreach($products as $pskey => $productArray){               
                    if(empty($productArray)) {
                        continue;
                    }  
                    $store_code = !empty($productArray['supplier']) ? $productArray['supplier']:'';
                    $product_id = !empty($productArray['product_id']) ? $productArray['product_id']:'';

                    //filter store wise products (related to vendor)          
                    if(in_array($store_code, $store_codes)){
                        $result[$product_id] = self::$database->getReference('products/'.$product_id)->getValue();
                    }   
                }
            }
        }   
        else{
            $result = array();
        }          
  
        $products = $this->getProduct($result, false);                      
        $data = [
            'title' => "Product",
            "products" => $products,
            "categories" => $categories,           
        ];        
       
        return view($this->viewPath."index")->with($data);
    }


    /**
     * Add new product.          
     */
    public function create()
    {
        $getStores = self::$database->getReference('stores')->getValue(); 
        $parentcategories = self::$database->getReference('categories')->orderByChild('parent')->equalTo('0')->getValue();  //(new CategoriesController)->getCategories();                                  
        $categories = [];
        if(!empty($parentcategories)){
            foreach ($parentcategories as $key => $value){                
                if(empty($value)) {
                    continue;
                }                                             
                $category = new Category();
                $category->category_id = $key;
                $category->name = $value['name'];
                $category->parent = $value['parent'];       
                $category->created_at = Carbon::createFromTimestamp($value['created_at']);                            
                array_push($categories, $category);
            }
        }         
        $data = [
            'title' => "Product",            
            "categories" => $categories,    
            "stores" => $getStores,        
        ];        
        return view($this->viewPath."create")->with($data);
    }


    /**
     * Store a newly created resource in storage.    
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => "required|alpha_num",
            'name' => "required|string",
            'description' => "required|string",
            'price' => "required|not_in:0|regex:/^[0-9]{1,3}(,[0-9]{9})*(\.[0-9]+)*$/",
            'sale_price' => "required|not_in:0|regex:/^[0-9]{1,3}(,[0-9]{9})*(\.[0-9]+)*$/",
            'vat' => "required",            
            'category' => "required",
            'supplier' => "required",            
            'stock'  => "required|not_in:0",
            //'picture' => "required|image|max:1999"  
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $product_code = $request->input("product_code");           
            $name = $request->input("name");
            $description = $request->input("description");
            $price = $request->input("price");
            $sale_price = $request->input("sale_price");
            $vat = $request->input("vat");           
            $category = $request->input("category");
            $sub_category = !empty($request->input("sub_category")) ? $request->input("sub_category") : '0';
            $sub_subcategory = !empty($request->input("sub_subcategory")) ? $request->input("sub_subcategory") : '0';                   
            $filePathToStore = $this->get_image_path($product_code); 
            
            //Check product exist, if exist then get product key            
            $checkproduct = self::$database->getReference(self::$reference)->orderByChild('product_code')
                            ->equalTo($product_code)->getValue();
            if(!empty($checkproduct))
            {  
                $product_key = key($checkproduct); 
                $error_msg = ["msg" => "Product already exist"];
                $msg = ['error'=> $error_msg];
            }
            else
            {
                $product_key = self::$database
                    ->getReference(self::$reference)
                    ->push([
                        'product_code' => $product_code,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'sale_price' => $sale_price,
                        'vat' => $vat,
                        'supplier' => '',                
                        'category_id' => $category,
                        'subcategory_id' => $sub_category,
                        'sub_subcategory_id' => $sub_subcategory,
                        'stock' => '',                    
                        'image' => $filePathToStore,          //'image' => 'uploads/products/'.$fileNameToStore,
                        'status' => '0',
                        'created_at' => time()                
                    ])->getKey();

                $msg = [
                    'success'=> "Product added successfully",                    
                    'uploaded_image' => '<img src="'.url($filePathToStore).'" width="50px" height="50px"/>'
                ];
            }
            
            $suppliers = $request->input("supplier");                                 
            $newstock = $request->input("stock");       
            if(!empty($suppliers)){
                foreach($suppliers as $supplier){

                    //check products in product_supplier table and get supplier code
                    $checkProductInSupplier = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($supplier)->getValue();														
                    $storeCode = array();
                    if(!empty($checkProductInSupplier)){ 
                        foreach($checkProductInSupplier as $pskey => $psvalue){                                                                                                                      
                            $storeCode[] = !empty($psvalue['supplier']) ? trim($psvalue['supplier']):'';;                                                    
                        }                                               
                    } 
    
                    //check product in products table and get product key
                    $checkproduct = self::$database->getReference(self::$reference)->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();
                    $product_key = '';            
                    if(!empty($checkproduct)){
                        $product_key = key($checkproduct);
                    } 
                                        
                    if(in_array($supplier, $storeCode)){                        
                        //Ignore                                                        
                    }else{
                        $newProduct = self::$database
                            ->getReference('product_supplier')
                            ->push([
                                'product_id' => $product_key,  
                                'product_code' => $product_code,                                
                                'supplier' => $supplier,                                               
                                'stock' => $newstock,                                                                                           
                            ]);   
                    }                                                                   
                }   
            }    
                    
            return response()->json($msg);
        }
    }


    /**
     * Display the specified resource.     
     */
    public function show($id)
    {        
        $uid = Session::get('uid'); 
        $categories = (new CategoriesController)->getCategories();        
        $result = self::$database->getReference(self::$reference."/".$id)->getValue();                
        $products = $this->getProduct($result, $id);               
        $suppliers = [];
        if(!empty($products->product_code)){
            $product_code = trim($products->product_code);
            $checkproduct = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();              
            if(!empty($checkproduct)){               
                foreach($checkproduct as $getkey => $getsupplier){                   
                    $store = new Store();
                    $store->product_code = $getsupplier['product_code'];
                    $store->product_id = $getsupplier['product_id'];                    
                    $store->supplier = $getsupplier['supplier'];
                    $store->stock = $getsupplier['stock'];
                    array_push($suppliers, $store);
                }
                //Set as descending order
                $suppliers = array_reverse($suppliers, true);  
            }
        } 
           
        //fetch vendor stores, if vendor else fetch all store
        if(Session::get('user_type') == 2)  
        { 
            //vendor stores
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue();                      
            foreach($getStores as $storekey => $storeArray){
                if(empty($storeArray)) {
                    continue;
                }  
                $store_codes[] = $storeArray['store_code'];                
            } 
        }
        else
        {
            $getStores = self::$database->getReference('stores')->getValue();                      
            foreach($getStores as $storekey => $storeArray){
                if(empty($storeArray)) {
                    continue;
                }  
                $store_codes[] = $storeArray['store_code'];                
            } 
        }       

        $data = [
            'title' => "Product",
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'store_codes' => $store_codes,
        ];                 
        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit Product info.     
     */
    public function edit($id)
    {                 
        $uid = Session::get('uid');         
        $parentcategories = self::$database->getReference('categories')->orderByChild('parent')->equalTo('0')->getValue();  //(new CategoriesController)->getCategories();                                  
        $categories = [];
        if(!empty($parentcategories)){
            foreach ($parentcategories as $key => $value){                
                if(empty($value)) {
                    continue;
                }                                             
                $category = new Category();
                $category->category_id = $key;
                $category->name = $value['name'];
                $category->parent = $value['parent'];       
                $category->created_at = Carbon::createFromTimestamp($value['created_at']);                            
                array_push($categories, $category);
            }
        }              
        $subcategories = (new CategoriesController)->getCategories();                
        $result = self::$database->getReference(self::$reference."/".$id)->getValue();                
        $products = $this->getProduct($result, $id); 
    
        //fetch product suppliers with product quentity
        $suppliers = [];        
        if(!empty($products->product_code)){
            $product_code = trim($products->product_code);
            $checkproduct = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();                                                      
            if(!empty($checkproduct)){               
                foreach($checkproduct as $getkey => $getsupplier){                   
                    $store = new Store();
                    $store->pskey = $getkey;
                    $store->product_code = $getsupplier['product_code'];
                    $store->product_id = $getsupplier['product_id'];                    
                    $store->supplier = $getsupplier['supplier'];
                    $store->stock = $getsupplier['stock'];
                    array_push($suppliers, $store);
                }
                //Set as descending order
                $suppliers = array_reverse($suppliers, true);  
            }
        }  
        
        //fetch vendor stores, if vendor else fetch all store
        if(Session::get('user_type') == 2)  
        { 
            //vendor stores
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue();                      
            if(!empty($getStores)){
                foreach($getStores as $storekey => $storeArray){
                    if(empty($storeArray)) {
                        continue;
                    }  
                    $store_codes[] = $storeArray['store_code'];                
                } 
            }
        }
        else
        {
            $getStores = self::$database->getReference('stores')->getValue(); 
            if(!empty($getStores)){                     
                foreach($getStores as $storekey => $storeArray){
                    if(empty($storeArray)) {
                        continue;
                    }  
                    $store_codes[] = $storeArray['store_code'];                
                } 
            }
        }

        $data = [
            'title' => "Product",
            'products' => $products,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'suppliers' => $suppliers,
            'stores' => $getStores,
            'store_codes' => $store_codes,
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
            'product_code' => "required|alpha_num",
            'name' => "required|string",
            'description' => "required|string",
            'price' => "required|not_in:0|regex:/^[0-9]{1,3}(,[0-9]{9})*(\.[0-9]+)*$/",
            'sale_price' => "required|not_in:0|regex:/^[0-9]{1,3}(,[0-9]{9})*(\.[0-9]+)*$/",
            'vat' => "required",           
            'category' => "required",
            //'supplier' => "required",            
            //'stock'  => "required" 
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {            
            $product_code = $request->input("product_code");
            $name = $request->input("name");
            $description = $request->input("description");
            $price = $request->input("price");
            $sale_price = $request->input("sale_price");
            $vat = $request->input("vat");            
            $category = $request->input("category");
            $sub_category = !empty($request->input("sub_category")) ? $request->input("sub_category") : '0';
            $sub_subcategory = !empty($request->input("sub_subcategory")) ? $request->input("sub_subcategory") : '0';            
            $status = $request->input("status");
           
            $image = $request->input("old_picture");
            //Remove previous pic
            if(file_exists($image)) 
            {
                unlink($image);  //echo "File Successfully Delete.";                 
            }
            $filePathToStore = $this->get_image_path($product_code); 

            //Update record
            $updateProduct = self::$database
                ->getReference(self::$reference."/".$id)
                ->update([
                    'product_code' => $product_code,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'sale_price' => $sale_price,
                    'vat' => $vat,
                    'supplier' => '',                
                    'category_id' => $category,
                    'subcategory_id' => $sub_category,
                    'sub_subcategory_id' => $sub_subcategory,
                    'stock' => '',
                    'image' => $filePathToStore,
                    'status' => $status,
                    'created_at' => time()                
                ]);

            return response()->json(['success'=> "Product updated successfully"]);
        }
    }


    /**
     * Product quentiy update.   
     */
    public function updateQuentity(Request $request)
    {         
        $validator = Validator::make($request->all(), [                       
            'stock'  => "required" 
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {           
            $stock = trim($request->input("stock"));         
            $pskey = trim($request->input("pskey"));

            //Update record            
            $updateProduct = self::$database->getReference("product_supplier/".$pskey)->getChild('stock')->getValue();	        
            if(trim($updateProduct) != ''){
                self::$database->getReference("product_supplier/".$pskey)->getChild('stock')->set($stock);
            }
                
            return response()->json(['success'=> "Product quentity updated successfully"]);
        }
    }


    /**
     * Remove the specified resource from storage.   
     */
    public function destroy($id)
    {      
        $uid = Session::get('uid');      
        $store_codes = [];  
        //if vendor, then fetch vendor's stores       
        /*if(Session::get('user_type') == 2)  
        {             
            $getStores = self::$database->getReference('stores')->orderByChild("vendor_id")->equalTo($uid)->getValue();                      
            foreach($getStores as $storekey => $storeArray){
                if(empty($storeArray)) {
                    continue;
                }  
                $store_codes[] = $storeArray['store_code'];                
            } 
        }*/        

        $data = self::$database->getReference(self::$reference."/".$id)->getValue();                  
        if(!empty($data['product_code'])){
            $product_code = $data['product_code'];
            $checkProductStock = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();
            if(!empty($checkProductStock)){
                foreach($checkProductStock as $pskey => $psvalue){                    
                    //if vendor, then remove only matched supplier products
                    /*if(in_array($psvalue['supplier'], $store_codes)){                        
                        self::$database->getReference("product_supplier/".$pskey)->remove();                       
                    }*/

                    //if admin, then remove all based on product key
                    if(Session::get('user_type') == 1){                                                                            
                        self::$database->getReference("product_supplier/".$pskey)->remove();
                    }                                  
                }
            } 

            //if admin, then remove image 
            if(Session::get('user_type') == 1)  
            {     
                if(!empty($data['image'])){                                                          
                    if(file_exists(public_path($data['image']))){
                        unlink(public_path($data['image']));
                    }
                }
                self::$database->getReference(self::$reference."/".$id)->remove();
            }
        }                        
        return back();
    }


    /**
     * Get category name   
     */
    public static function getCategory($category_id){        
        $result = self::$database->getReference(CategoriesController::$reference."/".$category_id)->getValue();  
        $category = !empty($result['name']) ? $result['name'] :'';
        return $category;        
    }

    
    /**
     * Get Product image name   
     */
    public function getProductInfo($id)
    {
        $result = self::$database->getReference(self::$reference."/".$id)->getValue(); 
        $image = !empty($result['image']) ? $result['image'] :'';           
        return $image;
    }


    /**
     * Product approve / reject   
     */
    public function approve(request $request)
    {
        $action = $request->input("action");
        if($action == 'approve'){
            $status = 1;
            $message = 'Product Approved';
            $button = '<button class="btn btn-success btn-sm">Approved</button>'; 
        }else{
            $status = 2;
            $message = 'Product Rejected';
            $button = '<button class="btn btn-warning btn-sm">Rejected</button>'; 
        }
        $product_id = trim($request->input("product_id"));        

        $checkstatus = self::$database->getReference(self::$reference."/".$product_id)->getChild('status')->getValue();	        
		if(trim($checkstatus) != ''){
			self::$database->getReference(self::$reference."/".$product_id)->getChild('status')->set($status);
        }     
            
        return response()->json(['success'=> $message,'arbtn'=> $button]);        
    }


    /**
     * Import excel and csv    
     */
    public function importExport()
    {
        return view($this->viewPath.'importExport');
    }


    /**
     * Download excel, csv file (not used)    
     */
    public function downloadExcel($type)
    {
        $data = Item::get()->toArray();
            
        return Excel::create('export_excel', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }


    /**
     * Import excel and csv for upload products 
     */
    public function importExcel(Request $request)
    {                
        $validate = $request->validate([
            'product_file' => 'required',
            'quentity_file' => 'required'
        ]);

        $path = $request->file('product_file')->getRealPath();
        $data = Excel::load($path)->get();        
        if($data->count()){
            foreach ($data as $key => $value) {     
                
                $product_code = !empty($value->km10) ? $value->km10 :'';                
                if(!empty($product_code)){

                    $filePathToStore = $this->get_image_path($product_code); 

                    $checkproduct = self::$database->getReference(self::$reference)->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();                          
                    if(!empty($checkproduct)){
                        $product_key =  key($checkproduct);

                        //Update record
                        $updateProduct = self::$database
                            ->getReference(self::$reference."/".$product_key)
                            ->update([
                                'product_code' => $product_code,
                                'name' => !empty($value->kean) ? $value->kean :'',   
                                'description' => !empty($value->kdes) ? $value->kdes :'',
                                'price' => !empty($value->prezzo_bd) ? $value->prezzo_bd :'',
                                'sale_price' => !empty($value->e_prezzo_farmacia) ? $value->e_prezzo_farmacia :'',
                                'vat' => !empty($value->iva) ? $value->iva :'',
                                'supplier' => '', //!empty($value->ditta) ? $value->ditta :'',               
                                'category_id' => !empty($value->degrassi) ? str_replace("."," ", $value->degrassi) :'',
                                'subcategory_id' => !empty($value->sud_merc) ? str_replace("."," ", $value->sud_merc) : '0',
                                'sub_subcategory_id' => !empty($value->sub_sud_merc) ? str_replace("."," ", $value->sub_sud_merc) : '0',
                                'stock' => '', //!empty($value->giac_farm) ? $value->giac_farm :'',
                                'image' => $filePathToStore,
                                'status' => '0',
                                'created_at' => time()                
                            ]);                           
                    }else{
                        // Add new product
                        $newProduct = self::$database
                            ->getReference(self::$reference)
                            ->push([
                                'product_code' => !empty($value->km10) ? $value->km10 :'',
                                'name' => !empty($value->kean) ? $value->kean :'',   
                                'description' => !empty($value->kdes) ? $value->kdes :'',
                                'price' => !empty($value->prezzo_bd) ? $value->prezzo_bd :'',
                                'sale_price' => !empty($value->e_prezzo_farmacia) ? $value->e_prezzo_farmacia :'',
                                'vat' => !empty($value->iva) ? $value->iva :'',
                                'supplier' => '', //!empty($value->ditta) ? $value->ditta :'',               
                                'category_id' => !empty($value->degrassi) ? str_replace("."," ", $value->degrassi) :'',
                                'subcategory_id' => !empty($value->sud_merc) ? str_replace("."," ", $value->sud_merc) : '0',
                                'sub_subcategory_id' => !empty($value->sub_sud_merc) ? str_replace("."," ", $value->sub_sud_merc) : '0',
                                'stock' => '', //!empty($value->giac_farm) ? $value->giac_farm :'',
                                'image' => $filePathToStore,
                                'status' => '0',
                                'created_at' => time()                           
                            ]);
                    }   
                }else{
                    continue;
                }                                
            }            
        }

        //Product supplier and quentity update
        $path = $request->file('quentity_file')->getRealPath();
        $data1 = Excel::load($path)->get();        
        if($data1->count()){            
            foreach ($data1 as $key => $value) {     

                $product_code = !empty($value->km10) ? $value->km10 :'';                
                $supplier = !empty($value->ditta) ? $value->ditta :'';
                $newstock = !empty($value->giac_farm) ? $value->giac_farm :''; 

                if(!empty($product_code)){                                         

                    //check products in product_supplier table and get supplier code
                    $checkProductInSupplier = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();														
                    $storeCode = array();
                    if(!empty($checkProductInSupplier)){ 
                        foreach($checkProductInSupplier as $pskey => $psvalue){                                                                                                                      
                            $storeCode[] = !empty($psvalue['supplier']) ? trim($psvalue['supplier']):'';;                                                    
                        }                                               
                    } 

                    //check product in products table and get product key
                    $checkproduct = self::$database->getReference(self::$reference)->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();
                    $product_key = '';            
                    if(!empty($checkproduct)){
                        $product_key = key($checkproduct);
                    } 
                                        
                    if(in_array($supplier, $storeCode)){                        
                        //Ignore                                                        
                    }else{
                        $newProduct = self::$database
                            ->getReference('product_supplier')
                            ->push([
                                'product_id' => $product_key,  
                                'product_code' => $product_code,                                
                                'supplier' => $supplier,                                               
                                'stock' => $newstock,                                                                                           
                            ]);   
                    }
                }                                                                             
            }              
        }
        return back()->with('success', 'Your files Record has been successfully added');             
    }


    /**
     * Import excel and csv for asign product with mulitple store with update quentity  
     */
    public function assignProduct(Request $request)
    {        
        $request->validate([
            'import_quentity_file' => 'required'
        ]);
 
        $path = $request->file('import_quentity_file')->getRealPath();
        $data = Excel::load($path)->get();
        
        if($data->count()){            
            foreach ($data as $key => $value) {     

                $product_code = !empty($value->km10) ? $value->km10 :'';                
                $supplier = !empty($value->ditta) ? $value->ditta :'';
                $newstock = !empty($value->giac_farm) ? $value->giac_farm :''; 

                if(!empty($product_code)){                                         

                    //check products in product_supplier table and get supplier code
                    $checkProductInSupplier = self::$database->getReference('product_supplier')->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();														
                    $storeCode = array();
                    if(!empty($checkProductInSupplier)){ 
                        foreach($checkProductInSupplier as $pskey => $psvalue){                                                                                                                      
                            $storeCode[] = !empty($psvalue['supplier']) ? trim($psvalue['supplier']):'';;                                                    
                        }                                               
                    } 

                    //check product in products table and get product key
                    $checkproduct = self::$database->getReference(self::$reference)->orderByChild('product_code')
                                ->equalTo($product_code)->getValue();
                    $product_key = '';            
                    if(!empty($checkproduct)){
                        $product_key = key($checkproduct);
                    } 
                                        
                    if(in_array($supplier, $storeCode)){                        
                        //Ignore                                                        
                    }else{
                        $newProduct = self::$database
                            ->getReference('product_supplier')
                            ->push([
                                'product_id' => $product_key,  
                                'product_code' => $product_code,                                
                                'supplier' => $supplier,                                               
                                'stock' => $newstock,                                                                                           
                            ]);   
                    }
                }                                                                             
            }              
        }

        return back()->with('success', 'Your files Record has been successfully updated');
    }


    /**
     * Base64 to image convert
     */
    public function base64_string_to_image($img_base64_string, $product_code)
    {        
        $image_64 = 'data:image/jpg;base64,'.$img_base64_string;        
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf        
        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
        
        // find substring fro replace here eg: data:image/jpg;base64,
        $image = str_replace($replace, '', $image_64); 
        $image = str_replace(' ', '+', $image); 
        $imageName = $product_code.'.'.$extension;
        
        //Storage path => public_path('uploads/products')
        Storage::disk('products')->put($imageName, base64_decode($image));
        return $imageName;
    }

    
    /**
     * Get Images path from server
     */
    public function get_image_path($product_code)
    {   
        //$product_code = '000367058';
        $current_date = date('dmY');
        $reverse_current_date = date('Ymd'); 
        $fulcri_userId = 21;
        $fulcri_username = 'medimarket';
        $fulcri_password = 'Eo43-Xvb0+3Fx';
        $password_hashata = hash('sha512', $fulcri_password);                  
        
        /***************** According to API - All data should be on one line without space ****************
            Current date (ddMMyyy) + Password hashata (password in the customer&#39;s possession, encrypted with code
            HASH512) + Current date (ddMMyyy) + Username + Reverse current date (yyyMMdd) + User ID   
        */  
        $final = $current_date.$password_hashata.$current_date.$fulcri_username.$reverse_current_date.$fulcri_userId; 

        /* Hash this data */
        $final_hashdata = hash('sha512', $final);
  
        /* Using Curl with get method */
        $endpoint = "http://pmanagerit.fulcri.it/api/v1/imagephf/getImagePhf";
        $client = new \GuzzleHttp\Client();
       
        $response = $client->request('GET', $endpoint, ['query' => [
            'UserID' => $fulcri_userId, 
            'Username' => $fulcri_username,
            'Password' => $fulcri_password,
            'Code' => $product_code,
            'Width' => 1200,
            'Hash' => $final_hashdata,     
        ]]);

        $statusCode = $response->getStatusCode();
        //$content = $response->getBody();
        
        $imagefilePath = '';
        if($statusCode == 200){
            //when server returns json
            $content = json_decode($response->getBody(), true);
            $img_base64_string = $content['content']['img'];

            //base64 to image convert and store in products folder 
            $image = !empty($img_base64_string) ? $this->base64_string_to_image($img_base64_string, $product_code):'';            
            if(!empty($image)){
                $imagefilePath = 'uploads/products/'.$image;
            }
        }         
        return $imagefilePath;   
    }


    /**
     * Import Images -- currently not using this function
     */
    public function importImage(request $request)
    {           
        $this->validate($request, [
            'filenames' => 'required',
            'filenames.*' => 'mimes:jpeg,png,jpg,gif,svg'
        ]); 

        if($request->hasfile('filenames'))
        {
            foreach($request->file('filenames') as $file)
            {            
                $name = $file->getClientOriginalName();
                //$file->move(storage_path() . '/products/', $name);
                $file->move(public_path('uploads/products'), $name);
                $data[] = $name;                
            }
        }
        return back()->with('success', 'Your files has been successfully Uploaded');  
    }

}
