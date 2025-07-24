@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="content">
  <nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="#">Sản phẩm</a></li>
      <li class="breadcrumb-item active">Tạo mới</li>
    </ol>
  </nav>
  <div class="container-fluid">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
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
              <input class="form-control mb-3 @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm..."/>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label fw-medium">Mô tả ngắn</label>
              <textarea class="form-control mb-3" id="ckeditor-short-description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn..."></textarea>
              <!-- Mô tả chi tiết -->
              <label class="form-label fw-medium">Mô tả chi tiết</label>
              <textarea class="form-control" id="ckeditor-description" name="description" rows="5" placeholder="Nhập mô tả..."></textarea>
            </div>
          </div>
          <!-- Card: Ảnh đại diện (ngay sau thông tin cơ bản, trước chọn kiểu sản phẩm) -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <i class="fas fa-image me-1"></i> Ảnh đại diện
            </div>
            <div class="card-body">
              <label class="form-label">Ảnh đại diện</label>
              <input type="file" class="form-control mb-3 @error('thumbnail') is-invalid @enderror" name="thumbnail" accept="image/*">
              @error('thumbnail')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="gallery-upload-wrapper" style="display: block;">
                <label class="form-label">Thư viện ảnh (có thể chọn nhiều)</label>
                <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" name="gallery_images[]" accept="image/*" multiple>
                @error('gallery_images')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                <option value="simple" selected>Sản phẩm đơn giản</option>
                <option value="variant">Sản phẩm biến thể</option>
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
                  <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
              </select>
              @error('categories')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label fw-medium mt-3">Thương hiệu</label>
              <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id">
                <option value="">Chọn thương hiệu</option>
                @foreach($brands as $brand)
                  <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
              <input type="text" class="form-control mb-2 @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku') }}">
              @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Giá</label>
              <input type="number" class="form-control mb-2 @error('price') is-invalid @enderror" name="price" value="{{ old('price', 0) }}" min="0" step="1">
              @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Giá khuyến mãi</label>
              <input type="number" class="form-control mb-2 @error('sale_price') is-invalid @enderror" name="sale_price" value="{{ old('sale_price') }}" min="0" step="1">
              @error('sale_price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <label class="form-label">Tồn kho</label>
              <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', 0) }}" min="0">
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
                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Đang kinh doanh</option>
                <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Ngừng kinh doanh</option>
              </select>
              @error('is_active')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>
      
      <!-- Biến thể sản phẩm - Khối riêng biệt với chiều ngang đầy đủ -->
      <div class="row" id="variantSection" style="display: none;">
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
                  <div id="variantTableWrapper" style="display:none;">
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
                            <th style="width: 40px;"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Dòng biến thể sẽ được JS sinh ra -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div id="noVariantsMessage" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">Chưa có biến thể nào được tạo. Vui lòng chọn thuộc tính và nhấn "Tạo biến thể từ thuộc tính".</p>
                  </div>
                </div>
              </div>
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
  font-size: 0.85em;
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
  $('.select-attribute').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Chọn thuộc tính'
  });

  $('.variant-attribute-select').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Chọn thuộc tính',
    allowClear: true
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
    // ... bổ sung thêm nếu cần
  };

  function viToEnConvert(str) {
    let lower = str.toLowerCase().trim();
    if (viToEn[lower]) return viToEn[lower];
    // Nếu không có trong bảng, lấy 2 ký tự đầu và viết hoa
    return lower
      .replace(/đ/g, 'd')
      .normalize('NFD').replace(/\p{Diacritic}/gu, '')
      .replace(/[^a-z0-9]+/g, '')
      .substring(0, 2)
      .toUpperCase();
  }

  function removeVietnameseTones(str) {
    return str.normalize('NFD').replace(/\p{Diacritic}/gu, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
  }

  function getSkuPrefix(productName) {
    let words = removeVietnameseTones(productName.trim()).split(/\s+/);
    let prefix = words.slice(0, 2).map(w => w[0] ? w[0].toUpperCase() : '').join('');
    return prefix;
  }

  const colorMap = {
    'Đỏ': 'DO',
    'Vàng': 'VANG',
    'Đen': 'DEN',
    'Trắng': 'TRANG',
    'Xám': 'XAM',
    'Xanh': 'XA',
    // ... bổ sung nếu có thêm màu
  };

  // Tính toán số lượng biến thể sẽ được tạo
  function calculateVariantCount() {
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let values = $(this).val() || [];
      if(values.length > 0) {
        attributes.push(values.length);
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

  // Tự động cập nhật SKU cho sản phẩm đơn giản
  $('input[name="name"]').on('input', function() {
    let productName = $(this).val();
    let skuPrefix = getSkuPrefix(productName);
    if (!skuPrefix) skuPrefix = 'SP'; // fallback nếu tên sản phẩm rỗng
    // Nếu có input size/màu cho sản phẩm đơn giản, lấy thêm (giả sử có input name="size" và name="color")
    let size = $('input[name="size"]').val() || '';
    let color = $('input[name="color"]').val() || '';
    let sku = skuPrefix;
    if(size) sku += '-' + removeVietnameseTones(size).toUpperCase();
    if(color) sku += '-' + removeVietnameseTones(color).toUpperCase();
    $('input[name="sku"]').val(sku);
  });

  // Khi thay đổi size hoặc màu (nếu có input riêng cho sản phẩm đơn giản)
  $('input[name="size"], input[name="color"]').on('input', function() {
    $('input[name="name"]').trigger('input');
  });

  // Tự động tạo SKU cho biến thể khi sinh dòng mới
  $('#generateVariantsBtn').on('click', function() {
    // Lấy các giá trị thuộc tính đã chọn
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let attrName = $(this).closest('.mb-2').find('label').text();
      let attrId = $(this).attr('name').match(/\d+/)[0];
      let values = $(this).val() || [];
      if(values.length > 0) {
        attributes.push({
          id: attrId,
          name: attrName,
          values: $(this).find('option:selected').map(function(){return $(this).text();}).get(),
          valueIds: values
        });
      }
    });

    // Lấy tên sản phẩm
    let productName = $('input[name="name"]').val();
    let skuPrefix = getSkuPrefix(productName);
    if (!skuPrefix) skuPrefix = 'SP'; // fallback nếu tên sản phẩm rỗng

    // Sinh tổ hợp biến thể (cartesian product)
    function cartesian(arr) {
      return arr.reduce(function(a, b) {
        return a.flatMap(d => b.values.map(e => d.concat([{attr: b.name, value: e, attr_id: b.id, value_id: b.valueIds[b.values.indexOf(e)]}])));
      }, [[]]);
    }

    console.log('attributes:', attributes);
    if(attributes.length === 0) {
      alert('Vui lòng chọn ít nhất một thuộc tính để tạo biến thể!');
      return;
    }
    let combos = cartesian(attributes);
    console.log('combos:', combos);
    let seenCombos = new Set();
    let duplicateCombos = [];
    let tbody = '';
    combos.forEach(function(combo, idx) {
      // Tạo key duy nhất cho tổ hợp thuộc tính
      let comboKey = combo.map(c => c.attr + ':' + c.value).join('|');
      if (seenCombos.has(comboKey)) {
        duplicateCombos.push(comboKey);
        return; // Bỏ qua tổ hợp trùng
      }
      seenCombos.add(comboKey);
      let attrStr = combo.map(c => `<input type="hidden" name="variants[${idx}][attributes][${c.attr_id}]" value="${c.value_id}"><span class='badge bg-secondary me-1'>${c.attr}: ${c.value}</span>`).join(' ');
      // GHÉP CHỈ SIZE VÀ MÀU VÀO SKU
      let sku = skuPrefix;
      combo.forEach(c => {
        let attrLower = c.attr.toLowerCase();
        if (
          attrLower.includes('màu') ||
          attrLower.includes('color')
        ) {
          // Nếu có trong bảng ánh xạ thì lấy mã, không thì lấy 2 ký tự đầu
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
        <td><input type="text" class="form-control" name="variants[${idx}][sku]" value="${sku}" placeholder="Để trống để tự tạo"></td>
        <td><input type="number" class="form-control variant-price" name="variants[${idx}][price]" min="0" placeholder="Giá gốc"></td>
        <td><input type="number" class="form-control variant-sale-price" name="variants[${idx}][sale_price]" min="0" placeholder="Giá khuyến mãi"></td>
        <td><input type="number" class="form-control" name="variants[${idx}][stock]" min="0" placeholder="Tồn kho"></td>
        <td><input type="file" class="form-control" name="variants[${idx}][image]" accept="image/*"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-variant-row"><i class="fas fa-trash"></i></button></td>
      </tr>`;
    });
    $('#variantTable tbody').html(tbody);
    $('#variantTableWrapper').show();
    $('#noVariantsMessage').hide();
    
    // Cập nhật badge số lượng biến thể
    $('#variantCountBadge').text(combos.length + ' biến thể');

    if (duplicateCombos.length > 0) {
      alert('Đã bỏ qua các biến thể bị trùng thuộc tính!');
    }

    // Kiểm tra trùng lặp SKU trong danh sách biến thể
    let skuList = [];
    let duplicateSkus = [];
    $('#variantTable tbody input[name*="[sku]"]').each(function() {
      let sku = $(this).val();
      if (skuList.includes(sku)) {
        duplicateSkus.push(sku);
      }
      skuList.push(sku);
    });
    if (duplicateSkus.length > 0) {
      alert('Có SKU trùng lặp trong danh sách biến thể: ' + [...new Set(duplicateSkus)].join(', '));
    }

    // Xóa dòng biến thể
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

  // Ẩn/hiện card biến thể theo kiểu sản phẩm
  $('#productTypeSelect').on('change', function() {
    if ($(this).val() === 'variant') {
      $('#variantSection').show();
      $('#priceStockCard').hide();
    } else {
      $('#variantSection').hide();
      $('#priceStockCard').show();
    }
  });
  // Khởi tạo trạng thái ban đầu
  $(function() {
    if ($('#productTypeSelect').val() === 'variant') {
      $('#variantSection').show();
      $('#priceStockCard').hide();
    } else {
      $('#variantSection').hide();
      $('#priceStockCard').show();
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('productTypeSelect');
    const galleryWrapper = document.getElementById('gallery-upload-wrapper');
    function toggleGalleryField() {
      if (typeSelect.value === 'simple') {
        galleryWrapper.style.display = 'block';
      } else {
        galleryWrapper.style.display = 'none';
      }
    }
    typeSelect.addEventListener('change', toggleGalleryField);
    toggleGalleryField(); // init on load
  });
</script>
<!-- CKEditor cho mô tả chi tiết -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#ckeditor-description'));
ClassicEditor.create(document.querySelector('#ckeditor-short-description'));
</script>
@endpush

@endsection
