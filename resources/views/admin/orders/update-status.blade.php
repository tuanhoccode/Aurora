@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm rounded mb-4">
        <div class="card-header bg-light fw-bold">Cập nhật trạng thái đơn hàng</div>
        <div class="card-body">
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="order_status_id" class="form-label">Trạng thái đơn hàng</label>
                    <select name="order_status_id" id="order_status_id" class="form-control">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ $currentStatus && $currentStatus->order_status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_status_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="is_paid" class="form-label">Trạng thái thanh toán</label>
                    <select name="is_paid" id="is_paid" class="form-control">
                        <option value="1" {{ $order->is_paid ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="0" {{ !$order->is_paid ? 'selected' : '' }}>Chờ thanh toán</option>
                    </select>
                    @error('is_paid')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea name="note" id="note" class="form-control">{{ old('note', $currentStatus?->note) }}</textarea>
                    @error('note')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="employee_evidence" class="form-label">Minh chứng của nhân viên</label>
                    <input type="text" name="employee_evidence" id="employee_evidence" class="form-control" value="{{ old('employee_evidence', $currentStatus?->employee_evidence) }}">
                    @error('employee_evidence')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="customer_confirmation" class="form-label">Xác nhận của khách hàng</label>
                    <select name="customer_confirmation" id="customer_confirmation" class="form-control">
                        <option value="1" {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 1 ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="0" {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 0 ? 'selected' : '' }}>Chưa xác nhận</option>
                    </select>
                    @error('customer_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection