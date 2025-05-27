<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Hiển thị danh sách sản phẩm
    public function index()
    {
        return view('admin.products.index');
    }

    // Hiển thị form thêm sản phẩm
    public function create()
    {
        return view('admin.products.create');
    }

     public function show()
    {
        return view('admin.products.show');
    }

    // Hiển thị form chỉnh sửa sản phẩm
    public function edit()
    {
        // Với dữ liệu tĩnh, bạn không cần lấy sản phẩm từ DB
        return view('admin.products.edit');
    }
}

