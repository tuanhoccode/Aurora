@extends('client.layouts.default')

@section('title', 'Hoàn tất thanh toán')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Hiển thị thông báo flash -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <!-- Thông báo hoàn tất -->
            <h1 class="text-2xl font-bold text-green-600 mb-4">Đơn hàng của bạn đã được đặt thành công!</h1>
            <p class="text-gray-700 mb-6">Cảm ơn bạn đã mua sắm với chúng tôi. Dưới đây là thông tin chi tiết về đơn hàng của bạn.</p>

            <!-- Thông tin đơn hàng -->
            <div class="border-t border-gray-200 pt-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Thông tin đơn hàng</h2>
                <div class="space-y-2">
                    <p><span class="font-medium">Mã đơn hàng:</span> {{ $order->code }}</p>
                    <p><span class="font-medium">Tên khách hàng:</span> {{ $order->fullname }}</p>
                    <p><span class="font-medium">Email:</span> {{ $order->email }}</p>
                    <p><span class="font-medium">Số điện thoại:</span> {{ $order->phone_number }}</p>
                    <p><span class="font-medium">Địa chỉ:</span> {{ $order->address }}</p>
                    <p><span class="font-medium">Tổng tiền:</span> {{ number_format($order->total_amount, 0, ',', '.') }} VND</p>
                    <p><span class="font-medium">Phương thức thanh toán:</span> {{ $order->payment_id == 1 ? 'Thanh toán khi nhận hàng (COD)' : 'Thanh toán qua VNPay' }}</p>
                    <p><span class="font-medium">Trạng thái thanh toán:</span> {{ $order->is_paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}</p>
                    @if ($order->note)
                        <p><span class="font-medium">Ghi chú:</span> {{ $order->note }}</p>
                    @endif
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Chi tiết sản phẩm</h2>
                <div class="overflow-x-auto">
                    @if ($order->orderItems && $order->orderItems->isNotEmpty())
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-2 border">Sản phẩm</th>
                                    <th class="p-2 border">Số lượng</th>
                                    <th class="p-2 border">Đơn giá</th>
                                    <th class="p-2 border">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td class="p-2 border">
                                            {{ $item->product ? $item->product->name : $item->name }}
                                            @if ($item->product_variant_id && $item->productVariant)
                                                ({{ $item->productVariant->name }})
                                            @endif
                                        </td>
                                        <td class="p-2 border">{{ $item->quantity }}</td>
                                        <td class="p-2 border">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                        <td class="p-2 border">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-600">Không có sản phẩm nào trong đơn hàng.</p>
                    @endif
                </div>
            </div>

            <!-- Nút điều hướng -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Quay về trang chủ</a>
                <a href="{{ route('client.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Xem lịch sử đơn hàng</a>
            </div>
        </div>
    </div>
@endsection
