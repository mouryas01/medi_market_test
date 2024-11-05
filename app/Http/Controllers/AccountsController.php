<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Orders;
use Hash;
use Mail;

class AccountsController extends Controller
{
     /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {        
        self::$database = self::firebaseDatabaseInstance();
    }


    public function get_array_key_first($data) {
        foreach ($data as $key => $value) { 
            return $key; 
        } 
    }


    public function sendResetLinkEmail(request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',                
        ]);
        
        if($validator->fails())
        {            
            return redirect()->back()->with('errors', $validator->errors());                       
        }
        else
        {
            $email = $request->email;        
            $userInfo = self::$database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();               
            if(!empty($userInfo)) {                                  
                //Create Password Reset Token        
                $pwdreset = self::$database
                    ->getReference('password_resets')
                    ->push([
                        'email' => $request->email,
                        'token' => str_random(60),                
                        'created_at' => time()   
                    ])->getKey();

                //Get the token just created above        
                $getInfo = self::$database->getReference('password_resets/'.$pwdreset)->getValue();
                $token = $getInfo['token'];     
            }

            if(!empty($token)){
                if ($this->sendResetEmail($request->email, $token)) {
                    return redirect()->back()->withErrors(['status' => trans('A reset link has been sent to your email address.')]);
                } else {
                    return redirect()->back()->withErrors(['error' => trans('A Network Error occurred. Please try again.')]);
                }
            }
        }
    }


    private function sendResetEmail($to_email, $token)
    {       
        //Generate, the password reset link. The token generated is embedded in the link
        $link = url('/') . '/password/reset/' . $token . '?email=' . urlencode($to_email);

        try {
            //Here send the link                                      
            $sendmail = Mail::send('auth.passwords.reset_template', ['link' => $link], function($message) use ($to_email) {
                $message->to($to_email);
                $message->subject('Reset Password');
                $message->from('support@knickglobal.co.in', 'Medimarket Team');
            });            

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function reset_password(request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',                
            'password' => 'required|min:6|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
        
        if($validator->fails())
        {            
            return redirect()->back()->with('errors', $validator->errors());                       
        }
        else
        {                        
            $email = $request->input('email');
            $password = trim($request->input('password')); 
            $users = self::$database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();                                      

            if(!empty($users)){  
                //reset password, update new password
                $userData_Key = $this->get_array_key_first($users);             		                                           
                $password =  Hash::make($password);                
                $changepassword = self::$database->getReference('users/'.$userData_Key)->getChild('password')->set($password);
                
                //Remove generated token from password reset table
                $password_resets = self::$database->getReference('password_resets')->orderByChild('email')->equalTo($email)->getValue();
                if(!empty($password_resets)){                 
                    $record_Key = $this->get_array_key_first($password_resets); 
                    $delete = self::$database->getReference('password_resets/'.$record_Key)->remove();
                } 
                            
                return redirect('/')->with('success', 'An activation link was send to your email address.');
            }
        }


    }

}
