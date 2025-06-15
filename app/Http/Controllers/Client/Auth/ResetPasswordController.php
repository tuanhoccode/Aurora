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
        return $status ===Password::PASSWORD_RESET
        ? redirect()->route('login')->with('success','Đặt lại mật khẩu thành công' )
        : back()->withErrors(['email' => __($status)]);
    }
}
