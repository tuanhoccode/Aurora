@extends('admin.layouts.app')

@section('title', 'Cập nhật sản phẩm')

@section('content')
<div class="content">
  <nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
      <li class="breadcrumb-item active">Cập nhật</li>
    </ol>
  </nav>
  <div class="container-fluid">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if ($errors->has('variants'))
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->get('variants') as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      <div class="mt-4 mb-4 text-end">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Huỷ</a>
        <button class="btn btn-outline-primary me-2" type="submit" name="save_draft" value="1">Lưu nháp</button>
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-save me-1"></i> Lưu sản phẩm
        </button>
      </div>

      <div class="row">
        <!-- Cột trái: Nội dung chính -->
        <div class="col-lg-8">
          <!-- Card: Thông tin cơ bản -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-info-circle me-1"></i> Thông tin cơ bản
            </div>
            <div class="card-body">
              <label class="form-label fw-medium">Tên sản phẩm</label>
              <input class="form-control mb-3 @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="Nhập tên sản phẩm..."/>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label fw-medium">Mô tả ngắn</label>
              <textarea class="form-control mb-3" id="ckeditor-short-description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn...">{{ old('short_description', $product->short_description) }}</textarea>
              <!-- Mô tả chi tiết -->
              <label class="form-label fw-medium">Mô tả chi tiết</label>
              <textarea class="form-control" id="ckeditor-description" name="description" rows="5" placeholder="Nhập mô tả...">{{ old('description', $product->description) }}</textarea>
            </div>
          </div>
          <!-- Card: Ảnh đại diện (ngay sau thông tin cơ bản, trước chọn kiểu sản phẩm) -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-image me-1"></i> Ảnh đại diện
            </div>
            <div class="card-body">
              <label class="form-label">Ảnh đại diện</label>
              @if($product->thumbnail)
                <div class="mb-2">
                  <img src="{{ Storage::url($product->thumbnail) }}" class="img-thumbnail" style="max-width: 120px; max-height: 120px; object-fit: cover;">
                </div>
              @endif
              <input type="file" class="form-control mb-3 @error('thumbnail') is-invalid @enderror" name="thumbnail" accept="image/*">
              @error('thumbnail')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="gallery-upload-wrapper" @if($product->type === 'variant') style="display: none;" @endif>
                <label class="form-label">Thư viện ảnh (có thể chọn nhiều)</label>
                <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" name="gallery_images[]" accept="image/*" multiple>
                @error('gallery_images')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(isset($product) && $product->images && $product->images->count())
                  <div class="row mt-2" id="product-gallery-images">
                    @foreach($product->images->where('product_variant_id', null) as $img)
                      <div class="col-3 mb-2 position-relative">
                        <img src="{{ asset('storage/' . $img->url) }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1" onclick="deleteGalleryImage({{ $img->id }})" style="width: 20px; height: 20px; line-height: 1; padding: 0; display: flex; align-items: center; justify-content: center;">
                          <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
              
             
              
            </div>
          </div>
          <!-- Card: Kiểu sản phẩm -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-cube me-1"></i> Kiểu sản phẩm
            </div>
            <div class="card-body">
              <select class="form-select" id="productTypeSelect" name="type">
                <option value="simple" {{ old('type', $product->type) == 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                <option value="variant" {{ old('type', $product->type) == 'variant' ? 'selected' : '' }}>Sản phẩm biến thể</option>
              </select>
            </div>
          </div>
        </div>
        <!-- Cột phải: Sidebar/card nhỏ -->
        <div class="col-lg-4">
          <!-- Card: Danh mục & Thương hiệu -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-list me-1"></i> Danh mục & Thương hiệu
            </div>
            <div class="card-body">
              <label class="form-label fw-medium">Danh mục</label>
              <select class="form-select select2 @error('categories') is-invalid @enderror" name="categories[]">
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
              </select>
              @error('categories')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label fw-medium mt-3">Thương hiệu</label>
              <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id">
                <option value="">Chọn thương hiệu</option>
                @foreach($brands as $brand)
                  <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
              </select>
              @error('brand_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <!-- Card: Giá & tồn kho chung -->
          <div class="card mb-4" id="priceStockCard">
            <div class="card-header bg-light">
              <i class="fas fa-tag me-1"></i> Giá & Tồn kho chung
            </div>
            <div class="card-body">
              <label class="form-label">SKU</label>
              <input type="text" class="form-control mb-2 @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku', $product->sku) }}" readonly>
              @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Giá</label>
              <input type="number" class="form-control mb-2 @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" min="0" step="1">
              @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Giá khuyến mãi</label>
              <input type="number" class="form-control mb-2 @error('sale_price') is-invalid @enderror" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="1">
              @error('sale_price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Tồn kho</label>
              <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $product->stock) }}" min="0">
              @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <!-- Card: Trạng thái sản phẩm -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-toggle-on me-1"></i> Trạng thái
            </div>
            <div class="card-body">
              <label class="form-label">Trạng thái sản phẩm</label>
              <select class="form-select @error('is_active') is-invalid @enderror" name="is_active">
                <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Đang kinh doanh</option>
                <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Ngừng kinh doanh</option>
              </select>
              @error('is_active')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>
      
      <!-- Biến thể sản phẩm - Khối riêng biệt với chiều ngang đầy đủ -->
      <div class="row" id="variantSection" @if($product->type !== 'variant') style="display: none;" @endif>
        <div class="col-12">
          <div class="card mb-4" id="variantCard">
            <div class="card-header bg-light">
              <i class="fas fa-cubes me-1"></i> <strong>Biến thể sản phẩm</strong>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs mb-3" id="variantTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="generate-tab" data-bs-toggle="tab" data-bs-target="#generate" type="button" role="tab" aria-controls="generate" aria-selected="true">
                    <i class="fas fa-plus"></i> Tạo biến thể
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab" aria-controls="list" aria-selected="false">
                    <i class="fas fa-list"></i> Danh sách biến thể
                  </button>
                </li>
              </ul>
              <div class="tab-content" id="variantTabContent">
                <div class="tab-pane fade show active" id="generate" role="tabpanel" aria-labelledby="generate-tab">
                  <!-- Chọn thuộc tính để tạo biến thể -->
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn thuộc tính để tạo biến thể</label>
                    <div class="row">
                      @foreach($attributes as $attribute)
                        <div class="col-md-6 mb-2">
                          <label class="form-label">{{ $attribute->name }}</label>
                          <select class="form-select variant-attribute-select" name="variant_attributes[{{ $attribute->id }}][]" multiple>
                            <option value="">Chọn thuộc tính</option>
                            @foreach($attribute->values as $value)
                              <option value="{{ $value->id }}">{{ $value->value }}</option>
                            @endforeach
                          </select>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="alert alert-info" id="variantPreview" style="display:none;">
                      <i class="fas fa-info-circle"></i> Sẽ tạo <strong id="variantCount">0</strong> biến thể từ các thuộc tính đã chọn
                    </div>
                    <button type="button" class="btn btn-primary" id="generateVariantsBtn">
                      <i class="fas fa-plus"></i> Tạo biến thể từ thuộc tính
                    </button>
                  </div>
                </div>
                <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
                  <div class="card-body">
                    @if ($errors->has('variants_old'))
                      <div class="alert alert-danger mt-2">
                        @foreach ($errors->get('variants_old') as $msg)
                          <div>{{ $msg }}</div>
                        @endforeach
                      </div>
                    @endif
                  <div id="variantTableWrapper" @if($product->variants->count() == 0) style="display:none;" @endif>
                    <h5 class="mb-3">Danh sách biến thể</h5>
                    <div class="table-responsive">
                      <table class="table table-bordered align-middle" id="variantTable">
                        <thead class="table-light">
                          <tr>
                            <th style="min-width: 200px;">Thuộc tính</th>
                            <th style="min-width: 120px;">SKU</th>
                            <th style="min-width: 120px;">Giá gốc</th>
                            <th style="min-width: 120px;">Giá khuyến mãi</th>
                            <th style="min-width: 100px;">Tồn kho</th>
                            <th style="min-width: 150px;">Ảnh</th>
                            <th style="width: 100px;">Thao tác</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($product->variants as $variant)
                            <tr>
                              <td>
                                @foreach($variant->attributeValues as $attributeValue)
                                  <input type="hidden" name="variants_old[{{ $variant->id }}][attributes][{{ $attributeValue->attribute_id }}]" value="{{ $attributeValue->id }}">
                                  <span class="badge bg-secondary me-1">{{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}</span>
                                @endforeach
                              </td>
                              <td><input type="text" class="form-control" name="variants_old[{{ $variant->id }}][sku]" value="{{ $variant->sku }}" readonly></td>
                              <td>
                                <input type="number" class="form-control variant-price" name="variants_old[{{ $variant->id }}][price]" value="{{ $variant->regular_price }}" min="0" placeholder="Giá gốc">
                              </td>
                              <td>
                                <input type="number" class="form-control variant-sale-price" name="variants_old[{{ $variant->id }}][sale_price]" value="{{ $variant->sale_price }}" min="0" placeholder="Giá khuyến mãi">
                                <small class="text-muted discount-percentage" style="display:none;"></small>
                              </td>
                              <td>
                                <input type="number" class="form-control" name="variants_old[{{ $variant->id }}][stock]" value="{{ $variant->stock }}" min="0" placeholder="Tồn kho">
                              </td>
                              <td>
                                <input type="file" class="form-control variant-image-upload" name="variants_old[{{ $variant->id }}][image]" accept="image/*" data-variant-id="{{ $variant->id }}">
                                @if($variant->img)
                                  <div class="mt-2">
                                    <img src="{{ asset('storage/' . $variant->img) }}" class="img-thumbnail" style="max-width: 60px;">
                                  </div>
                                @endif
                                <!-- Gallery images for this variant -->
                                <div class="variant-gallery mt-2" id="variant-gallery-{{ $variant->id }}">
                                  @foreach($variant->images as $image)
                                    <div class="position-relative d-inline-block me-2 mb-2">
                                      <img src="{{ asset('storage/' . $image->url) }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                      <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0" style="width: 20px; height: 20px; line-height: 1;" onclick="deleteVariantImage({{ $image->id }}); return false;">
                                        <i class="fas fa-times"></i>
                                      </button>
                                    </div>
                                  @endforeach
                                </div>
                                <input type="file" class="form-control mt-2" name="variant_gallery[{{ $variant->id }}][]" multiple accept="image/*" data-variant-id="{{ $variant->id }}">
                              </td>
                              <td>
                                <a href="{{ route('admin.products.variants.edit', [$product->id, $variant->id]) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-variant-btn" data-id="{{ $variant->id }}" data-sku="{{ $variant->sku }}"><i class="fas fa-trash"></i></button>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div id="noVariantsMessage" class="text-center py-4" @if($product->variants->count() > 0) style="display:none;" @endif>
                    <i class="fas fa-info-circle text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">Chưa có biến thể nào được tạo. Vui lòng chọn thuộc tính và nhấn "Tạo biến thể từ thuộc tính".</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Đảm bảo nút lưu luôn hiển thị ở cuối form -->
     
    </form>
  </div>
</div>
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
#variantTable .badge {
  font-size: 0.95em;
  margin-bottom: 2px;
}
#variantTable td, #variantTable th {
  vertical-align: middle;
}
.discount-percentage {
  font-size: 0.75rem;
  color: #dc3545;
  font-weight: 500;
}
.variant-sale-price:focus + .discount-percentage {
  color: #198754;
}
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $('.select2').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Chọn danh mục'
  });
  $('.variant-attribute-select').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Chọn thuộc tính'
  });
  // Bảng chuyển đổi tiếng Việt sang tiếng Anh cho SKU
  const viToEn = {
    'đỏ': 'DO',
    'xanh': 'XA',
    'vàng': 'VA',
    'đen': 'DE',
    'trắng': 'TR',
    'hồng': 'HO',
    'tím': 'TI',
    'cam': 'CA',
    'nâu': 'NA',
    'xám': 'XA',
    'áo thun': 'AO',
    'áo sơ mi': 'AO',
    'quần': 'QU',
    's': 'S',
    'm': 'M',
    'l': 'L',
    'xl': 'XL',
    'xxl': 'XXL',
    'xs': 'XS',
  };

  // Hàm loại bỏ dấu tiếng Việt
  function removeVietnameseTones(str) {
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    return str;
  }

  // Bảng ánh xạ màu sắc giống bên create
  const colorMap = {
    'Đỏ': 'DO',
    'Vàng': 'VANG',
    'Đen': 'DEN',
    'Trắng': 'TRANG',
    'Xám': 'XAM',
    'Xanh': 'XA',
    // ... bổ sung nếu có thêm màu
  };

  function viToEnConvert(str) {
    let lower = str.toLowerCase().trim();
    if (viToEn[lower]) return viToEn[lower];
    return lower
      .replace(/đ/g, 'd')
      .normalize('NFD').replace(/\p{Diacritic}/gu, '')
      .replace(/[^a-z0-9]+/g, '')
      .substring(0, 2)
      .toUpperCase();
  }

  // Tính toán số lượng biến thể sẽ được tạo
  function calculateVariantCount() {
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let values = $(this).val();
      if(values && values.length > 0) {
        attributes.push(values.length); // Mỗi thuộc tính có thể chọn nhiều giá trị
      }
    });
    
    if(attributes.length === 0) {
      $('#variantPreview').hide();
      return;
    }
    
    let totalCombinations = attributes.reduce((a, b) => a * b, 1);
    $('#variantCount').text(totalCombinations);
    $('#variantPreview').show();
  }

  // Tính toán khi thay đổi thuộc tính
  $('.variant-attribute-select').on('change', function() {
    calculateVariantCount();
  });
  // Sửa lại JS để khi tạo biến thể mới, index của variants mới không bị trùng với variants_old
  $('#generateVariantsBtn').on('click', function() {
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let attrName = $(this).closest('.mb-2').find('label').text();
      let attrId = $(this).attr('name').match(/\d+/)[0];
      let values = $(this).val();
      if(values && values.length > 0) {
        attributes.push({
          id: attrId,
          name: attrName,
          values: values.map(v => $(this).find(`option[value="${v}"]`).text()),
          valueIds: values
        });
      }
    });
    let productName = $('input[name="name"]').val();
    let productSlug = productName ? viToEnConvert(productName) : '';
    function cartesian(arr) {
      return arr.reduce(function(a, b) {
        return a.flatMap(d => b.values.map(e => d.concat([{attr: b.name, value: e, attr_id: b.id, value_id: b.valueIds[b.values.indexOf(e)]}])))
      }, [[]]);
    }
    if(attributes.length === 0) {
      alert('Vui lòng chọn ít nhất một thuộc tính để tạo biến thể!');
      return;
    }
    // Lấy số lượng dòng hiện tại (bao gồm cả cũ và mới)
    let currentRows = $('#variantTable tbody tr').length;
    let combos = cartesian(attributes);
    let tbody = '';
    combos.forEach(function(combo, idx) {
      let attrStr = combo.map(c => `<input type=\"hidden\" name=\"variants[${currentRows+idx}][attributes][${c.attr_id}]\" value=\"${c.value_id}\"><span class='badge bg-secondary me-1'>${c.attr}: ${c.value}</span>`).join(' ');
      let sku = productSlug;
      combo.forEach(c => {
        let attrLower = c.attr.toLowerCase();
        if (
          attrLower.includes('màu') ||
          attrLower.includes('color')
        ) {
          let colorCode = colorMap[c.value] || removeVietnameseTones(c.value).substring(0,2).toUpperCase();
          sku += '-' + colorCode;
        } else if (
          attrLower.includes('size') ||
          attrLower.includes('kích')
        ) {
          sku += '-' + removeVietnameseTones(c.value).toUpperCase();
        }
      });
      tbody += `<tr>
        <td>${attrStr}</td>
        <td><input type=\"text\" class=\"form-control\" name=\"variants[${currentRows+idx}][sku]\" value=\"${sku}\" readonly></td>
        <td><input type=\"number\" class=\"form-control variant-price\" name=\"variants[${currentRows+idx}][price]\" min=\"0\" placeholder=\"Giá gốc\"></td>
        <td>
          <input type=\"number\" class=\"form-control variant-sale-price\" name=\"variants[${currentRows+idx}][sale_price]\" min=\"0\" placeholder=\"Giá khuyến mãi\">
          <small class=\"text-muted discount-percentage\" style=\"display:none;\"></small>
        </td>
        <td><input type=\"number\" class=\"form-control\" name=\"variants[${currentRows+idx}][stock]\" min=\"0\" placeholder=\"Tồn kho\"></td>
        <td><input type=\"file\" class=\"form-control\" name=\"variants[${currentRows+idx}][image]\" accept=\"image/*\"></td>
        <td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm btn-danger remove-variant-row\"><i class=\"fas fa-trash\"></i></button></td>
      </tr>`;
    });
    $('#variantTable tbody').append(tbody);
    $('#variantTableWrapper').show();
    $('#noVariantsMessage').hide();
    $('#variantTable').off('click', '.remove-variant-row').on('click', '.remove-variant-row', function() {
      $(this).closest('tr').remove();
      // Kiểm tra nếu không còn biến thể nào thì hiển thị thông báo
      if ($('#variantTable tbody tr').length === 0) {
        $('#variantTableWrapper').hide();
        $('#noVariantsMessage').show();
      }
    });

    // Validate giá khuyến mãi không lớn hơn giá gốc và tính phần trăm giảm giá
    $('#variantTable').on('input', 'input[name*="[sale_price]"], input[name*="[price]"]', function() {
      let row = $(this).closest('tr');
      let priceInput = row.find('input[name*="[price]"]');
      let salePriceInput = row.find('input[name*="[sale_price]"]');
      let discountPercentage = row.find('.discount-percentage');
      
      let price = parseFloat(priceInput.val()) || 0;
      let salePrice = parseFloat(salePriceInput.val()) || 0;
      
      // Validate giá khuyến mãi
      if (salePrice > 0 && salePrice >= price) {
        salePriceInput.addClass('is-invalid');
        if (!salePriceInput.next('.invalid-feedback').length) {
          salePriceInput.after('<div class="invalid-feedback">Giá khuyến mãi phải nhỏ hơn giá gốc</div>');
        }
        discountPercentage.hide();
      } else {
        salePriceInput.removeClass('is-invalid');
        salePriceInput.next('.invalid-feedback').remove();
        
        // Tính và hiển thị phần trăm giảm giá
        if (price > 0 && salePrice > 0 && salePrice < price) {
          let discount = Math.round(((price - salePrice) / price) * 100);
          discountPercentage.text(`Giảm ${discount}%`).show();
        } else {
          discountPercentage.hide();
        }
      }
    });
  });
  // Ẩn/hiện card biến thể, thư viện ảnh và giá & tồn kho theo kiểu sản phẩm
  function toggleProductTypeDependentElements() {
    const isVariant = $('#productTypeSelect').val() === 'variant';
    const productId = '{{ $product->id }}';
    
    if (isVariant) {
      $('#variantSection').show();
      $('#gallery-upload-wrapper').hide();
      $('#priceStockCard').hide();
      
      // Ẩn tất cả ảnh gallery của sản phẩm chính
      $('#product-gallery-images').find('.gallery-image-item').hide();
    } else {
      $('#variantSection').hide();
      $('#gallery-upload-wrapper').show();
      $('#priceStockCard').show();
      
      // Chỉ hiển thị ảnh gallery của sản phẩm chính (không phải của biến thể)
      $('#product-gallery-images').find('.gallery-image-item').show();
    }
  }
  
  // Gọi lần đầu khi tải trang
  toggleProductTypeDependentElements();
  
  // Bắt sự kiện thay đổi kiểu sản phẩm
  $('#productTypeSelect').on('change', function() {
    toggleProductTypeDependentElements();
  });
  
  $(function() {
    if ($('#productTypeSelect').val() === 'variant') {
      $('#variantSection').show();
      $('#priceStockCard').hide();
    } else {
      $('#variantSection').hide();
      $('#priceStockCard').show();
    }
  });
  // Preview gallery images
  const imageUpload = document.querySelector('.image-upload');
  const previewContainer = document.querySelector('.preview-images');
  imageUpload?.addEventListener('change', function(e) {
    previewContainer.innerHTML = '';
    const files = e.target.files;
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'img-thumbnail';
          const removeBtn = document.createElement('button');
          removeBtn.className = 'btn btn-danger btn-sm remove-image';
          removeBtn.innerHTML = '<i class="fas fa-times"></i>';
          removeBtn.onclick = function() {
            previewContainer.removeChild(img);
            previewContainer.removeChild(removeBtn);
          };
          previewContainer.appendChild(img);
          previewContainer.appendChild(removeBtn);
        };
        reader.readAsDataURL(file);
      }
    }
  });
  // Delete gallery image
  document.querySelectorAll('.delete-gallery-image').forEach(button => {
    button.addEventListener('click', function() {
      const path = this.dataset.path;
      if (confirm('Bạn có chắc chắn muốn xóa hình ảnh này không?')) {
        fetch(`{{ route('admin.products.delete-gallery-image', $product->id) }}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.closest('.position-relative').remove();
          } else {
            alert('Có lỗi xảy ra khi xóa hình ảnh');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Có lỗi xảy ra khi xóa hình ảnh');
        });
      }
    });
  });
  // Xóa biến thể
  $('.delete-variant-btn').on('click', function() {
    const variantId = $(this).data('id');
    const variantSku = $(this).data('sku');
    if (confirm(`Bạn có chắc muốn xóa biến thể "${variantSku}"?`)) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/products/{{ $product->id }}/variants/${variantId}`;
      form.style.display = 'none';
      const csrfToken = document.createElement('input');
      csrfToken.type = 'hidden';
      csrfToken.name = '_token';
      csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      form.appendChild(csrfToken);
      const methodField = document.createElement('input');
      methodField.type = 'hidden';
      methodField.name = '_method';
      methodField.value = 'DELETE';
      form.appendChild(methodField);
      document.body.appendChild(form);
      form.submit();
    }
  });

// Hàm lấy key tổ hợp thuộc tính từ 1 dòng biến thể
function getVariantKeyFromRow(row) {
    let attrs = [];
    row.find('input[name*="[attributes]"]').each(function() {
        attrs.push($(this).val());
    });
    return attrs.sort().join('-');
}

// Kiểm tra và đánh dấu các dòng biến thể bị trùng tổ hợp thuộc tính
function checkDuplicateVariantCombinations() {
    let keys = {};
    $('#variantTable tbody tr').each(function() {
        let row = $(this);
        let key = getVariantKeyFromRow(row);
        if (!key) return;
        if (keys[key]) {
            // Đánh dấu dòng trùng
            row.addClass('table-danger');
            if (row.find('.duplicate-attr-msg').length === 0) {
                row.find('td:first').append('<div class="text-danger small duplicate-attr-msg">Tổ hợp thuộc tính này đã tồn tại!</div>');
            }
        } else {
            row.removeClass('table-danger');
            row.find('.duplicate-attr-msg').remove();
            keys[key] = true;
        }
    });
}

// Gọi lại hàm kiểm tra mỗi khi thêm/xóa dòng hoặc thay đổi thuộc tính
$(document).on('change', 'input[name*="[attributes]"]', checkDuplicateVariantCombinations);
$(document).on('click', '.remove-variant-row', function() {
    setTimeout(checkDuplicateVariantCombinations, 100);
});
$(document).on('DOMNodeInserted', '#variantTable tbody', function() {
    setTimeout(checkDuplicateVariantCombinations, 100);
});

// Chặn submit nếu còn dòng bị trùng tổ hợp thuộc tính
$('form').on('submit', function(e) {
    checkDuplicateVariantCombinations();
    if ($('#variantTable tbody tr.table-danger').length > 0) {
        alert('Có biến thể bị trùng tổ hợp thuộc tính. Vui lòng kiểm tra lại!');
        e.preventDefault();
        return false;
    }
});
</script>
<!-- CKEditor cho mô tả chi tiết -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
  ClassicEditor.create(document.querySelector('#ckeditor-description'));
  ClassicEditor.create(document.querySelector('#ckeditor-short-description'));
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('productTypeSelect');
    const variantSection = document.getElementById('variantSection');
    function toggleVariantSection() {
      if (typeSelect.value === 'variant') {
        variantSection.style.display = 'flex';
      } else {
        variantSection.style.display = 'none';
      }
    }
    if (typeSelect && variantSection) {
      typeSelect.addEventListener('change', toggleVariantSection);
      toggleVariantSection();
    }
  });

  // Xử lý xóa ảnh gallery của biến thể
  function deleteVariantImage(imageId) {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này không?')) {
      const token = $('meta[name="csrf-token"]').attr('content');
      const productId = '{{ $product->id }}';
      
      $.ajax({
        url: '{{ route("admin.products.variants.delete-gallery-image", ["__PRODUCT__", "__IMAGE__"]) }}'
          .replace('__PRODUCT__', productId)
          .replace('__IMAGE__', imageId),
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function(response) {
          if (response.success) {
            toastr.success(response.message || 'Đã xóa ảnh thành công');
            // Tìm và xóa phần tử ảnh đã xóa
            $(`[onclick*="${imageId}"]`).closest('.position-relative').remove();
          } else {
            toastr.error(response.message || 'Có lỗi xảy ra khi xóa ảnh');
          }
        },
        error: function(xhr) {
          const response = xhr.responseJSON || {};
          toastr.error(response.message || 'Có lỗi xảy ra khi xóa ảnh');
          console.error('Error deleting image:', xhr.responseText);
        }
      });
    }
  }

  // Xử lý khi chọn ảnh gallery cho biến thể
  $('input[name^="variant_gallery["]').on('change', function() {
    const variantId = $(this).data('variant-id');
    const files = this.files;
    const galleryContainer = $(`#variant-gallery-${variantId}`);
    const token = $('meta[name="csrf-token"]').attr('content');
    const productId = '{{ $product->id }}';
    
    // Tạo form data để gửi file
    const formData = new FormData();
    formData.append('_token', token);
    formData.append('variant_id', variantId);
    
    // Thêm từng file vào form data
    for (let i = 0; i < files.length; i++) {
      formData.append('gallery[]', files[i]);
    }
    
    // Hiển thị loading
    const loadingHtml = `
      <div class="position-relative d-inline-block me-2 mb-2">
        <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ms-2">Đang tải lên...</span>
      </div>`;
    
    const $loading = $(loadingHtml);
    galleryContainer.append($loading);
    
    // Gửi yêu cầu upload ảnh lên server
    $.ajax({
      url: '{{ route("admin.products.variants.upload-gallery") }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Xóa thông báo loading
        $loading.remove();
        
        if (response.success && response.images && response.images.length > 0) {
          // Xử lý từng ảnh đã upload thành công
          response.images.forEach(function(image) {
            const imgPreview = `
              <div class="position-relative d-inline-block me-2 mb-2">
                <img src="${image.url}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" 
                        style="width: 20px; height: 20px; line-height: 18px;"
                        onclick="deleteVariantImage(${image.id})">
                  <i class="fas fa-times"></i>
                </button>
                <input type="hidden" name="variant_images[${variantId}][]" value="${image.id}">
              </div>`;
            
            galleryContainer.append(imgPreview);
          });
          
          toastr.success('Tải lên ảnh thành công');
        } else {
          toastr.error(response.message || 'Có lỗi xảy ra khi tải lên ảnh');
        }
      },
      error: function(xhr) {
        $loading.remove();
        const response = xhr.responseJSON || {};
        toastr.error(response.message || 'Có lỗi xảy ra khi tải lên ảnh');
        console.error('Error uploading images:', xhr.responseText);
      }
    });
  });
</script>
@endpush

@endsection
