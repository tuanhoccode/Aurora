<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin(){
        return view('client.auth.login');
    }

    public function login(ClientLoginRequest $req){
        $credentials = $req->only('email', 'password');
        if (Auth::attempt($credentials)) {
           return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }
        return back() -> withErrors(['email' => 'Email hoặc mật khẩu chưa chính xác! '])->withInput();
    }

    public function logout(Request $req){
        Auth::logout();
        return redirect()->route('showLogin')->with('success', 'Đăng xuất thành công');
    }
    
}
