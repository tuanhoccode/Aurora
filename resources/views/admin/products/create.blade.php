@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="container-fluid">

    <!-- Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Add New Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Product Information</h6>
        </div>
        <div class="card-body">
            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter product name">
                        </div>

                        <div class="mb-3">
                            <label>Short Description</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Description (rich text)</label>
                            <textarea name="description" id="description" class="form-control" rows="8"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Product Images</label>
                            <input type="file" class="form-control" name="images[]" multiple>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Category</label>
                            <select class="form-select">
                                <option>Select Category</option>
                                <option>Smartphones</option>
                                <option>Laptops</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>SKU</label>
                            <input type="text" class="form-control" placeholder="e.g. SP00123">
                        </div>

                        <div class="mb-3">
                            <label>Price</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label>Sale Price</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label>Stock</label>
                            <input type="number" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <select class="form-select">
                                <option>Active</option>
                                <option>Draft</option>
                                <option>Out of Stock</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Sizes</label>
                            <select class="form-select" multiple>
                                <option>XS</option>
                                <option>S</option>
                                <option>M</option>
                                <option>L</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Colors</label>
                            <input type="text" class="form-control" placeholder="Black, White">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Add Product</button>
            </form>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection

