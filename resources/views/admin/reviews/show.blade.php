@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content') 
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết bình luận</h1>
        </div>
        <div>
            <a href="{{ route('admin.reviews.comments') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
         
        <div class="col-lg-12">
            <div class="card shadow-sm mb-12">
                <div class="card-header py-3 d-flex align-items-center">
                    <h5 class="card-title mb-0">Thông tin bình luận</h5>  
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Khách hàng:</th>
                                    <td><strong>{{ $review->user->fullname }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Sản phẩm:</th>
                                    <td>{{ $review->product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Bình luận:</th>
                                    <td><code>{{$review->review_text}}</code></td>
                                </tr>
                                <tr>
                                    <th>Số sao: </th>
                                    <td>{{$review->rating}}⭐</td>
                                </tr>
                                @if($review->images && $review->images->count())
                                    <tr>
                                        <th>Ảnh đính kèm:</th>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($review->images as $img)
                                                    <a href="{{asset('storage/' . $img->image_path)}}" target="_blank" class="d-inline-block">
                                                        <img src="{{asset('storage/' . $img->image_path)}}" alt="Ảnh đánh giá"
                                                        class="img-thumbnail" style="width:110px; height:110px; object-fit:cover; border-radius:10px;" >
                                                    </a>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                               
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                    @if ($review->is_active === 1)
                                        <span class="badge d-inline-block text-center bg-success w-10" style="min-width: 10px;">
                                        <i class="fas fa-check-circle me-1"></i> Đã duyệt
                                        </span>
                                    @elseif ($review->is_active === 0 && $review->reason)
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
                                    <th>Phản hồi của </th>
                                    <td>
                                        @if($review->replies && $review->replies->count())
                                            @foreach($review->replies as $reply)
                                                <strong>Admin:</strong> {{$reply->review_text}}
                                                <small class="text-muted">({{ $reply->created_at->format('d/m/Y H:i') }})</small>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Chưa phản hồi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lý do:</th>
                                    <td>{{$review->reason}}</td>
                                </tr>
                            </table>
                        </div>
                        
                    </div>
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
                <p>Bạn có chắc chắn muốn chuyển sản phẩm <strong>{{ $review->content }}</strong> vào thùng rác?</p>
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
