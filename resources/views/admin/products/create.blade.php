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
              <textarea class="form-control mb-3" name="short_description" rows="2" placeholder="Nhập mô tả ngắn..."></textarea>
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
              <!-- <label class="form-label">Thư viện ảnh (có thể chọn nhiều)</label>
              <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" name="gallery_images[]" accept="image/*" multiple>
              @error('gallery_images')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror -->
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
          <!-- Card: Thông tin chung -->
          <div class="card mb-4" id="priceStockCard">
            <div class="card-header bg-light">
              <i class="fas fa-tag me-1"></i> Thông tin chung
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">SKU</label>
                <input type="text" class="form-control @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku') }}" id="productSku">
                @error('sku')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Giá sản phẩm cha -->
              <div class="mb-3">
                <label class="form-label">Giá gốc</label>
                <div class="input-group">
                  <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', 0) }}" min="0" step="1000">
                  <span class="input-group-text">đ</span>
                  @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <small class="text-muted">Nhập giá tiền (VND) cho sản phẩm này</small>
              </div>
              
              <!-- Giá khuyến mãi (chỉ hiển thị với sản phẩm thường) -->
              <div class="mb-3 simple-product-fields">
                <label class="form-label">Giá khuyến mãi</label>
                <div class="input-group">
                  <input type="number" class="form-control @error('sale_price') is-invalid @enderror" name="sale_price" value="{{ old('sale_price', '') }}" min="0" step="1000">
                  <span class="input-group-text">đ</span>
                  @error('sale_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <small class="text-muted">Nhập giá khuyến mãi (nếu có)</small>
              </div>
              
              <!-- Thông báo cho sản phẩm biến thể -->
              <div id="variantProductNote" class="alert alert-info mt-3" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>
                <span>Đây là sản phẩm biến thể. Vui lòng nhập thêm thông tin cho từng biến thể ở bảng bên dưới.</span>
              </div>
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
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
  margin: 0;
  padding: 0.25rem 0.5rem;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
  margin: 0;
  width: 100% !important;
}
/* Style cho dòng được chọn trong bảng biến thể */
#variantTable tbody tr {
  cursor: pointer;
  transition: background-color 0.2s;
}
#variantTable tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.03);
}
#variantTable tbody tr.table-active {
  background-color: rgba(13, 110, 253, 0.1) !important;
  box-shadow: 0 0 0 1px rgba(13, 110, 253, 0.5);
}
/* Làm đẹp các ô input trong bảng */
#variantTable input[type="number"],
#variantTable input[type="text"] {
  border: 1px solid #dee2e6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  width: 100%;
}
#variantTable input[type="number"]:focus,
#variantTable input[type="text"]:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
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
    'đỏ': 'DO',
    'vàng': 'VANG',
    'đen': 'DEN',
    'trắng': 'TRANG',
    'xám': 'XAM',
    'xanh': 'XA'
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

  // Hàm chuyển đổi tên màu sang mã màu
  function getColorCode(colorName) {
    const colorMap = {
      'đỏ': 'DO',
      'vàng': 'VANG',
      'đen': 'DEN',
      'trắng': 'TRANG',
      'xám': 'XAM',
      'xanh': 'XA',
      'hồng': 'HONG',
      'tím': 'TIM',
      'cam': 'CAM',
      'nâu': 'NAU'
    };
    
    // Chuyển về chữ thường và xóa dấu
    const normalizedColor = removeVietnameseTones(colorName.toLowerCase());
    
    // Tìm màu phù hợp
    for (const [key, value] of Object.entries(colorMap)) {
      if (normalizedColor.includes(removeVietnameseTones(key.toLowerCase()))) {
        return value;
      }
    }
    
    // Nếu không tìm thấy, lấy 2-3 ký tự đầu và viết hoa
    return normalizedColor.replace(/[^a-z0-9]/g, '').substring(0, 3).toUpperCase();
  }

  // Sử dụng hàm removeVietnameseTones đã được định nghĩa ở dưới
  function getSkuPrefix(productName) {
    let words = removeVietnameseTones(productName.trim()).split(/\s+/);
    let prefix = words.slice(0, 2).map(w => w[0] ? w[0].toUpperCase() : '').join('');
    return prefix;
  }

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

  // Bảng ánh xạ màu sắc
  const colorMap = {
    'Đỏ': 'DO',
    'Vàng': 'VANG',
    'Vàng kim': 'VK',
    'Vàng đồng': 'VD',
    'Vàng bạc': 'VB',
    'Đen': 'DEN',
    'Trắng': 'TRANG',
    'Trắng ngà': 'TN',
    'Trắng sữa': 'TS',
    'Xanh dương': 'XD',
    'Xanh da trời': 'XDT',
    'Xanh lá': 'XL',
    'Xanh lá cây': 'XLC',
    'Xanh rêu': 'XR',
    'Xanh ngọc': 'XN',
    'Xanh nước biển': 'XNB',
    'Hồng': 'HONG',
    'Hồng pastel': 'HP',
    'Hồng đào': 'HD',
    'Tím': 'TIM',
    'Tím than': 'TT',
    'Tím hoa cà': 'THC',
    'Cam': 'CAM',
    'Cam đất': 'CD',
    'Nâu': 'NAU',
    'Nâu đỏ': 'ND',
    'Nâu socola': 'NS',
    'Xám': 'XAM',
    'Xám bạc': 'XB',
    'Xám đen': 'XD',
    'Xám khói': 'XK',
    'Kem': 'KEM',
    'Be': 'BE',
    'Ghi': 'GHI',
    'Ghi xám': 'GX'
  };

  // Hàm chuyển tiếng Việt có dấu sang không dấu
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

  // Tự động tạo SKU cho biến thể khi sinh dòng mới
  $('#generateVariantsBtn').on('click', function() {
    let attributes = [];
    $('.variant-attribute-select').each(function() {
      let attrName = $(this).closest('.mb-2').find('label').text();
      let attrId = $(this).attr('name').match(/\d+/)[0];
      let values = $(this).val() || [];
      if(values.length > 0) {
        attributes.push({
          id: attrId,
          name: attrName,
          values: $(this).find('option:selected').map(function(){ 
            return { 
              id: $(this).val(), 
              text: $(this).text() 
            }; 
          }).get(),
          valueIds: values
        });
      }
    });

    if(attributes.length === 0) {
      alert('Vui lòng chọn ít nhất một thuộc tính để tạo biến thể!');
      return;
    }
    
    // Sinh tổ hợp biến thể (cartesian product)
    function cartesian(arr) {
      return arr.reduce(function(a, b) {
        return a.flatMap(d => b.values.map(e => d.concat([{
          attr: b.name, 
          value: e.text, 
          attr_id: b.id, 
          value_id: e.id
        }])));
      }, [[]]);
    }
    
    let combos = cartesian(attributes);
    let seenCombos = new Set();
    let duplicateCombos = [];
    let tbody = '';
    let currentRows = $('#variantTable tbody tr').length;
    
    // Sắp xếp các thuộc tính theo thứ tự ưu tiên (màu sắc trước, kích thước sau)
    function sortCombo(combo) {
      return combo.sort((a, b) => {
        // Ưu tiên màu sắc trước
        if (a.attr.toLowerCase().includes('màu')) return -1;
        if (b.attr.toLowerCase().includes('màu')) return 1;
        // Sau đó đến kích thước
        if (a.attr.toLowerCase().includes('kích thước') || a.attr.toLowerCase().includes('size')) return 1;
        if (b.attr.toLowerCase().includes('kích thước') || b.attr.toLowerCase().includes('size')) return -1;
        return 0;
      });
    }
    
    // Xử lý từng tổ hợp thuộc tính
    combos.forEach(function(combo, idx) {
      // Sắp xếp lại thứ tự thuộc tính
      combo = sortCombo(combo);
      
      // Tạo key duy nhất cho tổ hợp thuộc tính
      let comboKey = combo.map(c => c.attr + ':' + c.value).join('|');
      if (seenCombos.has(comboKey)) {
        duplicateCombos.push(comboKey);
        return; // Bỏ qua tổ hợp trùng
      }
      seenCombos.add(comboKey);
      
      // Lấy SKU sản phẩm cha (nếu có) và loại bỏ dấu gạch ngang sau PRD nếu có
      let parentSku = ($('input[name="sku"]').val() || 'SP').replace(/^PRD-?/i, 'PRD');
      
      // Chỉ lấy size và màu sắc cho SKU
      let sizePart = '';
      let colorPart = '';
      
      // Duyệt qua các thuộc tính để tìm size và màu
      combo.forEach(function(attr) {
        let attrName = attr.attr.toLowerCase();
        let attrValue = attr.value;
        
        // Lấy kích thước (size)
        if (attrName.includes('size') || attrName.includes('kích thước')) {
          sizePart = attrValue.toUpperCase().replace(/[^A-Z0-9]/g, '');
        } 
        // Lấy màu sắc
        else if (attrName.includes('màu')) {
          colorPart = getColorCode(attrValue) || 
                     removeVietnameseTones(attrValue)
                      .toUpperCase()
                      .replace(/[^A-Z0-9]/g, '')
                      .substring(0, 3);
        }
      });
      
      // Tạo SKU hoàn chỉnh: parentSku-size-color
      let variantSku = parentSku;
      if (sizePart) variantSku += '-' + sizePart;
      if (colorPart) variantSku += '-' + colorPart;
      
      // Tạo HTML cho các thuộc tính ẩn
      let attrStr = combo.map(c => 
        `<input type="hidden" name="variants[${currentRows + idx}][attributes][${c.attr_id}]" value="${c.value_id}">`
      ).join('');
      
      // Tạo HTML để hiển thị các thuộc tính
      let attrDisplay = combo.map(c => 
        `<span class="badge bg-light text-dark me-1">${c.attr}: ${c.value}</span>`
      ).join('');
      
      // Tạo dòng mới cho bảng biến thể
      tbody += `
        <tr data-combo-key="${comboKey}">
          <td class="align-middle">
            ${attrDisplay}
            <input type="hidden" name="variants[${currentRows + idx}][id]" value="">
            <input type="hidden" name="variants[${currentRows + idx}][_destroy]" value="0">
            ${attrStr}
          </td>
          <td class="text-center align-middle">
            <input type="text" class="form-control form-control-sm sku-input" 
                   name="variants[${currentRows + idx}][sku]" 
                   value="${variantSku}" 
                   required
                   oninput="this.value = this.value.toUpperCase()">
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm price-input" 
                   name="variants[${currentRows + idx}][price]" 
                   min="0" 
                   value="0" 
                   required>
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm sale-price-input" 
                   name="variants[${currentRows + idx}][sale_price]" 
                   min="0" 
                   value="0">
            <small class="text-muted discount-percentage" style="display: none;"></small>
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm" 
                   name="variants[${currentRows + idx}][stock]" 
                   min="0" 
                   value="0" 
                   required>
          </td>
          <td class="text-center">
            <input type="file" class="form-control form-control-sm" 
                   name="variants[${currentRows + idx}][image]" 
                   accept="image/*">
            <small class="text-muted d-block">(Tối đa 2MB)</small>
          </td>
          <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-danger remove-variant-row" title="Xoá biến thể">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>`;
    });
    
    // Thêm các dòng mới vào bảng
    if ($('#variantTable tbody').length === 0) {
      $('#variantTable').append('<tbody></tbody>');
    }
    
    if (currentRows === 0) {
      $('#variantTable tbody').html(tbody);
    } else {
      $('#variantTable tbody').append(tbody);
    }
    
    // Hiển thị bảng và ẩn thông báo không có biến thể
    $('#variantTableWrapper').show();
    $('#noVariantsMessage').hide();
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

  // Ẩn/hiện card biến thể và các trường giá phù hợp theo kiểu sản phẩm
  function toggleProductFields(productType) {
    if (productType === 'variant') {
      // Hiển thị thông báo cho sản phẩm biến thể
      $('#variantProductNote').show();
      
      // Hiển thị phần biến thể
      $('#variantSection').show();
      $('.simple-product-fields').hide();
      $('.variant-product-fields').show();
      
      // Kích hoạt lại tab đầu tiên
      $('#variantTab .nav-link').removeClass('active show');
      $('#variantTabContent .tab-pane').removeClass('active show');
      $('#generate-tab').addClass('active show');
      $('#generate').addClass('active show');
    } else {
      // Ẩn thông báo sản phẩm biến thể
      $('#variantProductNote').hide();
      
      // Ẩn phần biến thể
      $('#variantSection').hide();
      $('.simple-product-fields').show();
      $('.variant-product-fields').hide();
      
      // Xóa highlight khỏi tất cả các dòng biến thể
      $('#variantTable tbody tr').removeClass('table-active');
    }
  }
  
  // Thêm sự kiện click vào dòng biến thể (sử dụng event delegation)
  $(document).on('click', '#variantTable tbody tr', function() {
    // Xóa highlight khỏi tất cả các dòng
    $('#variantTable tbody tr').removeClass('table-active');
    // Thêm highlight cho dòng được chọn
    $(this).addClass('table-active');
  });
  
  // Khởi tạo khi trang được tải
  $(document).ready(function() {
    // Khởi tạo select2 cho các select box
    $('.variant-attribute-select').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Chọn giá trị'
    });
    
    // Gọi hàm khi thay đổi kiểu sản phẩm
    $('#productTypeSelect').on('change', function() {
      toggleProductFields($(this).val());
    });
    
    // Gọi hàm khi trang được tải để thiết lập trạng thái ban đầu
    toggleProductFields($('#productTypeSelect').val());
  });
</script>
@endpush

@push('scripts')
<!-- CKEditor cho mô tả chi tiết -->
<script>
// Kiểm tra xem CKEditor đã được tải chưa
if (typeof ClassicEditor === 'undefined') {
  document.write('<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"><\/script>');
  
  // Đợi CKEditor tải xong
  let checkCKEditor = setInterval(function() {
    if (typeof ClassicEditor !== 'undefined') {
      clearInterval(checkCKEditor);
      initCKEditor();
    }
  }, 100);
} else {
  initCKEditor();
}

function initCKEditor() {
  // Kiểm tra xem đã khởi tạo chưa
  if (document.querySelector('#ckeditor-description') && !document.querySelector('#ckeditor-description').hasAttribute('data-cke')) {
    ClassicEditor
      .create(document.querySelector('#ckeditor-description'))
      .catch(error => {
        console.error('Lỗi khi khởi tạo CKEditor:', error);
      });
  }
}
</script>
@endpush

@endsection
