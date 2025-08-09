@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Thêm tồn kho mới</h5>
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded">
                            <i class="mdi mdi-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm rounded mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                       <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" 
                              action="{{ route('admin.stocks.store') }}" 
                              id="createStockForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="product_id" class="form-label fw-bold">Sản phẩm <span class="text-danger">*</span></label>
                                    <select name="product_id" 
                                            id="product_id" 
                                            class="form-select @error('product_id') is-invalid @enderror">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="stock" class="form-label fw-bold">Số lượng tồn kho (tối đa 100) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="stock" 
                                           id="stock" 
                                           class="form-control @error('stock') is-invalid @enderror" 
                                           value="{{ old('stock') }}" 
                                           min="0" 
                                           max="100"
                                           placeholder="Nhập số lượng tồn kho">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-plus-circle me-1"></i> Thêm mới
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for product selection
        $('#product_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Chọn sản phẩm'
        });
    });
</script>
@endpush
