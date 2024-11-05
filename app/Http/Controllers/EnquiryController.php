<?php

namespace App\Http\Controllers;

use App\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Session;

class EnquiryController extends Controller
{
    public static $reference = "advice";    

    /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Display a listing of Enquiries.    
     */
    public function index()
    {                                               
        $getEnquiries = self::getEnquiries();                     
        $data = [
            'title' => "Enquiries",
            'enquiries' => $getEnquiries,                       
        ];
        //dd($data);
        return view("enquiry")->with($data);
    }


    /**
     * Fetch all enquiries llst
     */
    public function getEnquiries() : array {
        $enquiriesFromFirebase = self::$database->getReference(self::$reference)->getValue();       
        $enquiries = [];        
        if(!empty($enquiriesFromFirebase)){
            
            //Set as descending order
            $enquiriesFromFirebase = array_reverse($enquiriesFromFirebase, true);    
            foreach ($enquiriesFromFirebase as $key => $value){                
                if(empty($value)) {
                    continue;
                }                   
                
                $advice_key = trim(trim('"'.$key.'"','"'));                           
                $enquiry = new Enquiry();
                $enquiry->advice_id = $advice_key;
                $enquiry->email = !empty($value['email']) ? $value['email']:'';
                $enquiry->message = !empty($value['message']) ? $value['message']:'';  
                $enquiry->name = !empty($value['name']) ? $value['name']:'';       
                $enquiry->created_at = Carbon::createFromTimestamp($value['created_at']);            
                
                array_push($enquiries, $enquiry);
            }

        }        
        return $enquiries;
    }



    public function enquiry_reply(request $request)
    {
        $validator = Validator::make($request->all(), [
            'reply_message' => "required",             
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $reply_message = $request->input("reply_message");            
            $advice_id = $request->input("advice_id");

            $getAdviceInfo = self::$database->getReference('advice/'.$advice_id)->getValue();
            if(!empty($getAdviceInfo)){
                $email = $getAdviceInfo['email'];
            }

            if(!empty($email)){
                $reply_id = self::$database
                    ->getReference('advice_reply')
                    ->push([
                        'reply_message' => $reply_message,
                        'advice_id' => $advice_id,
                        'email' => $email,                    
                        'created_at' => time()                
                    ])->getKey();
            

                $fetchReply = self::$database->getReference('advice_reply/'.$reply_id)->getValue();
                $enquiries = ''; 
                if(!empty($fetchReply)){
                    $reply_message = !empty($fetchReply['reply_message']) ? $fetchReply['reply_message']:'';                       
                    $created_at = Carbon::createFromTimestamp($fetchReply['created_at']);  

                    $enquiries .= '<div id="reply_msg">';
                    $enquiries .= '<h6>'.$created_at.'</h6>';
                    $enquiries .= '<p> '.$reply_message.'</p></div>';
                }  

                //Get customer device token and Send notification to customer
                $getCustomerInfo = self::$database->getReference('fd_users')->orderByChild("email")->equalTo($email)->getValue();	        
                if(!empty($getCustomerInfo)){
                    foreach($getCustomerInfo as $key => $value){

                        //Send Notification 
                        $title = 'Message Reply';                    
                        $this->send_notification($value['device_token'], $title, $reply_message); 

                        $user = [];
                        $user['subject'] = $title;
                        $user['to_email'] = !empty($value['email']) ? $value['email']:'';
                        $user['name'] = !empty($value['name']) ? $value['name']:'';

                        $content = "<html>
                                <head>
                                </head>
                                <body>
                                    <p>Dear ".$user['name'].", 
                                    <br><br>".$reply_message."
                                    
                                    <br><br>
                                                                    
                                    Medimarket</p>";
                        $content2 = "</body>
                                    </html>";
                        $body = $content.$content2;
                        $user['content'] = $body;

                        //Send Mail
                        $sendmail = Mail::send(array(), array(), function($message) use ($user){                      
                            $message->from('support@knickglobal.co.in','Medimarket');                
                            $message->to($user['to_email'], $user['name']);
                            $message->subject($user['subject']);  
                            $message->setBody($user['content'], 'text/html');
                        });
                    }
                }
                return $enquiries;
            }
            //response()->json(['success'=> "Reply notification sent to customer"]);
        }
    }


    /**
     * Display a listing of Enquiries.    
     */
    public function fetch_enquiry(request $request)
    {                
        $advice_id = $request->input("advice_id");           
        $fetchAllReply = self::$database->getReference('advice_reply')->orderByChild('advice_id')->equalTo($advice_id)->getValue(); 
        $enquiries = '';        
        if(!empty($fetchAllReply)){                                   
            foreach ($fetchAllReply as $ar_key => $arvalue){                
                if(empty($arvalue)) {
                    continue;
                }                                    
                $reply_message = !empty($arvalue['reply_message']) ? $arvalue['reply_message']:'';                       
                $created_at = Carbon::createFromTimestamp($arvalue['created_at']);  

                $enquiries .= '<div id="reply_msg">';
                $enquiries .= '<h6>'.$created_at.'</h6>';
                $enquiries .= '<p> '.$reply_message.'</p></div><hr>';               
            }
        }   
        return $enquiries;         
    }
}
