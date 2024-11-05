<?php

namespace App\Http\Controllers;

use App\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File; 
use Session;

class BlogController extends Controller
{
    public static $reference = "blogs";
    private $viewPath = "blog.";

    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }

     /**
     * Display a listing of blogs.    
     */
    public function index()
    {             
        $getblogs = self::getblogs();
        $data = [
            'title' => "Blogs",
            'blogs' => $getblogs,
        ];                
        return view($this->viewPath."index")->with($data);
    } 


    /**
     * Fetch all blogs llst
     */
    public function getBlogs() : array {
     
        $fetchBlogs = self::$database->getReference(self::$reference)->getValue();
        $blogs = [];
        if(!empty($fetchBlogs)){
            //Set as descending order 
            $fetchBlogs = array_reverse($fetchBlogs, true);  

            foreach ($fetchBlogs as $key => $value){                
                if(empty($value)) {
                    continue;
                }                   
                $blog_key = trim(trim('"'.$key.'"','"'));   
                $blog = new Blog();
                $blog->blog_key = $blog_key;
                $blog->description = $value['description'];                 
                $blog->title = $value['title'];                                                                  
                $blog->image = $value['image'];     
                $blog->created_at = Carbon::createFromTimestamp($value['created_at']);                 
                array_push($blogs, $blog);
            }                   
        }        
        return $blogs;
    }

    /**
     * Add new blog.          
     */
    public function create()
    {                   
        //$blogs = self::$database->getReference('blogs')->getValue();  
        $data = [
            'title' => "Blogs",            
        ];        
        return view($this->viewPath."create")->with($data);
    }


    /**
     * Store a newly created resource in storage.    
     */
    public function store(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'title' => "required",           
            'description' => "required",                
            'picture' => "required|image"  
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {            
            $title = $request->input("title");            
            $description = $request->input("description");           
            
            $fileNameToStore = "";
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                $eventPicName = trim(str_replace(' ', '', $title));
                $fileNameToStore = $eventPicName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/blogs'), $fileNameToStore); 
                $image = 'uploads/blogs/'.$fileNameToStore;
            }
                   
            $newPost = self::$database
                    ->getReference(self::$reference)
                    ->push([                        
                        'title' => $title,                                           
                        'image' => $image,                    
                        'description' => $description,                             
                        'created_at' => time()   
                    ]);                    
            
            return response()->json([
                    'success'=> "Blogs saved successfully",
                    'uploaded_image' => '<img src="'.url($image).'" width="50px" height="50px"/>'
                ]);
        }
    }


    /**
     * Display the specified resource.     
     */
    public function show($id)
    {        
        $blogData = self::$database->getReference(self::$reference."/".$id)->getValue(); 
        $blog = [];
        if(!empty($blogData)){   
            //blog info            
            $blog['title'] = $blogData['title']; 
            $blog['description'] = $blogData['description'];            
            $blog['image'] = $blogData['image'];                                         
            $blog['created_at'] = Carbon::createFromTimestamp($blogData['created_at']);             
        }                                   
        $data = [
            'title' => "Blog",
            'blog' =>  $blog,           
            'id' => $id
        ];                    

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit blog info.     
     */
    public function edit($id)
    {                                     
        $blog = self::$database->getReference(self::$reference."/".$id)->getValue();                                
        $data = [
            'title' => "Blog",
            'blogs' => $blog,          
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
            'title' => "required",            
            'description' => "required",                     
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $title = $request->input("title");            
            $description = $request->input("description");                              
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
                $eventPicName = trim(str_replace(' ', '', $title));
                $fileNameToStore = $eventPicName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/blogs'), $fileNameToStore); 
                $image = 'uploads/blogs/'.$fileNameToStore;               
            }

            $updateProduct = self::$database
                ->getReference(self::$reference."/".$id)
                ->update([                    
                    'title' => $title,                                       
                    'image' => $image,                    
                    'description' => $description,                                                   
                    'created_at' => time()   
                ]);
            
            return response()->json(['success'=> "Blog updated successfully"]);
        }
    }


    /**
     * Remove the specified resource from storage.   
     */
    public function destroy($id)
    {
        $data = self::$database->getReference(self::$reference."/".$id)->getValue();
        $delete = file_exists(public_path($data['image'])) ? unlink(public_path($data['image'])):'';
        self::$database->getReference(self::$reference."/".$id)->remove();       
        return back();
    }
    
}
