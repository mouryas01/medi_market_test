<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Session;

class VendorController extends Controller
{
    public static $reference = "users";
    private $viewPath = "vendor.";

    /**
     * PeopleController constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {                
        $usersFromFirebase = self::$database->getReference(self::$reference)->getValue();  
        $users = [];
        if(!empty($usersFromFirebase)){

            //Set as descending order 
            $usersFromFirebase = array_reverse($usersFromFirebase, true); 
            foreach ($usersFromFirebase as $key => $value) {
                               
                if(!empty($value['user_type']) &&  $value['user_type'] == 2){      //2 = vendors 

                    $user_type = '';
                    if(!empty($value['user_type'])){
                        if($value['user_type'] == 1){
                            $user_type = 'Admin';
                        }elseif($value['user_type'] == 2){
                            $user_type = 'Vendor';
                        }else{
                            $user_type = '';
                        }
                    }

                    $people = new User();
                    $people->user_key = $key;
                    $people->name = $value["name"];            
                    $people->email = $value["email"];            
                    $people->user_type = $user_type;    
                    $people->picture = $value['picture'];    
                    $people->created_at = $value['created_at'];    
                    array_push($users, $people);
                }
            }
        }

        $data = [
            'title' => "Vendors",
            "users" => $users
        ];     
        return view($this->viewPath. "index")->with($data);        
    }


    public function store(request $request)
    {
        $validator = Validator::make($request->all(), [            
            'name' => "required|string",
            'email' => "required",
            'password' => 'required|min:6|same:confirm_password',                            
            'confirm_password' => 'required|min:6',
            'picture' => "required"  
        ]);

        if ($validator->fails())
        {
            //return response()->json(['error'=> $validator->errors()->all()]);           
            return redirect('register')->withErrors($validator);
        }
        else
        {
            
            $name = $request->input("name");
            $email = $request->input("email");
            $password = $request->input("password"); 
            $usertype = $request->input("user_type");        
            
            $userInfo = self::$database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
            if(empty($userInfo)){
                $image = "";
                if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                    $fileNameToStore = $name.".".$request->file("picture")->getClientOriginalExtension();
                    $request->file("picture")->move(public_path('uploads/profile'), $fileNameToStore);                                 
                    $image = 'uploads/profile/'.$fileNameToStore;
                }
                $newProduct = self::$database
                    ->getReference(self::$reference)
                    ->push([
                        'name' => $name,
                        'email' => $email,                    
                        'picture' => $image,
                        'user_type' => $usertype,
                        "password" => Hash::make($password),
                        'created_at' => time()                
                    ]);           
    
                //return response()->json(['success'=> "New user added successfully"]);  
                return redirect()->route("vendors.index")->with("success", "New user added successfully");   
            }else{
                //return redirect('register')->withErrors(["message1" => "Already exist"]);
                return Redirect::back()->with('message1', 'Already exist.');
            }
        }
    }


     /**
     * Display the vendor detail
     */
    public function show($id)
    {        
        $title = "Vendor Details";
        $userInfo = self::$database->getReference(self::$reference)->getChild($id)->getValue();          
        $data = [
            "title" => $title,          
            "userInfo" => $userInfo,            	
        ];        

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Display the edit vendor form
     */
    public function edit($id)
    {        
        $title = "Edit User Details";
        $userInfo = self::$database->getReference(self::$reference)->getChild($id)->getValue();          
        $data = [
            "title" => $title,          
            "userInfo" => $userInfo, 
            "id" => $id,           	
        ];          

        return view("auth/edit")->with($data);
    }



    public function update(request $request, $id)
    {                 
        $name = $request->input("name");   
        $email = $request->input("email");  
        $password = $request->input("password");        
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

        if(!empty($name)){
            $updateName = self::$database->getReference("users/".$id."/name")->set($name);  
        }
        if(!empty($email)){
            $updateName = self::$database->getReference("users/".$id."/email")->set($email);  
        } 
        if(!empty($password)){
            $password = Hash::make($password);
            $updatePassword = self::$database->getReference("users/".$id."/password")->set($password);  
        } 
 
        return redirect()->route("vendors.index")->with("success", "User Details updated successfully");          
    }

}
