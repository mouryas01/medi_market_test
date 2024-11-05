<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Session;

class CategoriesController extends Controller
{
    public static $reference = "categories";
    private $viewPath = "category.";

    /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Display a listing of Categories.    
     */
    public function index()
    {                  
        $allcategories = self::getCategories();                     
        $data = [
            'title' => "Categories",
            'categories' => $allcategories,  
            //'categoryTree' => $this->categoryTree(),            
        ];
                
        return view($this->viewPath."index")->with($data);
    }    


    public function category_table(){

        $allcategories = self::getCategories();            
        $table_view = '';
        if(is_array($allcategories) && count($allcategories) > 0) {  
            $sn = 1;                                    
            foreach($allcategories as $key => $value){                                
                $table_view .= '<tr>';   //<td>'. $sn++ .'</td>
                $table_view .= '<td><a href="'.route('categories.show', $value->category_id) .'">';
                $table_view .= '<img src="'.url($value->image) .'" height="50px" width="50px"></a></td>';
                $table_view .= '<td><a href="'. route('categories.show', $value->category_id) .'">'. $value->name .'</a></td>';
                $table_view .= '<td>'. Self::getCategory($value->parent) .'</td>';
                $table_view .= '<td>'. $value->no_of_product .'</td>';
                $table_view .= '<td><a href="'. route('categories.edit', $value->category_id) .'" class="btn btn-dark btn-sm">Edit</a></td></tr>'; 
            }            
        }              
        return $table_view;       
    }


    public function create()
    {
        $allcategories = self::getCategories();                     
        $data = [
            'title' => "Categories",
            'categories' => $allcategories,  
            //'categoryTree' => $this->categoryTree(),            
        ];
        
        return view($this->viewPath."create")->with($data);      
    }


    /**
     * Store a newly created category. 
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'picture' => "required|max:1999|mimes:jpg,jpeg,png,bmp,gif,svg,webp"  
        ],
        [
            'name.required' => 'The Category field is required.',
            'picture.required' => 'The Category Image field is required.'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $name = trim($request->input("name"));            
            //create category slug
            $slug = str_replace(' ','_',$name);
            $slug = substr_replace("_", $slug, 1);   
            
            //Check category exist, if not then create new category
            $check = self::$database->getReference(self::$reference)->orderByChild('slug')->equalTo($slug)->getValue(); 
            if(!empty($check))
            {              
                $error = ['msg' => 'Category already exist'];
                return response()->json(['error'=> $error]);
            }
            else
            {
                $parent = $request->input("parent");

                //check last exist category key
                $categories = self::$database->getReference(self::$reference)->getValue();
                $lastkey = !empty($categories) ? end($categories):'';
                $key = !empty($lastkey) ? key($categories)+1 :'1';
                $key = trim($key);

                //save category image
                $fileNameToStore = "";
                if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                    $fileName = $name.".".$request->file("picture")->getClientOriginalExtension();
                    $request->file("picture")->move(public_path('uploads/categories'), $fileName);  
                    $fileNameToStore = 'uploads/categories/'.$fileName;              
                }            

                //store category in firebase here
                $newPost = self::$database
                    ->getReference('categories/'.$key)
                    ->set([
                        'name' => $name,
                        'slug' => $slug,
                        'parent' => $parent,
                        'image' => $fileNameToStore,                        
                        'created_at' => time()   
                    ]);

                //New row append in list    
                $added_category = self::$database->getReference(self::$reference.'/'.$key)->getValue(); 
                $new_row = ''; 
                $dkey = "'".$key."'";
                if(!empty($added_category)){
                    $new_row .= '<tr id="row'.$key.'">';   //<td>'. $key .'</td>
                    $new_row .= '<td><a href="'.route('categories.show', $key) .'">';
                    $new_row .= '<img src="'.url($added_category['image']) .'" height="50px" width="50px"></a></td>';
                    $new_row .= '<td><a href="'. route('categories.show', $key) .'">'. $added_category['name'] .'</a></td>';
                    $new_row .= '<td>'. Self::getCategory($added_category['parent']) .'</td>';
                    $new_row .= '<td>0</td>';
                    $new_row .= '<td>
                                <a href="'. route('categories.edit', $key) .'" class="btn btn-dark btn-sm">Edit</a>
                                <button onclick="deleteCategory('.$dkey.')" class="btn btn-warning btn-sm">Delete</button>
                                </td></tr>';
                }
                
                // //return redirect()->route("categories.index")->with("success", "category $name added");
                return response()->json([
                    'success'=> "Category $name added successfully",   
                    'new_row' => $new_row,          
                    'uploaded_image' => '<img src="'.url($fileNameToStore).'" width="50px" height="50px"/>'
                ]);               
            }            
        }
    }

    
    /**
     * Display the Category
     */
    public function show($id)
    {        
        $title = "Category";
        $category = self::$database->getReference(self::$reference)->getChild($id)->getValue();    
        $subcategories = self::$database->getReference(self::$reference)->orderByChild('parent')->equalTo($id)->getValue();  
        $data = [
            "title" => $title,
            "category" => $category,
            "subcategories" => $subcategories,
            "id"   => $id			
        ];        

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit the Category
     */
    public function edit($id)
    {        
        $allcategories = self::getCategories();        
        $category = self::$database->getReference(self::$reference)->getChild($id)->getValue();         
        $data = [
            "title" => "Categories",
            'categories' => $allcategories,
            "category" => $category,            
            "id"   => $id			
        ];		
              
        return view($this->viewPath."edit")->with($data);
    }
    
    
	/**
     * Update the information
     */
	public function update(Request $request, $id)
    {       
        $validator = Validator::make($request->all(), [
            'name' => "required|string",  
            'picture' => "mimes:jpg,jpeg,png,bmp,gif,svg,webp"            
        ],
        [
            'name.required' => 'The Category field is required.',
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $parent = $request->input("parent");
            $name = trim($request->input("name"));
            //create category slug
            $slug = str_replace(' ','_',$name);
            $slug = substr_replace("_", $slug, 1); 

            //Check category exist, if not then create new category
            $check = self::$database->getReference(self::$reference)->orderByChild('slug')->equalTo($slug)->getValue();             
            $exist_parent = !empty($check[$id]['parent']) ? $check[$id]['parent']:'0';
            /*if(!empty($check) && $exist_parent == $parent)
            {                                      
                $error = ['msg' => 'Category already exist'];        
                return response()->json(['error'=> $error]);                              
            }
            else
            { */                                  
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
                    $fileNameToStore = $name.".".$request->file("picture")->getClientOriginalExtension();
                    $request->file("picture")->move(public_path('uploads/categories'), $fileNameToStore);
                    $image = 'uploads/categories/'.$fileNameToStore;
                }

                //Update record
                $updateProduct = self::$database
                    ->getReference(self::$reference."/".$id)
                    ->update([
                        'name' => $name,
                        'slug' => $slug,
                        'parent' => $parent,
                        'image' => $image,                                           
                        'created_at' => time()                
                    ]);         
                                
                return response()->json(['success'=> "Category updated successfully"]);
            //}
        }
    }
    

    /**
     * get category name
     */
    public static function getCategory($catekey){        
        $result = self::$database->getReference(self::$reference."/".$catekey)->getValue();  
        $category = !empty($result['name']) ? $result['name'] :'';
        return $category;        
    }
    
    
    /**
     * Fetch all categories llst
     */
    public function getCategories() : array {
        $categoriesFromFirebase = self::$database->getReference(self::$reference)->getValue();       
        $categories = [];        
        if(!empty($categoriesFromFirebase)){
            
            //Set as descending order
            $categoriesFromFirebase = array_reverse($categoriesFromFirebase, true);    
            foreach ($categoriesFromFirebase as $key => $value){                
                if(empty($value)) {
                    continue;
                }                                   
                $cate_key = trim(trim('"'.$key.'"','"'));                           
                $category = new Category();
                $category->category_id = $cate_key;
                $category->name = !empty($value['name']) ? $value['name']:'';
                $category->slug = !empty($value['slug']) ? $value['slug']:'';   
                $category->parent = !empty($value['parent']) ? $value['parent']:'0';  
                $category->image = !empty($value['image']) ? $value['image']:'';                       
                $category->created_at = Carbon::createFromTimestamp($value['created_at']);            
                $category->no_of_product = count(self::$database->getReference(ProductsController::$reference)
                        ->orderByChild('category_id')->equalTo($cate_key)->getValue());                
                array_push($categories, $category);
            }            
        } 
        //dd($categories);       
        return $categories;
    }


    /**
     * Fetch all Parent categories llst
     */
    public function getParentCategories() : array {
        $parentcategories = self::$database->getReference('categories')->orderByChild('parent')->equalTo('0')->getValue();  //(new CategoriesController)->getCategories();                                  
        $parent_categories = [];
        if(!empty($parentcategories)){
            foreach ($parentcategories as $key => $value){                
                if(empty($value)) {
                    continue;
                }                                             
                $category = new Category();
                $category->category_id = $key;
                $category->name = !empty($value['name']) ? $value['name']:'';
                $category->parent = !empty($value['parent']) ? $value['parent']:'0';       
                $category->created_at = Carbon::createFromTimestamp($value['created_at']);                            
                array_push($parent_categories, $category);
            }
        }        
        return $parent_categories;
    }


    /**
     * Fetch sub categories llst
     */
    public function getSubCategory(request $request)
    {
        $category_id = $request->input("category");
        $result = self::$database->getReference(self::$reference)->orderByChild('parent')->equalTo($category_id)->getValue();        ;  
        
        $html = '<option value="0">Select Subcategory</option>';
        if(!empty($result)){
            foreach($result as $key => $value) {            
                $html .= '<option value="'.$key.'">'.$value['name'].' ('.$key.')'.'</option>';
            }         
        }
        return $html;
    }


    /**
     * Fetch sub sub categories llst
     */
    public function getSubSubCategory(request $request)
    {
        $category_id = $request->input("category");
        $result = self::$database->getReference(self::$reference)->orderByChild('parent')->equalTo($category_id)->getValue();        ;  
        
        $html = '<option value="0">Select Sub-Subcategory</option>';
        if(!empty($result)){
            foreach($result as $key => $value) {            
                $html .= '<option value="'.$key.'">'.$value['name'].' ('.$key.')'.'</option>';
            }         
        }
        return $html;
    }


    /**
     * Category tree view function
     */
    public function categoryTree()
    {      
        $html = '';                    
        $parent_id = '0';
        $sub_mark = '';
        $data = self::$database->getReference('categories')->orderByChild('parent')->equalTo($parent_id)->getValue(); 
        foreach($data as $key => $value){                               
            $html .= '<option value="'.$key.'">'.$sub_mark.$value['name'].'</option>';            
            $html .= $this->subcategoryTree(trim($key));
        } 
        return $html;                                                                               
    }

    public function subcategoryTree($parent_id)
    {         
        $html = '';        
        $sub_mark = '--- ';
        $dataa = self::$database->getReference('categories')->orderByChild('parent')->equalTo($parent_id)->getValue(); 
        foreach($dataa as $keys => $values){                        
            $html .= '<option value="'.$keys.'">'.$sub_mark.$values['name'].'</option>';                        
        }  
        return $html;                                                                                  
    }


    public function deleteCategory(request $request)
    {    
        $category_id = $request->input("id");
        $result = self::$database->getReference(self::$reference.'/'.$category_id)->getValue();
        $image = !empty($result['image']) ? $result['image']:'';
         //Remove pic
        if(file_exists($image)) 
        {
            unlink($image);                       
        }

        $delete = self::$database->getReference(self::$reference.'/'.$category_id)->remove();
        if($delete){
            return response()->json(['success'=> "Category Deleted successfully"]); 
        }                                                                                    
    }



}