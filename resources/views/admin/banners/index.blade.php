@extends('admin.layouts.app')

@section('title', 'Quản lý Banner')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Banner</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Thêm Banner
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Banners Table --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Thứ tự</th>
                            <th>Link</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th width="100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr>
                            <td>
                                <img src="{{ $banner->image_url }}" 
                                     alt="{{ $banner->title }}" 
                                     class="rounded shadow-sm" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">
                                    {{ $banner->title }}
                                </div>
                                @if($banner->subtitle)
                                    <div class="text-muted small">{{ Str::limit($banner->subtitle, 40) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $banner->sort_order }}</span>
                            </td>
                            <td>
                                @if($banner->link)
                                    <span class="text-muted small">{{ Str::limit($banner->link, 30) }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">Không có</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $banner->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-status" 
                                           type="checkbox" 
                                           data-id="{{ $banner->id }}"
                                           {{ $banner->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.banners.show', $banner->id) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.banners.edit', $banner->id) }}"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item text-danger delete-btn" href="#" data-id="{{ $banner->id }}" data-title="{{ $banner->title }}"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-images display-4 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">Không có banner nào</h5>
                                <p class="text-muted">Bắt đầu tạo banner đầu tiên của bạn</p>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Thêm Banner
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($banners->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Xác nhận xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa banner "<strong id="banner-title"></strong>"?</p>
                <p class="text-muted small mb-0">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Hủy
                </button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle status
    $('.toggle-status').change(function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const checkbox = $(this);
        
        $.ajax({
            url: `/admin/banners/${id}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error('Có lỗi xảy ra!');
                    checkbox.prop('checked', !isChecked);
                }
            },
            error: function(xhr) {
                toastr.error('Có lỗi xảy ra!');
                // Revert checkbox
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    // Delete banner
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const title = $(this).data('title');
        
        $('#banner-title').text(title);
        $('#delete-form').attr('action', `/admin/banners/${id}`);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush 