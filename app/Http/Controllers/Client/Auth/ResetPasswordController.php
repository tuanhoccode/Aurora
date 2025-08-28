<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm($token){
        return view('client.reset-password', ['token' => $token]);
    }
    public function reset(ResetPasswordRequest $req){
        $status = Password::reset(
            $req->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password){
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
                //Xóa session để logout mọi tk khi thay đổi mk
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
        );
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công');
        }

        $messages = [
            Password::INVALID_TOKEN => 'Liên kết không hợp lệ hoặc đã hết hạn.',
            Password::INVALID_USER => 'Liên kết không hợp lệ hoặc đã hết hạn.',
            Password::RESET_THROTTLED => 'Vui lòng đợi trước khi thử lại.',
            Password::RESET_LINK_SENT => 'Email đặt lại mật khẩu đã được gửi.',
        ];
        return back()->withErrors(['email' => $messages[$status] ?? 'Có lỗi xảy ra vui lòng thử lại']);
    }
}
