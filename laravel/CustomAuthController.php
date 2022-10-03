<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return redirect('/');
        }

        return view('auth.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/')
                ->withSuccess('You Are Succussfully logged in!');
        }

        return redirect("login")->withSuccess('Login details are not valid');
    }

    public function signOut()
    {
        // Session::flush();
        Auth::logout();
        return Redirect('login');
    }
}
