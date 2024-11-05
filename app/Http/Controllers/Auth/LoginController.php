<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {                
        self::$database = self::firebaseDatabaseInstance();
        // $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {          
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails())
        {            
            return redirect(url()->previous())->withErrors($validator)->withInput();
        }
        else
        {
            $username = $request->input("email");
            $password = $request->input("password");
            $users = self::$database->getReference('users')->getvalue();	 
            if(!empty($users)){       
                foreach($users as $key => $value){	                
                    if(trim($value['email']) == trim($username))
                    {
                        if(Hash::check($password, $value['password'])){                                           
                            Session::put('uid', $key);                            
                            Session::put('user_type', $value['user_type']);  
                        }
                        else
                        {                            
                            return back()->withErrors(['password'=>'Wrong password']);                                                    
                        }                                
                    }                                   
                }               
                if(Session::get('user_type') == 1){
                    return redirect()->route('admin.home');               
                }elseif(Session::get('user_type') == 2){
                    return redirect()->route('home');
                }else{
                    //return redirect()->route('/');
                    return redirect()->route('login')->with('error','User not exist.');
                }
            }else{
                return redirect()->route('login')->with('error','Username and password are wrong.');
            }
        }
    
        // if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        // {
        //     if (auth()->user()->is_admin == 1) {
        //         return redirect()->route('admin.home');
        //     }else{
        //         return redirect()->route('home');
        //     }
        // }else{
        //     return redirect()->route('login')
        //         ->with('error','Email-Address And Password Are Wrong.');
        // }
          
    }
}
