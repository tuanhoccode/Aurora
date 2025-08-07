@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Chi tiết người dùng</h1>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Cột trái -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://via.placeholder.com/100' }}"
                            class="rounded-circle mb-3 border" width="100" height="100">
                        <h5>{{ $user->fullname }}</h5>
                        <p class="text-muted">Tham gia {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Thông tin liên hệ</span>
                    </div>
                    <div class="card-body">
                        {{-- Email --}}
                        <p class="mb-1"><strong>Email</strong></p>
                        @if ($user->email)
                            <p class="mb-3"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
                        @else
                            <p class="text-muted mb-3">Chưa có email</p>
                        @endif

                        {{-- Số điện thoại --}}
                        <p class="mb-1"><strong>Số điện thoại</strong></p>
                        @if ($user->phone_number)
                            <p class="mb-3">{{ $user->phone_number }}</p>
                        @else
                            <p class="text-muted mb-3">Chưa có số điện thoại</p>
                        @endif

                        {{-- Địa chỉ --}}
                        <p class="mb-1"><strong>Địa chỉ</strong></p>
                        @if ($user->address)
                            <p class="mb-1">{{ $user->address->address }}</p>
                        @else
                            <p class="text-muted mb-3">Chưa có địa chỉ</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cột phải -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold">Đơn hàng ({{ is_countable($orders) ? count($orders) : 0 }})</div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Hình thức giao</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders ?? [] as $order)
                                    @php /** @var \App\Models\Order $order */ @endphp
                                    <tr>
                                        <td><a href="#">#{{ $order->id }}</a></td>
                                        <td>{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</td>
                                        <td>{!! $order->payment_status_badge !!}</td>
                                        <td>{!! $order->fulfilment_status_badge !!}</td>
                                        <td>{{ $order->delivery_type_full_info }}</td>
                                        <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header fw-bold">Đánh giá ({{ is_countable($reviews) ? count($reviews) : 0 }})</div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đánh giá</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews ?? [] as $review)
                                    @php /** @var \App\Models\Review $review */ @endphp
                                    <tr>
                                        <td>{{ $review->product_name }}</td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>{{ $review->content }}</td>
                                        <td>
                                            @if ($review->is_active)
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa duyệt</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold">
                        Sản phẩm yêu thích ({{ is_countable($wishlists) ? count($wishlists) : 0 }})
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Ngày thêm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($wishlists as $wishlist)
                                    <tr>
                                        <td>
                                            <img src="{{ $wishlist->product->thumbnail ? asset('storage/' . $wishlist->product->thumbnail) : 'https://via.placeholder.com/50' }}"
                                                 alt="{{ $wishlist->product->name }}" width="50">
                                        </td>
                                        <td>
                                            <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}" target="_blank">
                                                {{ $wishlist->product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ number_format($wishlist->product->price, 0, ',', '.') }} ₫
                                        </td>
                                        <td>
                                            {{ $wishlist->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Chưa có sản phẩm yêu thích</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection