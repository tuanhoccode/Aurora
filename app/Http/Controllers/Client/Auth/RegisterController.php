<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientRegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('client.auth.register');
    }
    public function register(ClientRegisterRequest $req)
    {
        $user = User::create([
            'fullname' => $req->fullname,
            'email' => $req->email,
            'password' => Hash::make($req->password),

        ]);
        Auth::login($user); //login user sau khi đăng ký
          Log::info('📧 Đang gửi email xác thực cho: ' . $user->email);
        event(new Registered($user)); //gửi email xác thực
        return redirect()->route('verification.notice')->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác minh tài khoản');
    }
}
