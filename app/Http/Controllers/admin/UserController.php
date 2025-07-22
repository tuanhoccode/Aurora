<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreUserRequest;
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

            $user = User::create($data);

            \App\Models\UserAddress::create([
                'user_id' => $user->id,
                'address' => $request->input('address'),
                'phone_number' => $request->input('address_phone'),
                'fullname' => $request->input('address_name'),
                'is_default' => 1,
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm người dùng: ' . $e->getMessage());
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

    public function update(Request $request, $id)
    {
        try {
            $user = User::with('address')->findOrFail($id);

            $request->validate([
                'phone_number' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
                'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
                'fullname' => 'nullable|string|max:100',
                'avatar' => 'nullable|image|max:2048',
                'gender' => 'nullable|in:male,female,other',
                'birthday' => 'nullable|date',
                'role' => 'required|in:customer,employee,admin',
                'status' => 'required|in:active,inactive',
                'bank_name' => 'nullable|string|max:255',
                'user_bank_name' => 'nullable|string|max:255',
                'bank_account' => 'nullable|string|max:255',
                'reason_lock' => 'nullable|string|max:255',
                'is_change_password' => 'nullable|boolean',
                'address' => 'nullable|string|max:255',
                'address_phone' => 'nullable|string|max:20',
                'address_name' => 'nullable|string|max:100',
            ]);

            $user->update([
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'fullname' => $request->fullname,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'role' => $request->role,
                'status' => $request->status,
                'bank_name' => $request->bank_name,
                'user_bank_name' => $request->user_bank_name,
                'bank_account' => $request->bank_account,
                'reason_lock' => $request->reason_lock,
                'is_change_password' => $request->input('is_change_password', false),
            ]);

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $user->avatar))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar));
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
                $user->save();
            }

            // Cập nhật hoặc tạo địa chỉ nếu có dữ liệu
            if ($request->filled('address') || $request->filled('address_phone') || $request->filled('address_name')) {
                $user->address()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'address' => $request->input('address'),
                        'phone_number' => $request->input('address_phone'),
                        'fullname' => $request->input('address_name'),
                        'is_default' => 1,
                    ]
                );
            }

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage());
        }
    }
}
