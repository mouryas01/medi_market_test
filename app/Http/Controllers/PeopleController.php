<?php

namespace App\Http\Controllers;

use App\People;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Session;

class PeopleController extends Controller
{
    public static $reference = "fd_users";
    private $viewPath = "people.";

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
        //dd($usersFromFirebase); 
        $users = [];
        if(!empty($usersFromFirebase)){
            //Set as descending order 
            $usersFromFirebase = array_reverse($usersFromFirebase, true);             
            foreach ($usersFromFirebase as $key => $value) {
                            
                $people = new People();
                $people->user_key = $key;
                $people->user_id = $value["uid"];   
                $people->name = $value["name"];            
                $people->email = $value["email"];            
                $people->street = $value['street'];  
                $people->city = $value['city'];   
                $people->country = $value['country'];       
                $people->joined = $value['joined'];    
                $people->created_at = Carbon::createFromTimestamp($value['created_at']);  
                array_push($users, $people);
            }
        }

        $data = [
            'title' => "Users",
            "users" => $users
        ];     
        return view($this->viewPath. "index")->with($data);        
    }


     /**
     * Display the cusotmer detail
     */
    public function show($id)
    {                
        $title = "User Details";
        $userInfo = self::$database->getReference(self::$reference)->getChild($id)->getValue();            
        $data = [
            "title" => $title,          
            "userInfo" => $userInfo,            	
        ];        
        //dd($data);
        return view($this->viewPath."show")->with($data);
    }
}
