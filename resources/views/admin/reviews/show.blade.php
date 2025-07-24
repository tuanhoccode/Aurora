@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content') 
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Chi tiết bình luận</h1>
            <p class="text-muted mt-1">Thông tin chi tiết bình luận/đánh giá sản phẩm</p>
        </div>
        <div>
            <a href="{{ route('admin.reviews.comments') }}" class="btn btn-light rounded-pill shadow-sm"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm rounded mb-4">
                <div class="card-header bg-light fw-bold">Thông tin bình luận</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                                    <th width="150">Khách hàng:</th>
                                    <td><strong>{{ $comment->user->fullname }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Sản phẩm:</th>
                                    <td>{{ $comment->product->name }}</td>
                                </tr>
                                @if($type === 'review')
                                <tr>
                                    <th>Bình luận:</th>
                                    <td><code>{{$comment->review_text}}</code></td>
                                </tr>
                                <tr>
                                    <th>Số sao: </th>
                                    <td>{{$comment->rating}}⭐</td>
                                </tr>
                                @else
                                <tr>
                                    <th>Bình luận:</th>
                                    <td><code>{{ $comment->content }}</code></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                    @if ($comment->is_active === 1)
                                        <span class="badge d-inline-block text-center bg-success w-10" style="min-width: 10px;">
                                        <i class="fas fa-check-circle me-1"></i> Đã duyệt
                                        </span>
                                    @elseif ($comment->is_active === 0 && $comment->reason)
                                        <span class="badge d-inline-block text-center bg-danger w-10" style="min-width: 10px;">
                                            <i class="fas fa-times-circle me-1"></i> Không duyệt
                                        </span>
                                    @else
                                        <span class="badge d-inline-block text-center bg-warning text-dark w-10" style="min-width: 10px;">
                                            <i class="fas fa-clock me-1"></i> Chờ duyệt
                                        </span>
                                    @endif
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>Lý do:</th>
                                    <td>{{$comment->reason}}</td>
                                </tr>
                            </table>
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
                <p>Bạn có chắc chắn muốn chuyển sản phẩm <strong>{{ $comment->content }}</strong> vào thùng rác?</p>
                <p class="mb-0 text-muted">
                    <i class="bi bi-info-circle"></i>
                    Sản phẩm sẽ được chuyển vào thùng rác và có thể khôi phục lại sau.
                </p>
            </div>
            <div class="modal-footer">
                <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Chuyển vào thùng rác
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
