@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Thêm giá trị thuộc tính</h1>
                <p class="text-muted mt-1">
                    Thêm giá trị mới cho thuộc tính {{ $attribute->name }} {{ $attribute->is_variant ? '(Biến thể)' : '' }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-secondary shadow-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Main Form --}}
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card shadow-sm rounded-3 border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.attribute_values.store', $attribute->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="value" class="form-label small fw-bold text-muted mb-1">Giá trị thuộc tính</label>
                                        <input type="text" 
                                               name="value" 
                                               id="value" 
                                               class="form-control rounded-3 @error('value') is-invalid @enderror" 
                                               value="{{ old('value') }}" 
                                               placeholder="Nhập giá trị thuộc tính">
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="color_code" class="form-label small fw-bold text-muted mb-1">Mã màu (Hex)</label>
                                        <div class="d-flex align-items-center">
                                            <div id="color_picker" class="me-3"></div>
                                            <input type="hidden" 
                                                   name="color_code" 
                                                   id="color_code" 
                                                   class="form-control rounded-3 @error('color_code') is-invalid @enderror" 
                                                   value="{{ old('color_code', '#000000') }}" 
                                                   required>
                                            @error('color_code')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Nhấn vào ô màu để chọn từ bảng màu</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="is_active" class="form-label small fw-bold text-muted mb-1">Trạng thái</label>
                                        <select name="is_active" 
                                                id="is_active" 
                                                class="form-select rounded-3 @error('is_active') is-invalid @enderror">
                                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                                            <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="text-secondary my-4">

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">
                                    <i class="bi bi-save me-1"></i> Lưu giá trị
                                </button>
                                <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-light rounded-pill px-4 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color_code');
        
        const pickr = Pickr.create({
            el: '#color_picker',
            theme: 'classic',
            default: colorInput.value || '#000000',
            swatches: [
                '#000000', '#ffffff', '#ff0000', '#00ff00', '#0000ff',
                '#ffff00', '#00ffff', '#ff00ff', '#ff8000', '#800080',
                '#008000', '#000080', '#800000', '#808000', '#008080',
                '#808080', '#c0c0c0', '#ffc0cb', '#ffa500', '#a52a2a'
            ],
            components: {
                preview: true,
                opacity: true,
                hue: true,
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: false,
                    save: true
                }
            }
        });

        // Khi chọn màu
        pickr.on('save', (color, instance) => {
            const hexColor = color.toHEXA().toString();
            colorInput.value = hexColor;
            pickr.hide();
        });

        // Khởi tạo giá trị ban đầu
        if (colorInput.value) {
            pickr.setColor(colorInput.value);
        }
    });
</script>
@endpush