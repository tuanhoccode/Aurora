<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister(){
        return view('client.auth.register');
    }
    public function register(ClientRegisterRequest $req){
        User::create([
            'fullname' => $req->fullname,
            'email' => $req->email,
            'password' => Hash::make($req->password),

        ]);
        return redirect()->route('showLogin')->with('success', 'Đăng ký thành công!');
    }
}