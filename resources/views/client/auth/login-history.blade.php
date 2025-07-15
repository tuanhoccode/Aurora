@extends('client.layouts.default')
@section('title', 'Đăng nhập')
@section('content')
<style>
   .error {
      color: #dc3545;
      /* Màu đỏ Bootstrap */
      font-size: 0.9rem;
      margin-top: 5px;
   }
</style>
@php
   $isGoogleUser = !empty(Auth::user()->google_id);
@endphp
<div class="container py-5">
    <h2 class="mb-4 text-center">📜 Lịch sử đăng nhập</h2>

    @if (session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
    @endif

    @if ($logs->isEmpty())
    <div class="alert alert-info text-center">
        Bạn chưa có lịch sử đăng nhập nào.
    </div>
    @else
    <div class="table-responsive shadow rounded">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Thiết bị</th>
                    <th>IP</th>
                    <th>Thời gian</th>
                    <th>Đang sử dụng</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($logs as $index => $log)
                <tr class="{{ $log->is_current ? 'table-success fw-bold' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->user_agent }}</td>
                    <td>{{ $log->ip_address ?? 'Không xác định' }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->logged_in_at)->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}</td>
                    <td>
                        @if ($log->is_current)
                        <span class="badge bg-success">
                            <i class="fas fa-laptop-house me-1"></i> Thiết bị hiện tại
                        </span>
                        @else
                        <span class="badge bg-secondary">Khác</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmLogoutAllModal">
            Đăng xuất tất cả thiết bị
        </button>
        <div class="modal fade" id="confirmLogoutAllModal" tabindex="-1" aria-labelledby="confirmLogoutAllModal" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('logoutAll') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                            <h5 class="modal-title text-center w-100">Xác nhận hành động</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            @if($isGoogleUser)
                                <p class="text-center text-success fw-bold">
                                    Bạn đang sử dụng tài khoản Google. Không cần mật khẩu để đăng xuất khỏi các thiết bị
                                </p>
                            @else
                                <p>Vui lòng nhập mật khẩu để xác nhận đăng xuất khỏi tất cả thiết bị.</p>
                                <input type="password" name="password" class="form-control" placeholder="Mật khẩu">
                                @error('password')
                                <div class="error">{{$message}}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xác nhận đăng xuất</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

@endsection