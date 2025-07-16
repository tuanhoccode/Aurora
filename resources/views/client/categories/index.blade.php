@extends('client.layouts.default')

@section('content')
<div class="container py-5">
    <h2 class="mb-4" style="font-weight:700;font-size:2.2rem;line-height:1.2;">Danh mục sản phẩm</h2>
    <div class="row">
        @php use Illuminate\Support\Str; @endphp
        @foreach($categories as $category)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    @if($category->icon)
                        @if(Str::startsWith($category->icon, ['http://', 'https://']))
                            <img src="{{ $category->icon }}" class="card-img-top" alt="{{ $category->name }}">
                        @else
                            <img src="{{ asset('storage/' . $category->icon) }}" class="card-img-top" alt="{{ $category->name }}">
                        @endif
                    @else
                        <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="{{ $category->name }}">
                    @endif
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <a href="{{ route('client.categories.show', $category->id) }}" class="btn btn-primary">Xem sản phẩm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 