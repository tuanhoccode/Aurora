<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LogoutAllRequest;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginHistoryController extends Controller
{
    public function loginHistory(){
        $logs = LoginLog::where('user_id', Auth::id())
        ->orderByDesc('logged_in_at')->paginate(15);
        return view('client.auth.login-history', compact('logs'));
    }
    public function logoutAll(LogoutAllRequest $req){
        $user = Auth::user();
        $isGoogleUser = !empty($user->google_id);
        //Kiểm tra mk
        if (!$isGoogleUser) {
            if (!Hash::check($req->password, $user->password)) {
                return back()->with('error', 'Mật khẩu không chính xác!');
            }
            Auth::logoutOtherDevices(request('password'));
        }
        //Xác định id phiên hiện tại
        // $currentSessionId = session()->getId();
        //Xóa các session khác của user(trừ phiên dăng nhập)
        DB::table('sessions')->where('user_id', $user->id)
        ->where('id', '!=', session()->getId())->delete();
        
        //Cập nhật login_log & is_current = false cho các bản ghi
        LoginLog::where('user_id', $user->id)->where('is_current', true)
        ->where('session_id', '!=', session()->getId())->update(['is_current' => false]);
        return redirect()->route('loginHistory')->with('success', 'Đã đăng xuất khỏi tất cả các thiết bị khác.');
    }
}
