@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Danh sách liên hệ</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form tìm kiếm và lọc trạng thái --}}
    <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3 mb-4 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm theo tên hoặc email">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Lọc theo trạng thái --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã trả lời</option>
                {{-- <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option> --}}
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </div>
        <div class="col-md-1">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Xóa</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>
                                @if($contact->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chưa xử lý</span>
                                @elseif($contact->status === 'replied')
                                    <span class="badge bg-success">Đã trả lời</span>
                                @elseif($contact->status === 'closed')
                                    <span class="badge bg-secondary">Đã đóng</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($contact->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" id="actionMenu{{ $contact->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $contact->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.contacts.show', $contact->id) }}">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có liên hệ nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
