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
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string',
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
