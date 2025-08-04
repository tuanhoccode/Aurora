<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $maxAttempts = 5; 
    protected $decayMinutes = 1;
    public function showLogin()
    {
        return view('client.auth.login');
    }
    
    public function throttleKey(Request $req){
        return Str::lower($req->input('email')) . '|' . $req->ip();
    }

    public function login(ClientLoginRequest $req)
    {
        // Kiểm tra tối đa đăng nhập quá 5 lần
        $limiter = app(RateLimiter::class);
        $key = $this-> throttleKey($req);
        //Nếu người dùng nhập quá giới hạn
        if ($limiter->tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = $limiter->availableIn($key);
            return back()->withErrors(['email' => "Bạn đã đăng nhập quá nhiều lần. Vui lòng thử lại sau {$seconds} giây"]);
        }
        
        $credentials = $req->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        //Không tìm thấy tài khoản
        if (!$user) {
            $limiter->hit($key, $this->decayMinutes * 60);
            return back()->with(['error' => 'Tài khoản không tồn tại!'])->withInput();
        }
        //Kiểm tra trạng thái tk bị khóa
        if ($user->status === 'inactive') {
            $reason = $user->reason_lock ? 'Lý do:' . $user->reason_lock : '';
            return back()->with(['error' => 'Tài khoản của bạn đã bị khóa.' . $reason])->withInput();
        }

        //Kiểm tra xem remember có được chọn k
        $remember = $req->filled('remember');
        if (Auth::attempt($credentials, $remember)) {
            $limiter->clear($key); //Đăng nhập thành công thì reset số lần thử
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user = $user->fresh(); // Làm mới lại thông tin từ database
            DB::table('sessions')->where('id', session()->getId())->update(['user_id' => $user->id]);
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->with('error', 'Bạn cần xác thực email trước khi đăng nhập.')->withInput();
            }
            $ip = $req->ip();
            $agent = $req->header('User-Agent');
            //Kiểm tra xem IP + user_agent này đã từng được ghi nhận chx
            $existingLog = LoginLog::where('user_id', $user->id)->where('ip_address', $req->ip())
            ->where('user_agent', $req->header('User-Agent'))->exists();
            //Ghi log đăng nhập
            LoginLog::create([
                'user_id' => $user->id,
                'session_id' => session()->getId(),
                'ip_address' => $req->ip(),
                'user_agent' => $req->header('User-Agent'),
                'logged_in_at' => now(),
                'is_current' => true,
                
            ]);
            
            if (!$existingLog) {
                //Gửi mail cảnh báo đăng nhập lạ
                Mail::to($user->email)->send(new LoginAlertMail(
                    $user,
                    $ip,
                    $agent
                ));
            }
            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }
        //Đănh nhập thất bại
        $limiter->hit($key, $this->decayMinutes * 60);
        $attemptsLeft = $this->maxAttempts - $limiter->attempts($key);
        return back()-> withErrors(['email' => "Đăng nhập sai. Bạn còn {$attemptsLeft} lần thử."])->withInput();
    }

    public function logout(Request $req)
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất thành công');
    }
}
