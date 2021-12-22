<?php

namespace App\Http\Controllers;


use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class UserModelController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }
    public function insert_register(Request $request)
    {

        // $this->validate($request,[
        //     'firstname'=>'required',
        //     'lastname'=>'required',
        //     'email'=>'required|email|unique:logins',
        //     'password' => 'min:6|required_with:cpassword|same:cpassword',
        //     'cpassword' => 'min:6',
        // ]);


       if(empty($request->firstname))
       {
           return response()->json(['error'=>'Firstname Required']);
       }
       else if(empty($request->lastname))
       {
            return response()->json(['error'=>'Lastname Required']);
       }
       elseif(empty($request->email))
       {
            return response()->json(['error'=>'Email Required']);
       }
       elseif(empty($request->password))
       {
        return response()->json(['error'=>'password Required']);
       }
       elseif(empty($request->cpassword))
       {
        return response()->json(['error'=>'Conform password Required']);
       }
       elseif($request->password !==$request->cpassword)
       {
           return response()->json(['error'=>'passwords do not match']);
       }
       else
       {
            $user=new UserModel;
            $user->firstname=$request->firstname;
            $user->lastname=$request->lastname;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $result=$user->save();
            if($result)
            {
                return response()->json(['success'=>'Register Successfully']);
            }
            else
            {
            return response()->json(['error'=>'Error Somewhere']);
            }
       }
    }
    public function fpassword()
    {
        return view('auth.fpassword');
    }

    public function checkEmail(Request $request){
        $email = $request->input('email');
        $isExists = UserModel::where('email',$email)->first();
        if($isExists){
            return response()->json(array("exists" => 'This email is already taken'));
        }else{
            return response()->json(array("exists" => 'No one use this email'));
        }
    }

    public function checklogin(Request $request)
    {

        if(empty($request->email))
        {
             return response()->json(['error'=>'Email Required']);
        }
        elseif(empty($request->password))
        {
         return response()->json(['error'=>'password Required']);
        }
        else
        {
            $user=UserModel::where('email','=',$request->post('email'))->first();
            if($user)
            {
                if(Hash::check($request->post('password'),$user->password))
                {
                    $request->session()->put('login_id',$user->id);
                    $request->session()->put('email',$user->email);
                    //return redirect('dashboard')->with('message','Login Successfully');
                    return response()->json(['success'=>'Login Successfully']);
                }
                else
                {
                    //return back()->with('error','Invalid password');
                    return response()->json(['error'=>'Invalid password']);
                }
            }
            else
            {
                //return back()->with('error','This email is not registered');
                return response()->json(['error'=>'This email is not registered']);
            }
        }

    }
    public function dashboard()
    {
        $data=UserModel::get();
        return view('auth.dashboard',['list_user'=>$data]);
    }

    public function logout()
    {
        if(session()->has('login_id'))
        {
            session()->pull('login_id');
            return redirect('login')->with('message','Logout Successfully');
        }
    }
    //google login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    //google call back
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
    }
    //facebook login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    //facebook call back
    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        $this->registerorlogin($user);
        return redirect()->route('dashboard');
    }

    public function registerorlogin($data)
    {
        $user=UserModel::where('email','=',$data->email)->first();
        if(!$user)
        {
            $user=new UserModel;
            $user->firstname=$data->first_name;
            $user->lastname=$data->last_name;
            $user->email=$data->email;
            $user->password=Hash::make($data->password);
            $user->save();
        }
        Auth::login($user);
    }
}
