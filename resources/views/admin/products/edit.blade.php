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
              <textarea class="form-control mb-3" name="short_description" rows="2" placeholder="Nhập mô tả ngắn...">{{ old('short_description', $product->short_description) }}</textarea>
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
              <label class="form-label">Thư viện ảnh (có thể chọn nhiều)</label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                @if($product->gallery)
                  @foreach(json_decode($product->gallery) as $image)
                    <div class="position-relative">
                      <img src="{{ Storage::url($image) }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                      <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-gallery-image" data-path="{{ $image }}">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  @endforeach
                @endif
              </div>
              <input type="file" class="form-control image-upload @error('gallery_images') is-invalid @enderror" name="gallery_images[]" accept="image/*" multiple>
              <div class="preview-images d-flex flex-wrap gap-2 mt-2"></div>
              @error('gallery_images')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <!-- Sau phần thuộc tính sản phẩm, thêm lựa chọn kiểu sản phẩm dạng select -->
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
          <!-- Card: Biến thể sản phẩm với tab nhỏ bên trong, thêm id để JS ẩn/hiện -->
          <div class="card mb-4" id="variantCard">
            <div class="card-header bg-light">
              <strong>Biến thể sản phẩm</strong>
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
                          <select class="form-select variant-attribute-select" name="variant_attributes[{{ $attribute->id }}]">
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
                  <div id="variantTableWrapper" @if($product->variants->count() == 0) style="display:none;" @endif>
                    <h5 class="mb-3">Danh sách biến thể</h5>
                    <div class="table-responsive">
                      <table class="table table-bordered align-middle" id="variantTable">
                        <thead class="table-light">
                          <tr>
                            <th>Thuộc tính</th>
                            <th>SKU</th>
                            <th>Giá gốc</th>
                            <th>Giá khuyến mãi</th>
                            <th>Tồn kho</th>
                            <th>Ảnh</th>
                            <th style="width: 80px;">Thao tác</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($product->variants as $variant)
                            @php
                              $hasSuccessfulOrder = $variant->orderItems()->whereHas('order.currentStatus', function($q) {
                                $q->where('order_status_id', 4)->where('is_current', 1);
                              })->exists();
                            @endphp
                            <tr>
                              <td>
                                @foreach($variant->attributeValues as $attributeValue)
                                  <span class="badge bg-secondary me-1">{{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}</span>
                                @endforeach
                              </td>
                              <td>{{ $variant->sku }}</td>
                              <td>{{ number_format($variant->regular_price) }}đ</td>
                              <td>
                                @if($variant->sale_price)
                                  {{ number_format($variant->sale_price) }}đ
                                  @php
                                    $discount = round((($variant->regular_price - $variant->sale_price) / $variant->regular_price) * 100);
                                  @endphp
                                  <br><small class="text-danger">Giảm {{ $discount }}%</small>
                                @else
                                  <span class="text-muted">-</span>
                                @endif
                              </td>
                              <td>{{ $variant->stock }}</td>
                              <td>
                                @if($variant->img)
                                  <img src="{{ asset('storage/' . $variant->img) }}" class="img-thumbnail" style="max-width: 60px;">
                                @endif
                              </td>
                              <td>
                                <a href="{{ route('admin.products.variants.edit', [$product->id, $variant->id]) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-variant-btn" data-id="{{ $variant->id }}" data-sku="{{ $variant->sku }}" @if($hasSuccessfulOrder) disabled data-bs-toggle="tooltip" title="Không thể xóa: Biến thể đã có đơn hàng giao thành công" @endif><i class="fas fa-trash"></i></button>
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
          <!-- Card: Tồn kho theo kho hàng -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-warehouse me-1"></i> Tồn kho theo kho hàng
            </div>
            <div class="card-body">
              @foreach($stocks as $stock)
                <div class="border rounded p-3 mb-2">
                  <div><strong>Kho:</strong> {{ $stock->warehouse_name ?? 'N/A' }}</div>
                  <div><strong>Số lượng:</strong> {{ $stock->stock }}</div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      <div class="mt-4 text-end">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Huỷ</a>
        <button class="btn btn-outline-primary me-2" type="submit" name="save_draft" value="1">Lưu nháp</button>
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-save me-1"></i> Lưu sản phẩm
        </button>
      </div>
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
      let value = $(this).val();
      if(value) {
        attributes.push(1); // Mỗi thuộc tính chỉ chọn 1 giá trị
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
  $('#generateVariantsBtn').on('click', function() {
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let attrName = $(this).closest('.mb-2').find('label').text();
      let attrId = $(this).attr('name').match(/\d+/)[0];
      let value = $(this).val();
      if(value) {
        attributes.push({
          id: attrId,
          name: attrName,
          values: [$(this).find('option:selected').text()],
          valueIds: [value]
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
    let combos = cartesian(attributes);
    let tbody = '';
    combos.forEach(function(combo, idx) {
      let attrStr = combo.map(c => `<input type=\"hidden\" name=\"variants[${idx}][attributes][${c.attr_id}]\" value=\"${c.value_id}\"><span class='badge bg-secondary me-1'>${c.attr}: ${c.value}</span>`).join(' ');
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
        <td><input type=\"text\" class=\"form-control\" name=\"variants[${idx}][sku]\" value=\"${sku}\" readonly></td>
        <td><input type=\"number\" class=\"form-control variant-price\" name=\"variants[${idx}][price]\" min=\"0\" placeholder=\"Giá gốc\"></td>
        <td>
          <input type=\"number\" class=\"form-control variant-sale-price\" name=\"variants[${idx}][sale_price]\" min=\"0\" placeholder=\"Giá khuyến mãi\">
          <small class=\"text-muted discount-percentage\" style=\"display:none;\"></small>
        </td>
        <td><input type=\"number\" class=\"form-control\" name=\"variants[${idx}][stock]\" min=\"0\" placeholder=\"Tồn kho\"></td>
        <td><input type=\"file\" class=\"form-control\" name=\"variants[${idx}][image]\" accept=\"image/*\"></td>
        <td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm btn-danger remove-variant-row\"><i class=\"fas fa-trash\"></i></button></td>
      </tr>`;
    });
    $('#variantTable tbody').html(tbody);
    $('#variantTableWrapper').show();
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
  // Ẩn/hiện card biến thể và giá & tồn kho theo kiểu sản phẩm
  $('#productTypeSelect').on('change', function() {
    if ($(this).val() === 'variant') {
      $('#variantCard').show();
      $('#priceStockCard').hide();
    } else {
      $('#variantCard').hide();
      $('#priceStockCard').show();
    }
  });
  $(function() {
    if ($('#productTypeSelect').val() === 'variant') {
      $('#variantCard').show();
      $('#priceStockCard').hide();
    } else {
      $('#variantCard').hide();
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
</script>
<!-- CKEditor cho mô tả chi tiết -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#ckeditor-description'));
</script>
@endpush

@endsection
