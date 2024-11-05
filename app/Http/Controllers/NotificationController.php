<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;

class NotificationController extends Controller
{
     /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Display a listing of customers.    
     */
    public function index()
    {          
        $fetchCustomers = self::$database->getReference('fd_users')->getValue();  
        $customers = [];
        if(!empty($fetchCustomers)){
            foreach ($fetchCustomers as $customer_id => $customerValue){  
                
                if(empty($customerValue)) {
                    continue;
                }                   
                $customer_id = trim(trim('"'.$customer_id.'"','"')); 
                $data = [];
                $data['cid'] = $customer_id;
                $data['name'] = !empty($customerValue['name']) ? $customerValue['name']:'';
                $data['device_token'] = !empty($customerValue['device_token']) ? $customerValue['device_token']:'';
                $data['email'] = !empty($customerValue['email']) ? $customerValue['email']:'';                           
                array_push($customers, $data);                        
            } 
        }   
        $data = [
            'title' => "Send Notification",
            'customers' => $customers,
        ];        

        return view("sendNotification")->with($data);
    }  


    //Send notification to customer
    public function sendNotification(request $request)
    {
        $tokens = $request->device_token;
        $title = $request->title;
        $message = $request->message;
        if(!empty($tokens)){
            foreach($tokens as $token){
                if(empty($token)){
                    continue;
                }else{                
                    //Send notification
                    $this->send_notification($token, $title, $message); 
                }                                
            }
        }    

        //Fetch customer    
        $fetchCustomers = self::$database->getReference('fd_users')->getValue();  
        $customers = [];
        if(!empty($fetchCustomers)){
            foreach ($fetchCustomers as $customer_id => $customerValue){  
                
                if(empty($customerValue)) {
                    continue;
                }                   
                $customer_id = trim(trim('"'.$customer_id.'"','"')); 
                $data = [];
                $data['cid'] = $customer_id;
                $data['name'] = !empty($customerValue['name']) ? $customerValue['name']:'';
                $data['device_token'] = !empty($customerValue['device_token']) ? $customerValue['device_token']:'';
                $data['email'] = !empty($customerValue['email']) ? $customerValue['email']:'';                           
                array_push($customers, $data);                        
            } 
        }   

        $data = [
            'title' => "Send Notification",
            'customers' => $customers,            
            'msg' => 'Notification has been send',
        ];  

        return view("sendNotification")->with($data);

    }

}
