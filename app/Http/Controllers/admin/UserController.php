<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
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
                $data['avatar'] = 'storage/' . $path;
            }

            User::create($data);
            return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm người dùng: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, User $user)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive',
            ]);

            $user->status = $request->status;
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
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'phone_number' => [
                    'required',
                    'string',
                    Rule::unique('users')->ignore($user->id),
                ],
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
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
            ]);

            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->fullname = $request->fullname;
            $user->gender = $request->gender;
            $user->birthday = $request->birthday;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->bank_name = $request->bank_name;
            $user->user_bank_name = $request->user_bank_name;
            $user->bank_account = $request->bank_account;
            $user->reason_lock = $request->reason_lock;
            $user->is_change_password = $request->input('is_change_password', $user->is_change_password);

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $user->avatar))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar));
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = 'storage/' . $path;
            }

            $user->save();

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