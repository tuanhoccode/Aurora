<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('product')->paginate(10);
        return view('admin.stocks.index', compact('stocks'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.stocks.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0|max:100',
        ], [
            'stock.max' => 'Tồn kho không được vượt quá 100'
        ]);

        Stock::create($request->only('product_id', 'stock'));

        return redirect()->route('admin.stocks.index')->with('success', 'Tồn kho được thêm thành công!');
    }

   public function show($id)
    {
        $stock = Stock::with('product')->findOrFail($id);

        return view('admin.stocks.show', [
            'stock' => $stock,
            'product' => $stock->product, // truyền thêm
            'stocks' => collect([$stock]), // để dùng lại vòng lặp nếu có
        ]);
    }

    public function edit(Stock $stock)
    {
        $products = Product::all();
        return view('admin.stocks.edit', compact('stock', 'products'));
    }

    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0|max:100',
        ], [
            'stock.max' => 'Tồn kho không được vượt quá 100'
        ]);

        $stock->update($request->only('product_id', 'stock'));

        return redirect()->route('admin.stocks.index')->with('success', 'Tồn kho được cập nhật thành công!');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('admin.stocks.index')->with('success', 'Tồn kho đã bị xóa.');
    }
}
