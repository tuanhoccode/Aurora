@extends('admin.layouts.app')

@section('title', 'Quản lý Banner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Quản lý Banner</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm Banner
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="100">Ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th width="120">Vị trí</th>
                                    <th width="80">Thứ tự</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="150">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banners as $banner)
                                    <tr>
                                        <td>{{ $banner->id }}</td>
                                        <td>
                                            <img src="{{ $banner->image_url }}" 
                                                 alt="{{ $banner->title }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 80px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong>{{ $banner->title }}</strong>
                                            @if($banner->subtitle)
                                                <br><small class="text-muted">{{ $banner->subtitle }}</small>
                                            @endif
                                            @if($banner->link)
                                                <br><small class="text-muted">Link: {{ $banner->link }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $banner->position == 'slider' ? 'primary' : ($banner->position == 'banner' ? 'success' : 'warning') }}">
                                                {{ ucfirst($banner->position) }}
                                            </span>
                                        </td>
                                        <td>{{ $banner->sort_order }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-status" 
                                                       type="checkbox" 
                                                       data-id="{{ $banner->id }}"
                                                       {{ $banner->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>{{ $banner->created_at->format('d/m/Y H:i') }}</td>
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
                                        <td colspan="8" class="text-center">Không có banner nào</td>
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
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa banner "<span id="banner-title"></span>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
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
        
        $.ajax({
            url: `/admin/banners/${id}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra!');
                // Revert checkbox
                $(this).prop('checked', !isChecked);
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