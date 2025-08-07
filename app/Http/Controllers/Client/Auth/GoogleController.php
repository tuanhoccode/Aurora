<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            if ($existingUser) {
                //Khóa tk thì chặng đăng nhập
                if($existingUser->status === 'inactive'){
                    $reason = $existingUser->reason_lock ? 'Lý do:' . $existingUser->reason_lock: '';
                    return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.' . $reason);
                }
                //Cập nhật google_id nếu thiếu
                if (!$existingUser->google_id) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }

                $user = $existingUser;
            }else{
                $user = User::create([
                    'fullname' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);
            }
            
            Auth::login($user);
            session()->regenerate();

        // Ghi log login
        LoginLog::create([
            'user_id'     => $user->id,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->header('User-Agent'),
            'logged_in_at'=> now(),
            'session_id'  => session()->getId(),
            'is_current'  => true,
        ]);
            return redirect()->route('home')->with('success', 'Đăng nhập bằng Google thành công!');
        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Có lỗi xảy ra khi đăng nhập bằng tài khoản Google!');
        }
    }
}
