@extends('admin.layouts.app')

@section('title', 'Chi tiết Banner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Chi tiết Banner</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>{{ $banner->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tiêu đề:</th>
                                    <td><strong>{{ $banner->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Dòng chữ nhỏ:</th>
                                    <td>
                                        @if($banner->subtitle)
                                            <em>{{ $banner->subtitle }}</em>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Link:</th>
                                    <td>
                                        @if($banner->link)
                                            <a href="{{ $banner->link }}" target="_blank">{{ $banner->link }}</a>
                                        @else
                                            <span class="text-muted">Không có link</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Vị trí:</th>
                                    <td>
                                        <span class="badge bg-{{ $banner->position == 'slider' ? 'primary' : ($banner->position == 'banner' ? 'success' : 'warning') }}">
                                            {{ ucfirst($banner->position) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Thứ tự:</th>
                                    <td>{{ $banner->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if($banner->is_active)
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo:</th>
                                    <td>{{ $banner->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Cập nhật lần cuối:</th>
                                    <td>{{ $banner->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5>Ảnh Banner</h5>
                                <div class="border rounded p-3 mb-3">
                                    <img src="{{ $banner->image_url }}" 
                                         alt="{{ $banner->title }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 300px; max-width: 100%;">
                                </div>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> Xem link
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 