<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProfileRequest;
use App\Http\Requests\Client\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user()->load('address');
        return view('client.profile.pro', compact('user'));
    }
    public function updateProfile(ProfileRequest $req)
    {
       
        $user = Auth::user();
        //Dữ liệu đã được validate trong ProfileRequest
        $validated = $req->validated();
        // dd($validated);
        $emailChanged = $validated['email'] !== $user->email;
        //Cập nhật bảng users
        $user->update([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'birthday' => $validated['birthday'],
            'gender' => $validated['gender'],
        ]);
        
        //Cập nhật hoặc tạo mới user_address
        $userAddress = $user->address()->first();
        if ($userAddress) {
            $userAddress->update([
                'fullname' => $validated['fullname'],
                'address' => $validated['address'],
                'phone_number' => $validated['address_phone_number'] ,
                
            ]);
        } else{
            $user->address()->create([
                'fullname' => $validated['fullname'],
                'address' => $validated['address'],
                'phone_number' => $validated['address_phone_number'] ,
                'is_default' => true,
            ]);
        }
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            Auth::logout();
            return redirect()->route('login')->with('success', 'Bạn đã thay đổi email. Vui lòng xác minh địa chỉ email mới để tiếp tục.');
        }
        
        return redirect()->route('showProfile')->with('success', 'Cập nhật hồ sơ thành công');
    }
    public function avatar(UpdateAvatarRequest $req){
        $user = Auth::user();
        //Xóa avatar cũ nếu có
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public') ->delete($user->avatar);
        }

        //Lưu avatar mới 
        $path = $req->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        return back()->with('success', 'Cập nhật ảnh đại diện thành công');

    }
}
