@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('content')
    <!-- CSS nhanh: bạn có thể chuyển vào file CSS chung nếu muốn -->
    <style>
        .scroll-5 { max-height: 320px; overflow-y: auto; }
        .scroll-5 table thead th { position: sticky; top: 0; background: #ffffff; z-index: 2; }
        /* Reviews table alignment */
        .table-reviews td,
        .table-reviews th {
            vertical-align: middle;
        }

        .table-reviews thead th.col-rating,
        .table-reviews thead th.col-status {
            text-align: center;
        }

        .table-reviews thead th.col-date {
            text-align: right;
        }

        .table-reviews .col-product {
            width: 22%;
        }

        .table-reviews .col-rating {
            width: 110px;
            text-align: center;
            white-space: nowrap;
        }

        .table-reviews .col-content {
            width: auto;
            line-height: 1.6;
            word-break: break-word;
        }

        .table-reviews .col-status {
            width: 120px;
            text-align: center;
            white-space: nowrap;
        }

        .table-reviews .col-date {
            width: 140px;
            white-space: nowrap;
            text-align: right;
        }

        .table-reviews .stars i {
            margin-right: 2px;
        }

        /* Scroll containers showing ~5 rows */
        .scroll-5 {
            max-height: 320px;
            overflow-y: auto;
        }

        .scroll-5 table thead th {
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 2;
        }
    </style>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Bảng điều khiển</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">Chi tiết người dùng</h1>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        @php
            $totalOrders = is_countable($orders) ? count($orders) : 0;
            $totalReviews = is_countable($reviews) ? count($reviews) : 0;
            $totalWishlists = is_countable($wishlists) ? count($wishlists) : 0;
            $totalSpent = collect($orders ?? [])->sum('total_amount');
        @endphp

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="text-muted small">Tổng đơn hàng</div>
                        <div class="h5 mb-0">{{ $totalOrders }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="text-muted small">Tổng chi tiêu</div>
                        <div class="h5 mb-0">{{ number_format((float) $totalSpent, 0, ',', '.') }} ₫</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="text-muted small">Đánh giá</div>
                        <div class="h5 mb-0">{{ $totalReviews }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="text-muted small">Yêu thích</div>
                        <div class="h5 mb-0">{{ $totalWishlists }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Cột trái -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://via.placeholder.com/100' }}"
                            class="rounded-circle mb-3 border" width="100" height="100" alt="Avatar">
                        <h5 class="mb-1">{{ $user->fullname }}</h5>
                        <div class="text-muted small">Tham gia {{ $user->created_at->diffForHumans() }}</div>
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
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Đơn hàng ({{ is_countable($orders) ? count($orders) : 0 }})</span>
                        <span class="text-muted small">Tổng chi tiêu: {{ number_format((float) $totalSpent, 0, ',', '.') }}
                            ₫</span>
                    </div>

                    <div class="table-responsive scroll-5">
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
                                        <td><span class="text-body">#{{ $order->code }}</span></td>
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
                    <div class="table-responsive scroll-5">
                        <table class="table table-sm mb-0 table-reviews">
                            <thead class="table-light">
                                <tr>
                                    <th class="col-product">Sản phẩm</th>
                                    <th class="col-rating">Đánh giá</th>
                                    <th class="col-content">Nội dung</th>
                                    <th class="col-status">Trạng thái</th>
                                    <th class="col-date">Ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews ?? [] as $review)
                                    @php /** @var \App\Models\Review $review */ @endphp
                                    <tr>
                                        <td class="col-product">{{ $review->product->name }}</td>
                                        <td class="col-rating">
                                            <span class="stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </span>
                                        </td>
                                        <td class="col-content text-break">{{ $review->content }}</td>
                                        <td class="col-status">
                                            @if ($review->is_active)
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa duyệt</span>
                                            @endif
                                        </td>
                                        <td class="col-date">{{ $review->created_at->format('d/m/Y') }}</td>
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

                    <div class="table-responsive scroll-5">
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
                                            <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}"
                                                target="_blank">
                                                {{ $wishlist->product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @php
                                                $product = $wishlist->product;

                                                if ($product->type === 'variant' && $product->variants->count() > 0) {
                                                    $regularMin = $product->variants->min('regular_price');
                                                    $regularMax = $product->variants->max('regular_price');

                                                    $saleMin = $product->variants->where('sale_price', '>', 0)->min('sale_price');
                                                    $saleMax = $product->variants->where('sale_price', '>', 0)->max('sale_price');

                                                    if ($saleMin && $saleMax) {
                                                        // Có giảm giá trong biến thể
                                                        echo '<span class="text-danger fw-bold">';
                                                        echo ($saleMin == $saleMax)
                                                            ? number_format($saleMin, 0, ',', '.') . ' ₫'
                                                            : number_format($saleMin, 0, ',', '.') . ' ₫ - ' . number_format($saleMax, 0, ',', '.') . ' ₫';
                                                        echo '</span><br>';

                                                        echo '<span class="text-muted text-decoration-line-through">';
                                                        echo ($regularMin == $regularMax)
                                                            ? number_format($regularMin, 0, ',', '.') . ' ₫'
                                                            : number_format($regularMin, 0, ',', '.') . ' ₫ - ' . number_format($regularMax, 0, ',', '.') . ' ₫';
                                                        echo '</span>';
                                                    } else {
                                                        // Không giảm giá
                                                        echo '<span>';
                                                        echo ($regularMin == $regularMax)
                                                            ? number_format($regularMin, 0, ',', '.') . ' ₫'
                                                            : number_format($regularMin, 0, ',', '.') . ' ₫ - ' . number_format($regularMax, 0, ',', '.') . ' ₫';
                                                        echo '</span>';
                                                    }
                                                } else {
                                                    // Sản phẩm thường
                                                    $regular = $product->price;
                                                    $sale = $product->sale_price > 0 ? $product->sale_price : 0;

                                                    if ($sale > 0 && $sale < $regular) {
                                                        echo '<span class="text-danger fw-bold">'
                                                            . number_format($sale, 0, ',', '.') . ' ₫</span><br>';
                                                        echo '<span class="text-muted text-decoration-line-through">'
                                                            . number_format($regular, 0, ',', '.') . ' ₫</span>';
                                                    } else {
                                                        echo '<span>' . number_format($regular, 0, ',', '.') . ' ₫</span>';
                                                    }
                                                }
                                            @endphp

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