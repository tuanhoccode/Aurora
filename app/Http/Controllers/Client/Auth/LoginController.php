<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            DB::table('sessions')->where('id', session()->getId())->update(['user_id' => $user->id]);
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->with('error' ,'Bạn cần xác thực email trước khi đăng nhập.')->withInput();
            }
            //Ghi log đăng nhập
            LoginLog::create([
                'user_id' =>$user->id,
                'session_id' =>session()->getId(),
                'ip_address' =>$req->ip(),
                'user_agent' =>$req->header('User-Agent'),
                'logged_in_at' =>now(),
                'is_current' =>true,

            ]);
           return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }
        return back() -> with(['error' => 'Email hoặc mật khẩu chưa chính xác! '])->withInput();
    }

    public function logout(Request $req){
        Auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất thành công');
    }
    
}
