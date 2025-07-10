@extends('admin.layouts.app')

@section('title', 'Quản lý bình luận')

@section('content')
<style>
   .error {
      color: #dc3545;
      /* Màu đỏ Bootstrap */
      font-size: 0.9rem;
      margin-top: 5px;
   }
</style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý bình luận</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reviews.trashComments') }}" class="btn btn-outline-secondary">
                <i class="bi bi-trash"></i>
                Thùng rác @if ($trashComments > 0)
                    <span class="badge bg-danger">{{ $trashComments }}</span>
                @endif
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    {{-- Search --}}
    <div class="card shadow-sm rounded-3 border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Tìm kiếm sản phẩm...">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                            <tr>
                            
                                
                                <th>Người dùng </th>
                                <th>Sản phẩm</th>
                                <th>Nội dung</th>
                                <th>Đánh giá</th>
                                <th>Trạng thái</th>
                                <th>Lý do</th>
                                <th>Ngày bình luận</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mergedList as $comment)
                                <tr>
                                    
                                    <td>
                                        {{ $comment->user ? $comment->user->fullname : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $comment->product ? $comment->product->name : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $comment->content }}
                                    </td>
                                    <td>
                                        @if($comment->type === 'review' && $comment->rating !== null)
                                            {!! str_repeat('<i class="fa fa-star text-warning"></i>', $comment->rating) !!}
                                            {!! str_repeat('<i class="fa fa-star text-muted"></i>', 5 - $comment->rating) !!}
                                            <br>
                                            
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if ($comment->is_active === 1)
                                            <span class="badge d-inline-block text-center bg-success w-100" style="min-width: 110px;">
                                            <i class="fas fa-check-circle me-1"></i> Đã duyệt
                                            </span>
                                        @elseif ($comment->is_active === 0 && $comment->reason)
                                            <span class="badge d-inline-block text-center bg-danger w-100" style="min-width: 110px;">
                                                <i class="fas fa-times-circle me-1"></i> Không duyệt
                                            </span>
                                        @else
                                            <span class="badge d-inline-block text-center bg-warning text-dark w-100" style="min-width: 110px;">
                                                <i class="fas fa-clock me-1"></i> Chờ duyệt
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{$comment->reason}}
                                    </td>
                                    <td>
                                        {{$comment->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i')}}
                                    </td>
                                    <!-- <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-comment"
                                                data-id="{{ $comment->id }}" {{ $comment->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td> -->
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.reviews.showComment', ['type' => $comment->type, 'id' => $comment->id]) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            {{-- Nút mở modal từ chối --}}
                                            @if (!$comment->is_active)
                                            {{-- Nút chấp nhận --}}
                                            <form action="{{ route('admin.reviews.approve', ['type' => $comment->type, 'id' => $comment->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> {{-- Chấp nhận --}}
                                                </button>
                                            </form>
                                            @else
                                            {{-- Nút mở modal từ chối --}}
                                            <button type="button"
                                                class="btn btn-sm btn-warning btn-reject"
                                                data-id="{{ $comment->id }}"
                                                data-type="{{ $comment->type }}"
                                                data-user="{{ $comment->user ? $comment->user->fullname : 'N/A' }}"
                                                data-content="{{ $comment->content }}">
                                                <i class="bi bi-x-circle"></i> {{-- Từ chối --}}
                                            </button>
                                            @endif
                                            <form action="{{route('admin.reviews.destroyComment', ['type' => $comment->type, 'id' => $comment->id])}}" method="post"  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-button"
                                                data-type= "{{$comment->type}}"
                                                onclick="return false;"
                                                >
                                                    
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Hiển thị {{ $mergedList->firstItem() }} đến {{ $mergedList->lastItem() }} trong tổng số {{ $mergedList->total() }}
                        sản phẩm
                    </div>
                    <div>
                        {{ $mergedList->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="deletecommentName"></strong>?</p>
                        <p class="mb-0 text-danger">Hành động này không thể hoàn tác!</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteForm" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i>
                                Xóa
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
                // Select All
                $('#selectAll').change(function() {
                    $('.product-checkbox').prop('checked', $(this).prop('checked'));
                    toggleBulkActions();
                });

                // Individual checkbox
                $('.product-checkbox').change(function() {
                    toggleBulkActions();
                });

                // Toggle bulk actions
                function toggleBulkActions() {
                    if ($('.product-checkbox:checked').length > 0) {
                        $('.bulk-actions').slideDown();
                    } else {
                        $('.bulk-actions').slideUp();
                    }
                }

                // Cancel bulk actions
                $('.cancel-bulk').click(function() {
                    $('.product-checkbox, #selectAll').prop('checked', false);
                    toggleBulkActions();
                });

                // Bulk actions
                $('.bulk-action').click(function() {
                    const action = $(this).data('action');
                    const ids = $('.product-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get();

                    if (action === 'delete') {
                        if (!confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')) {
                            return;
                        }
                    }

                    let url = '';
                    let method = 'POST';
                    let data = {
                        ids
                    };

                    

                    // Send request
                    $.ajax({
                        url: url,
                        method: method,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                        }
                    });
                });

                // Toggle status
                $('.toggle-status').change(function() {
                    const id = $(this).data('id');
                    const status = $(this).prop('checked') ? 1 : 0;

                    $.ajax({
                        url: '{{ route('admin.products.bulk-toggle-status') }}',
                        method: 'POST',
                        data: {
                            ids: [id],
                            status: status
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        error: function(xhr) {
                            alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                            // Revert the checkbox
                            $(this).prop('checked', !status);
                        }
                    });
                });

                // Delete confirmation
                $('.delete-product').click(function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');

                    $('#deleteProductName').text(name);
                    $('#deleteForm').attr('action', `/admin/products/${id}`);
                    $('#deleteModal').modal('show');
                });
                $('.btn-reject').click(function () {
                    const id = $(this).data('id');
                    const type = $(this).data('type');
                    const user = $(this).data('user');
                    const content = $(this).data('content');

                    const action = `/admin/reviews/reject/${type}/${id}`;
                    $('#rejectForm').attr('action', action);

                    $('#rejectUser').text(user);
                    $('#rejectContent').text(content);
                    $('#reasonInput').val('');

                    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                    modal.show();
                });

            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', function (e) {
                        const type = this.dataset.type;
                        const form = this.closest('form');
                    
                        if (type === 'review') {
                            alert('Không thể xóa đánh giá sản phẩm. Bạn chỉ có thể duyệt hoặc từ chối.');
                        } else if (type === 'comment') {
                            if (confirm('Bạn có chắc chắn muốn xóa mềm bình luận này?')) {
                                form.submit();
                            }
                        }
                    });
                });
            });
        </script>
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Từ chối bình luận</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Người dùng:</strong> <span id="rejectUser"></span></p>
                    <p><strong>Nội dung:</strong> <span id="rejectContent"></span></p>
                    <div class="mb-3">
                        <label for="reasonInput" class="form-label">Lý do từ chối:</label>
                        <textarea class="form-control" id="reasonInput" name="reason" rows="3" ></textarea>
                        @error('reason')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </div>
        </form>
    </div>
</div>

    </div>
    @endpush
