<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash as FacadesHash;

class ChangePasswordController extends Controller
{
    public function changePassword(ChangePasswordRequest $req){
        $user = Auth::user();
        //Kiểm tra mật khẩu hiện tại có đúng k
        if (!Hash::check($req->old_password, $user->password)) {
            return back() -> withErrors(['old_password' => 'Mật khẩu hiện tại không đúng']);
        }
        //mk mới trùng với mk cũ
        if (Hash::check($req->new_password, $user->password)) {
           return back()->withErrors(['new_password' => 'Mật khẩu mới không được giống mật khẩu cũ']);
        }
        //Lưu lại mk mới
        $user->password = Hash::make($req->new_password);
        $user->save();
        return back()->with('success', 'Đổi mật khẩu thành công');
    }
}
