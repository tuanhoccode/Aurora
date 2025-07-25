<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Illuminate\Support\Facades\Auth;
class AdminLoginController extends Controller
{
    public function showLoginForm(){
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request){
        $credentials = $request->only('email', 'password');
        //Kiểm tra xem remember có được chọn k
        $remember = $request->filled('remember');
        if (Auth::attempt($credentials, $remember)) {
            // dd(Auth::user()->role);
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Bạn không có quyền đăng nhập trang quản trị']);
                
            }
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập thành công!');

        }
        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Đăng xuất thành công!');
    }
}
