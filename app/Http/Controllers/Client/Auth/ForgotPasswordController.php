<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Http\Requests\Client\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('client.forgot-password');
    }
    public function sendRequestLinkEmail(ForgotPasswordRequest $req)
    {
        $status = Password::sendResetLink($req->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Đã gửi email đặt lại mật khẩu! Vui lòng kiển tra hộp thư')
            : back()->with('error', 'Không thể gửi email! Vui lòng thử lại' );
    }
}
