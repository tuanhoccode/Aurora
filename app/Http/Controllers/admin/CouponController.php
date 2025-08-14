<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CouponRequest;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('is_active', $request->status);
        }

        $coupons = $query->latest()->paginate(10)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }



    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(CouponRequest $request)
    {
        Coupon::create($request->validated());
        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã thành công!');
    }


    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật thành công!');
    }



    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Đã xóa mã giảm giá!');
    }

    // Xem các mã đã bị xóa mềm
    public function trash()
    {
        $coupons = Coupon::onlyTrashed()->paginate(10);
        return view('admin.coupons.trash', compact('coupons'));
    }

    // Khôi phục
    public function restore($id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->restore();
        return redirect()->route('admin.coupons.index')->with('success', 'Khôi phục mã thành công!');
    }

    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->forceDelete();
        return redirect()->route('admin.coupons.trash')->with('success', 'Đã xóa vĩnh viễn mã!');
    }

    // Xóa hàng loạt
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->input('ids_json'), true);
        if (!empty($ids)) {
            Coupon::whereIn('id', $ids)->delete();
            return back()->with('success', 'Đã xóa các mã giảm giá đã chọn!');
        }
        return back()->with('error', 'Không có mã nào được chọn để xóa.');
    }


    // Khôi phục hàng loạt
    public function bulkRestore(Request $request)
    {
        $ids = json_decode($request->input('ids_json'), true);
        if (!empty($ids)) {
            Coupon::onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', 'Đã khôi phục các mã đã chọn!');
        }
        return back()->with('error', 'Không có mã nào được chọn để khôi phục.');
    }

    // Xóa vĩnh viễn hàng loạt
    public function bulkForceDelete(Request $request)
    {
        $ids = json_decode($request->input('ids_json'), true);
        if (!empty($ids)) {
            Coupon::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return back()->with('success', 'Đã xóa vĩnh viễn các mã đã chọn!');
        }
        return back()->with('error', 'Không có mã nào được chọn để xóa vĩnh viễn.');
    }
}
