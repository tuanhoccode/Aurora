@extends('admin.layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-5 text-primary fw-bold">Chi tiết Liên hệ</h1>

    <div class="row g-4">
        {{-- Thông tin người gửi --}}
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0 p-3">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-person-circle fs-2 text-primary me-3"></i>
                    <h4 class="mb-0 fw-semibold">Thông tin người gửi</h4>
                </div>

                <ul class="list-unstyled fs-5">
                    <li class="mb-3">
                        <strong>Tên:</strong><br>
                        <span class="text-secondary">{{ $contact->name }}</span>
                    </li>
                    <li class="mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-secondary">{{ $contact->email ?? '-' }}</span>
                    </li>
                    <li class="mb-3">
                        <strong>Số điện thoại:</strong><br>
                        <span class="text-secondary">{{ $contact->phone ?? '-' }}</span>
                    </li>
                    <li class="mb-3">
                        <strong>Trạng thái:</strong><br>
                        @if($contact->status === 'pending')
                            <span class="badge bg-warning text-dark fw-semibold">
                                <i class="bi bi-hourglass-split me-1"></i> Chưa trả lời
                            </span>
                        @elseif($contact->status === 'replied')
                            <span class="badge bg-success fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Đã trả lời
                            </span>
                        @elseif($contact->status === 'closed')
                            <span class="badge bg-secondary fw-semibold">
                                <i class="bi bi-x-circle me-1"></i> Đã đóng
                            </span>
                        @else
                            <span class="badge bg-secondary fw-semibold">{{ ucfirst($contact->status) }}</span>
                        @endif
                    </li>
                    <li>
                        <strong>Ngày gửi:</strong><br>
                        <span class="text-secondary">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Nội dung liên hệ và phản hồi --}}
        <div class="col-md-8 d-flex flex-column gap-4">
            {{-- Nội dung liên hệ --}}
            <div class="card shadow-sm rounded-4 border-0 p-4">
                <h4 class="fw-semibold mb-3">
                    <i class="bi bi-chat-text fs-3 text-info me-2"></i> Nội dung liên hệ
                </h4>
                <p class="fs-5 text-secondary">{{ $contact->message ?? 'Không có nội dung' }}</p>
            </div>

            {{-- Phản hồi nếu có --}}
            @if(in_array($contact->status, ['replied', 'closed']) && $contact->reply_message)
            <div class="card shadow-sm rounded-4 border-0 p-4 bg-light">
                <h4 class="fw-semibold mb-3 text-success">
                    <i class="bi bi-reply-fill fs-3 me-2"></i> Phản hồi
                </h4>
                <p class="fs-5">{{ $contact->reply_message }}</p>
                <small class="text-muted fst-italic">
                    <i class="bi bi-clock-history me-1"></i> Đã trả lời lúc: {{ optional($contact->replied_at)->format('d/m/Y H:i') ?? 'Không xác định' }}
                </small>
            </div>
            @endif

            {{-- Form gửi phản hồi (chỉ khi trạng thái đang pending) --}}
            @if($contact->status === 'pending')
            <div class="card shadow-sm rounded-4 border-0 p-4">
                <h4 class="fw-semibold mb-3 text-primary">
                    <i class="bi bi-envelope-fill fs-3 me-2"></i> Gửi phản hồi
                </h4>
                <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <textarea name="reply_message" id="reply_message" rows="6" class="form-control @error('reply_message') is-invalid @enderror" placeholder="Viết phản hồi tại đây..." required>{{ old('reply_message') }}</textarea>
                        @error('reply_message')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary fw-semibold px-4">Gửi phản hồi</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Nút quay lại --}}
    <div class="mt-5 text-end">
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary fw-semibold px-4">
            <i class="bi bi-arrow-left-circle me-2"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection
