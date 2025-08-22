@extends('admin.layouts.app')

@section('title', 'Quản lý tồn kho')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Quản lý tồn kho</h1>

    <form method="GET" class="row g-1 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên sản phẩm" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="filter" class="form-control">
                <option value="">Tất cả loại</option>
                <option value="product" {{ request('filter') == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                <option value="variant" {{ request('filter') == 'variant' ? 'selected' : '' }}>Biến thể</option>
                <option value="stock" {{ request('filter') == 'stock' ? 'selected' : '' }}>Bảng tồn kho</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="stock_level" class="form-control">
                <option value="">Tất cả tồn kho</option>
                <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Thấp (&le;10)</option>
                <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Bình thường (&gt;10)</option>
                <option value="out" {{ request('stock_level') == 'out' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-control">
                <option value="">Sắp xếp theo</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên</option>
                <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Số lượng</option>
                <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Ngày cập nhật</option>
            </select>
        </div>
        {{-- <div class="col-md-1">
            <select name="order" class="form-control">
                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng</option>
                <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Giảm</option>
            </select>
        </div> --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Số lượng tồn</th>
                    <th>Loại</th>
                    <th>Ngày cập nhật</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($allStocks as $stock)
                    @php
                        $rowClass = '';
                        $icon = '';

                        if ($stock['stock'] <= 0) {
                            $rowClass = 'table-danger';
                            $icon = '<i class="bi bi-exclamation-triangle-fill text-danger me-1" title="Hết hàng"></i>';
                        } elseif ($stock['stock'] <= 10) {
                            $rowClass = 'table-warning';
                            $icon = '<i class="bi bi-exclamation-circle-fill text-warning me-1" title="Tồn kho thấp"></i>';
                        }
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $stock['name'] }}</td>
                        <td>{{ $stock['variant_info'] ?? 'Không có' }}</td>
                        <td>{!! $icon !!}{{ $stock['stock'] }}</td>
                        <td>
                            @if($stock['type'] == 'product')
                                <span class="badge bg-success">Sản phẩm</span>
                            @elseif($stock['type'] == 'variant')
                                <span class="badge bg-info">Biến thể</span>
                            @else
                                <span class="badge bg-secondary">Tồn kho</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($stock['updated_at'])->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Không có dữ liệu tồn kho phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($allStocks->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $allStocks->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
