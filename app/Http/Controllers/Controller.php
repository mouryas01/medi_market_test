<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Kreait\Firebase\Auth;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected static $database;

    protected static function firebaseDatabaseInstance()
    {        
        $factory = (new Factory)->withServiceAccount(__DIR__.'/medimarket-c1698-firebase-adminsdk-8mmq8-d74bb05e94.json');
        $database = $factory->createDatabase();
        return $database;
    }

    
    function send_notification($regid, $title, $msgg, $check = null)
    {
        $serverKey = 'AAAAk5t1fLQ:APA91bFy9S3f8FM9IhIvPY3paiWbMpyiH8nh28KGxQg88-yEimtvCxd0gSaqjV-auq6bfui2ha5O8Z5sZ6zAiBdZJd-gu80C8x-XeYFNejeZZm7czWnMfhYVp74ayx-HvYCv-02fyuVJ';
                
        if (!defined('API_ACCESS_KEY')) define('API_ACCESS_KEY', $serverKey);

        $registrationIds        = $regid;
        $title                  = $title;
        $msg                    = $msgg;

        //prep the bundle
        $msg    =   array(
                        'body'  => $msg,
                        'title' => $title
                    );
                
        $fields =   array(
                        'to'    => $registrationIds, 
                        'data'  => $msg
                    );
                    
        $headers =  array(
                        'Authorization: key=' . API_ACCESS_KEY,
                        'Content-Type: application/json'
                    );
        
        #Send Reponse To FireBase Server    
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        if($check == null){
            //echo $result;  
        }  
    }

    public function random_strings($length_of_string) 
    { 
        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
      
        // Shufle the $str_result and returns substring of specified length 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    }
}
