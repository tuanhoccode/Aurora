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
use Illuminate\Support\Facades\Auth;


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

        // Lấy danh sách sản phẩm yêu thích
        $wishlists = $user->wishlists()->with('product')->get();

        return view('admin.users.show', compact('user', 'orders', 'reviews', 'wishlists'));
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
    // 1. Không cho user tự thay đổi role chính mình
    if (Auth::id() === $user->id) {
        $message = 'Bạn không thể thay đổi vai trò của chính mình.';

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message], 403)
            : back()->with('error', $message);
    }

    // 2. Không cho thay đổi role của user đang là admin
    if ($user->role === 'admin') {
        $message = 'Không được phép thay đổi vai trò của Admin.';

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message], 403)
            : back()->with('error', $message);
    }

    // 3. Validate role gửi lên
    $validated = $request->validate([
        'role' => 'required|in:customer,employee,admin',
    ]);

    try {
        $user->role = $validated['role'];
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật vai trò thành công.']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Cập nhật vai trò thành công.');
    } catch (\Exception $e) {
        $message = 'Có lỗi khi cập nhật vai trò: ' . $e->getMessage();

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message], 500)
            : back()->with('error', $message);
    }
}




public function changeStatus(Request $request, User $user)
{
    // 1. Chặn admin tự khóa chính họ
    if (Auth::id() === $user->id) {
        return back()->with('error', 'Bạn không thể thay đổi trạng thái của chính mình.');
    }

    // 2. Chặn việc thay đổi trạng thái của tài khoản đã là admin
    if ($user->role === 'admin') {
        return back()->with('error', 'Không được phép thay đổi trạng thái của Admin.');
    }

    try {
        $request->validate([
            'status'      => 'required|in:active,inactive',
            'reason_lock' => 'nullable|string|max:255',
        ]);

        $newStatus = $request->input('status');
        $user->status = $newStatus;

        if ($newStatus === 'inactive') {
            $user->reason_lock = $request->input('reason_lock', 'Không rõ lý do');
        } else {
            $user->reason_lock = null;
        }

        $user->save();

        // Nếu vừa chuyển sang inactive thì ép logout
        if ($newStatus === 'inactive') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Cập nhật trạng thái thành công.');
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        return back()->with('error', 'Có lỗi khi cập nhật trạng thái: ' . $e->getMessage());
    }
}




    public function edit($id)
    {
        $user = User::with('address')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

public function update(UpdateUserRequest $request, $id)
{
    // Lấy user cần cập nhật
    $user = User::findOrFail($id);

    // ❌ Không cho phép admin tự sửa chính họ
    if (Auth::id() === $user->id) {
        return back()->with('error', 'Bạn không thể cập nhật thông tin của chính mình.');
    }

    // ❌ Không cho phép cập nhật thông tin Admin khác
    if ($user->role === 'admin') {
        return back()->with('error', 'Không được phép cập nhật thông tin của tài khoản Admin.');
    }

    try {
        $forceLogout = false;

        // Nếu chuyển từ active → inactive → cần đăng xuất
        if ($user->status === 'active' && $request->status === 'inactive') {
            $forceLogout = true;
        }

        // Nếu người dùng yêu cầu đổi mật khẩu
        $changePassword = $request->filled('is_change_password') && $request->filled('password');
        if ($changePassword) {
            $forceLogout = true;
        }

        // Cập nhật thông tin người dùng
        $user->role = $request->role;
        $user->status = $request->status;
        $user->reason_lock = $request->reason_lock;
        $user->is_change_password = $request->input('is_change_password', false);

        // Nếu có yêu cầu đổi mật khẩu thì băm và gán
        if ($changePassword) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Nếu cần đăng xuất thì xóa session
        if ($forceLogout) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    } catch (\Exception $e) {
        return back()->with('error', 'Có lỗi xảy ra khi cập nhật người dùng: ' . $e->getMessage());
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
