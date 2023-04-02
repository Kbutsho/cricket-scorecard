<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function ShowLoginForm()
    {
        return view('pages.auth.login');
    }
    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('Login successfully!');
        }
        return redirect("login")->withDanger('Invalid credentials!');
    }
    public function logout()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login')->withSuccess('Logout successfully!');
    }
    public function Dashboard()
    {
        return view('pages.dashboard.dashboard');
    }
}
