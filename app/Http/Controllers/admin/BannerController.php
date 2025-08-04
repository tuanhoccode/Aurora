<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        // Tìm kiếm theo tiêu đề
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('subtitle', 'like', '%' . $request->search . '%');
        }

        // Lọc theo vị trí
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $banners = $query->orderBy('created_at', 'desc')
                        ->orderBy('sort_order', 'asc')
                        ->paginate(10);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $usedSortOrders = Banner::pluck('sort_order')->toArray();
        $nextSortOrder = Banner::getNextAvailableSortOrder();
        
        return view('admin.banners.create', compact('usedSortOrders', 'nextSortOrder'));
    }

    public function store(BannerRequest $request)
    {
        $data = $request->validated();
        
        // Tự động gán sort_order nếu không được cung cấp
        if (empty($data['sort_order'])) {
            $data['sort_order'] = Banner::getNextAvailableSortOrder();
        }
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công!');
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.show', compact('banner'));
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(BannerRequest $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Xóa ảnh
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công!');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái banner đã được cập nhật!',
            'is_active' => $banner->is_active
        ]);
    }
} 