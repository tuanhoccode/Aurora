<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        // Nếu muốn gửi email, có thể dùng Mail::to(...)->send(...)
        // Ở đây chỉ log lại nội dung liên hệ
        try {
            Log::info('Contact form submitted', $validated);
            // Mail::to('info@yourdomain.com')->send(new ContactMail($validated));
            return back()->with('success', 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.');
        } catch (\Exception $e) {
            return back()->with('error', 'Đã có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }
} 