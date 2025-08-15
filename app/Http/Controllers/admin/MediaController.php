<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            
            // Lưu file vào thư mục public/uploads/blog
            $path = $file->storeAs('public/uploads/blog', $fileName);
            
            // Tạo URL công khai cho ảnh
            $url = Storage::url($path);
            $fullUrl = asset($url);

            return response()->json([
                'uploaded' => true,
                'url' => $fullUrl
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => [
                'message' => 'Không thể tải lên tệp. Vui lòng thử lại.'
            ]
        ]);
    }
}
