<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Hiển thị form liên hệ
    public function index()
    {
        return view('client.contact');
    }

    // Xử lý gửi liên hệ
    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|min:3|max:255',
            'email'   => [
                'required',
                'max:255',
                'regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/'
            ],
            'phone'   => [
                'nullable',
                'regex:/^\+?[0-9]{9,15}$/'
            ],
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min'      => 'Họ và tên phải có ít nhất :min ký tự.',
            'name.max'      => 'Họ và tên không được vượt quá :max ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.regex'    => 'Email không hợp lệ. Vui lòng nhập đúng định dạng (ví dụ: ten@domain.com).',
            'email.max'      => 'Email không được vượt quá :max ký tự.',

            'phone.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập từ 9 đến 15 chữ số, có thể bắt đầu bằng +.',

            'message.required' => 'Vui lòng nhập nội dung liên hệ.',
            'message.min'      => 'Nội dung phải có ít nhất :min ký tự.',
        ]);

        Contact::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm!');
    }
}
