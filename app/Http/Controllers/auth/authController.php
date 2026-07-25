<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\loginReqeust;
use App\Http\Requests\auth\signupReqeust;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class authController extends Controller
{
    public function showLoginAndSingUpForm()
    {
            return view('auth.login&signup');
    }
    public function signup(signupReqeust $request){
        $data = $request->validated();

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        if($user){
            return back()->with('success', 'You have been registered!');
        }else{
            return back()->with('fail', 'Something went wrong!');
        }
    }

    public function login(loginReqeust $request){

        $data = $request->validated();
        $credentials = $data;
        $userStatus = User::where('email', $data['email'])->select('status')->first();

        if ($userStatus->status == 'active') {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('homepage'))->with('success', 'You have been logged in!');
            }

            return back()->with('fail','email or password is incorrect!');
        }
        return back()->with('fail','There is a problem with your account please contact the administrator!');
    }

}
