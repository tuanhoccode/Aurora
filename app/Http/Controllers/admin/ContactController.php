<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    // Danh sách liên hệ
    public function index(Request $request)
    {
        $query = Contact::withTrashed(); // lấy cả bản ghi đã xóa mềm

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Lọc trạng thái
        if ($request->filled('status') && in_array($request->status, ['pending', 'replied', 'closed'])) {
            $query->where('status', $request->status);
        }

        $contacts = $query->latest()->paginate(10)->withQueryString();

        return view('admin.contacts.index', compact('contacts'));
    }

    // Xem chi tiết
    public function show($id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }

    // Xoá mềm liên hệ
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete(); // Xóa mềm
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Xoá liên hệ thành công');
    }

    // Khôi phục liên hệ đã xóa
    public function restore($id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);
        $contact->restore();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Khôi phục liên hệ thành công');
    }

    // Xóa vĩnh viễn liên hệ
    public function forceDelete($id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);
        $contact->forceDelete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Xoá liên hệ vĩnh viễn thành công');
    }

    // Cập nhật trạng thái
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,replied,closed'
        ]);

        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
    }

    // Gửi phản hồi
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);

        // Lưu phản hồi
        $contact->reply_message = $request->reply_message;
        $contact->replied_at = now();
        $contact->status = 'replied';
        $contact->save();

        // Gửi email
        Mail::raw($request->reply_message, function ($message) use ($contact) {
            $message->to($contact->email)
                ->subject('Phản hồi liên hệ từ Website');
        });

        return back()->with('success', 'Phản hồi đã được gửi thành công!');
    }
    public function trash()
    {
        $contacts = Contact::onlyTrashed()->latest()->paginate(10);

        return view('admin.contacts.trash', compact('contacts'));
    }

}
