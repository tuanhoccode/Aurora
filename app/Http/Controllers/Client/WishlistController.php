<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();
        $productId = $request->input('product_id');

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        if (!$wishlist->wasRecentlyCreated) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách yêu thích.']);
        }

        return response()->json(['message' => 'Đã thêm vào danh sách yêu thích.']);
    }


    public function index()
    {
        $wishlists = Wishlist::with('product')->where('user_id', Auth::id())->paginate(8);
        return view('client.wishlist.index', compact('wishlists'));
    }

    public function destroy($id)
    {
        Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Đã xóa khỏi yêu thích');
    }
}
