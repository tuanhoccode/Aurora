<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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
            }
        );
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công');
        }

        $messages = [
            Password::INVALID_TOKEN => 'Email khôi phục mật khẩu không hợp lệ.',
            Password::INVALID_USER => 'Email không tồn tại trong hệ thống.',
            Password::RESET_THROTTLED => 'Vui lòng đợi trước khi thử lại.',
            Password::RESET_LINK_SENT => 'Email đặt lại mật khẩu đã được gửi.',
        ];
        return back()->withErrors(['email' => $messages[$status] ?? 'Có lỗi xảy ra vui lòng thử lại']);
    }
}
