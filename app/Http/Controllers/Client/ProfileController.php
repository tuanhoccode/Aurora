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
    // public function showImformation()
    // {
    //     $user = Auth::user()->load('address');
    //     return view('client.profile.imformation', compact('user'));
    // }
    public function updateProfile(ProfileRequest $req)
    {
       
        $user = Auth::user();
        //Dữ liệu đã được validate trong ProfileRequest
        $validated = $req->validated();
        // dd($validated);
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
        } else if($user) {
            $user->address()->create([
                'fullname' => $validated['fullname'],
                'address' => $validated['address'],
                'phone_number' => $validated['address_phone_number'] ,
                'is_default' => true,
            ]);
        }else{
            return back()->with('error', 'Bạn còn thiếu các thông tin chưa điền hoặc không chính xác');
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
