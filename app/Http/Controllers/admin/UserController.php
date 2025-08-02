<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }


    public function show(User $user)
    {
        $user->load('address');

        $orders = \App\Models\Order::where('user_id', $user->id)->get()->map(function ($order) {
            $order->payment_status_badge = $order->is_paid
                ? '<span class="badge bg-success">Đã thanh toán</span>'
                : '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';

            if ($order->is_refunded) {
                $order->fulfilment_status_badge = '<span class="badge bg-danger">Đã hoàn tiền</span>';
            } elseif ($order->is_refunded_canceled) {
                $order->fulfilment_status_badge = '<span class="badge bg-secondary">Đã hủy</span>';
            } else {
                $order->fulfilment_status_badge = '<span class="badge bg-info">Đang xử lý</span>';
            }

            return $order;
        });

        $reviews = \App\Models\Review::where('user_id', $user->id)->get()->map(function ($review) {
            $review->product_name = 'Sản phẩm #' . $review->product_id;
            $review->stars = str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);
            $review->content = $review->review_text;
            return $review;
        });

        return view('admin.users.show', compact('user', 'orders', 'reviews'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

public function store(StoreUserRequest $request)
{
    try {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_change_password'] = 0;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = $path;
        }

        //Tạo user
        $user = User::create($data);

        //Gửi email xác thực ngay lập tức
        $user->sendEmailVerificationNotification();

        //Chỉ tạo địa chỉ khi admin thực sự nhập đầy đủ
        if (
            $request->filled('address')
            && $request->filled('address_phone')
            && $request->filled('fullname_address') 
        ) {
            \App\Models\UserAddress::create([
                'user_id'      => $user->id,
                'address'      => $request->input('address'),
                'phone_number' => $request->input('address_phone'),
                'fullname'     => $request->input('fullname_address'),
                'is_default'   => 1,
            ]);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', 'Thêm người dùng thành công.');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Có lỗi xảy ra khi thêm người dùng: ' . $e->getMessage());
    }
}


    public function changeRole(Request $request, User $user)
    {
        try {
            $request->validate([
                'role' => 'required|in:customer,employee,admin',
            ]);

            $user->role = $request->role;
            $user->save();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò thành công.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật vai trò: ' . $e->getMessage());
        }
    }


    public function changeStatus(Request $request, User $user)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive',
                'reason_lock' => 'nullable|string|max:255',  // Thêm validate lý do khóa
            ]);

            $user->status = $request->status;

            if ($user->status === 'inactive') {
                // Gán lý do khóa nếu có
                $user->reason_lock = $request->input('reason_lock', 'Không rõ lý do');
                // Xóa session để ép logout ngay
                DB::table('sessions')->where('user_id', $user->id)->delete();
            } else {
                // Mở khóa thì xoá lý do khóa
                $user->reason_lock = null;
            }

            $user->save();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật trạng thái thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $user = User::with('address')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            // Lấy thông tin người dùng từ database, kèm theo kiểm tra tồn tại
            $user = User::findOrFail($id);

            $forceLogout = false; // Biến dùng để xác định có cần đăng xuất người dùng hay không

            // Nếu trạng thái thay đổi từ "active" sang "inactive", đánh dấu cần đăng xuất
            if ($user->status === 'active' && $request->status === 'inactive') {
                $forceLogout = true;
            }

            // Nếu có yêu cầu đổi mật khẩu, cũng cần đăng xuất
            if ($request->filled('is_change_password') && $request->filled('password')) {
                $forceLogout = true;
            }

            // Cập nhật các trường cơ bản
            $user->update([
                'role' => $request->role,
                'status' => $request->status,
                'reason_lock' => $request->reason_lock,
                'is_change_password' => $request->input('is_change_password', false),
            ]);

            // Nếu có yêu cầu đổi mật khẩu, thực hiện băm và lưu mật khẩu mới
            if ($request->filled('is_change_password') && $request->filled('password')) {
                $user->password = Hash::make($request->password);
                $user->save(); // Lưu lại mật khẩu mới
            }

            // Nếu cần đăng xuất, xóa hết session của user này
            if ($forceLogout) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
        } catch (\Exception $e) {
            // Trả về thông báo lỗi nếu có exception xảy ra
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật người dùng: ' . $e->getMessage());
        }
    }



public function destroy(Request $request, User $user)
{
    try {
        $user->delete();

        $currentPage = $request->input('page', 1);
        $queryParams = $request->except('_token', '_method');

        $perPage = 10;
        $totalRecords = User::count();
        $lastPage = (int) ceil($totalRecords / $perPage);

        if ($currentPage > $lastPage && $lastPage > 0) {
            $currentPage = $lastPage;
        }

        return redirect()->route('admin.users.index', array_merge($queryParams, ['page' => $currentPage]))
            ->with('success', 'Xóa tài khoản thành công.');
    } catch (\Illuminate\Database\QueryException $e) {
        // Kiểm tra xem có phải lỗi khóa ngoại không (mã lỗi 1451)
        if ($e->getCode() == '23000' && str_contains($e->getMessage(), '1451')) {
            return redirect()->back()->with('error', 'Không thể xóa tài khoản này vì tài khoản đã phát sinh giao dịch hoặc dữ liệu liên quan.');
        }
        return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage());
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage());
    }
}

}
