@extends('admin.layouts.app')

@section('content')
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form cập nhật trạng thái --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Cập nhật trạng thái đơn hàng #{{ $order->code }}</h5>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="order_status_id" class="form-label">Trạng thái đơn hàng</label>
                    <select name="order_status_id" id="order_status_id" class="form-select">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('order_status_id', $currentStatus?->order_status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="is_paid" class="form-label">Trạng thái thanh toán</label>
                    <select name="is_paid" id="is_paid" class="form-select">
                        <option value="0" {{ old('is_paid', $order->is_paid) == 0 ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="1" {{ old('is_paid', $order->is_paid) == 1 ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea name="note" id="note" rows="3" class="form-control">{{ old('note') }}</textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lịch sử trạng thái --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Lịch sử trạng thái đơn hàng</h5>

            @if ($order->statusHistory->isEmpty())
                <p class="text-muted">Chưa có lịch sử trạng thái.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>ID</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                                <th>Người cập nhật</th>
                                <th>Thời gian</th>
                                <th>Hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->statusHistory as $status)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $status->id }}</td>
                                    <td>{{ $status->status->name }}</td>
                                    <td>{{ $status->note ?? 'Không có ghi chú' }}</td>
                                    <td>{{ $status->modifier?->name ?? 'Hệ thống' }}</td>
                                    <td>{{ $status->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        @if ($status->is_current)
                                            <span class="badge bg-success">Hiện tại</span>
                                        @else
                                            <span class="text-muted">Cũ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection