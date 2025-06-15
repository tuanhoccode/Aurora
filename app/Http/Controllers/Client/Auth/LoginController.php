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
        
        //Kiểm tra xem remember có được chọn k
        $remember = $req->filled('remember');
        if (Auth::attempt($credentials, $remember )) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user = $user->fresh(); // Làm mới lại thông tin từ database
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->with('error' ,'Bạn cần xác thực email trước khi đăng nhập.')->withInput();
            }
           return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }
        return back() -> with(['error' => 'Email hoặc mật khẩu chưa chính xác! '])->withInput();
    }

    public function logout(Request $req){
        Auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất thành công');
    }
    
}
