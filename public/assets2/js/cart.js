/**
 * Aurora Shopping Cart Management
 * Quản lý giỏ hàng cho website Aurora
 */

// Aurora Shopping Cart - Backend Only Version
// Tất cả thao tác giỏ hàng đều qua API backend, không dùng localStorage

    // Hiển thị thông báo tùy chỉnh
function showCustomNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `custom-notification custom-notification--${type}`;
        notification.innerHTML = `
            <div class="custom-notification__content">
                <i class="fa-solid fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
    setTimeout(() => { notification.classList.add('active'); }, 100);
        setTimeout(() => {
            notification.classList.remove('active');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

// Thêm sản phẩm vào giỏ hàng (gọi API backend)
function addToCart(productId, quantity = 1, variant = '') {
    fetch('/shopping-cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity, variant: variant })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showCustomNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            location.reload();
        } else {
            showCustomNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(() => showCustomNotification('Có lỗi xảy ra!', 'error'));
}

// Cập nhật số lượng sản phẩm (gọi API backend)
function updateCartQty(itemId, newQty) {
    if (newQty < 1) return;
    fetch('/shopping-cart/update/' + itemId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showCustomNotification('Đã cập nhật số lượng!', 'success');
            location.reload();
        } else {
            showCustomNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(() => showCustomNotification('Có lỗi xảy ra!', 'error'));
}

// Gắn sự kiện cho các nút trên trang (nếu cần)
document.addEventListener('DOMContentLoaded', function() {
    // Nút thêm vào giỏ hàng
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = btn.dataset.productId;
            const quantity = btn.dataset.productQuantity ? parseInt(btn.dataset.productQuantity) : 1;
            const variant = btn.dataset.productVariant || '';
            addToCart(productId, quantity, variant);
        });
    });
    // Nút cập nhật số lượng (nếu có input)
    document.querySelectorAll('.tp-product-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const productId = input.dataset.id;
            let newQty = parseInt(input.value);
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            updateCartQty(productId, newQty);
        });
    });
});

// Thêm CSS cho custom notification (nếu chưa có)
const notificationStyles = `
<style>
.custom-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 15px 20px;
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
    border-left: 4px solid #007bff;
}
.custom-notification.active {
    transform: translateX(0);
}
.custom-notification--success { border-left-color: #28a745; }
.custom-notification--error { border-left-color: #dc3545; }
.custom-notification--warning { border-left-color: #ffc107; }
.custom-notification__content { display: flex; align-items: center; gap: 10px; }
.custom-notification__content i { font-size: 18px; }
.custom-notification--success .custom-notification__content i { color: #28a745; }
.custom-notification--error .custom-notification__content i { color: #dc3545; }
.custom-notification--warning .custom-notification__content i { color: #ffc107; }
.custom-notification__content span { color: #333; font-size: 14px; font-weight: 500; }
</style>
`;
if (!document.querySelector('#aurora-cart-notification-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'aurora-cart-notification-styles';
    styleElement.innerHTML = notificationStyles;
    document.head.appendChild(styleElement);
} 