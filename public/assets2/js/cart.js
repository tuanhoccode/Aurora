/**
 * Aurora Shopping Cart Management
 * Quản lý giỏ hàng cho website Aurora
 */

class AuroraCart {
    constructor() {
        this.cartKey = 'aurora_cart';
        this.init();
    }

    init() {
        this.updateCartCount();
        this.updateCartMini();
        this.bindEvents();
        
        // Load cart nếu đang ở trang giỏ hàng
        if (window.location.pathname.includes('/shopping-cart')) {
            this.loadCart();
        }
    }

    // Lấy dữ liệu giỏ hàng từ localStorage
    getCart() {
        const cart = localStorage.getItem(this.cartKey);
        return cart ? JSON.parse(cart) : [];
    }

    // Lưu dữ liệu giỏ hàng vào localStorage
    saveCart(cart) {
        localStorage.setItem(this.cartKey, JSON.stringify(cart));
        this.updateCartCount();
        this.updateCartMini();
        this.triggerCartUpdate();
    }

    // Thêm sản phẩm vào giỏ hàng
    addToCart(product) {
        const cart = this.getCart();
        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += product.quantity || 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                variant: product.variant || '',
                quantity: product.quantity || 1
            });
        }

        this.saveCart(cart);
        this.showAddToCartSuccess();
        return cart;
    }

    // Cập nhật số lượng sản phẩm
    updateQuantity(productId, quantity) {
        const cart = this.getCart();
        const item = cart.find(item => item.id === productId);

        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(productId);
            } else {
                item.quantity = quantity;
                this.saveCart(cart);
            }
        }

        return cart;
    }

    // Xóa sản phẩm khỏi giỏ hàng
    removeFromCart(productId) {
        const cart = this.getCart();
        const updatedCart = cart.filter(item => item.id !== productId);
        this.saveCart(updatedCart);
        return updatedCart;
    }

    // Xóa toàn bộ giỏ hàng
    clearCart() {
        localStorage.removeItem(this.cartKey);
        this.updateCartCount();
        this.triggerCartUpdate();
    }

    // Lấy tổng số lượng sản phẩm
    getCartCount() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + item.quantity, 0);
    }

    // Lấy tổng giá trị giỏ hàng
    getCartTotal() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    // Cập nhật số lượng hiển thị trên header
    updateCartCount() {
        const count = this.getCartCount();
        const cartCountElements = document.querySelectorAll('#cart-count');
        
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }

    // Cập nhật cart mini
    updateCartMini() {
        const cart = this.getCart();
        const cartWidget = document.getElementById('cartmini-widget-container');
        const cartEmpty = document.getElementById('cartmini-empty');
        const cartSubtotal = document.getElementById('cartmini-subtotal');
        const cartItemCount = document.getElementById('mini-cart-item-count');
        const shippingInfo = document.getElementById('mini-cart-shipping-info');
        const freeShippingThreshold = 500000;
        
        if (!cartWidget) return;

        const total = this.getCartTotal();
        const itemCount = this.getCartCount();

        if (cartItemCount) {
            cartItemCount.textContent = itemCount;
        }

        if (cart.length === 0) {
            cartWidget.innerHTML = '';
            cartEmpty.classList.remove('d-none');
            cartSubtotal.textContent = this.formatCurrency(0);
            if(shippingInfo) shippingInfo.style.display = 'none';
            return;
        }
        
        cartEmpty.classList.add('d-none');
        
        cartWidget.innerHTML = cart.map(item => `
            <div class="cartmini__widget-item">
                <div class="cartmini__thumb">
                    <a href="#">
                        <img src="${item.image || '/assets2/img/product/cartmini/default.jpg'}" alt="${item.name}">
                    </a>
                </div>
                <div class="cartmini__content">
                    <h5 class="cartmini__title"><a href="#">${item.name}</a></h5>
                    <div class="cartmini__price-wrapper">
                        <span class="cartmini__price">${this.formatCurrency(item.price)}</span>
                        <span class="cartmini__quantity">x${item.quantity}</span>
                    </div>
                </div>
                <a href="javascript:void(0);" class="cartmini__del" onclick="window.auroraCart.removeFromCart(${item.id})"><i class="fa-regular fa-xmark"></i></a>
            </div>
        `).join('');
        
        cartSubtotal.textContent = this.formatCurrency(total);

        // Update shipping info
        if (shippingInfo) {
            if (total >= freeShippingThreshold) {
                shippingInfo.innerHTML = '🎉 Chúc mừng! Bạn đã được miễn phí vận chuyển.';
                shippingInfo.style.display = 'block';
            } else {
                const remaining = freeShippingThreshold - total;
                shippingInfo.innerHTML = `Mua thêm <strong>${this.formatCurrency(remaining)}</strong> để được miễn phí vận chuyển.`;
                shippingInfo.style.display = 'block';
            }
        }
    }

    // Load và hiển thị giỏ hàng
    loadCart() {
        const cart = this.getCart();
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCartDiv = document.getElementById('cart-view-empty');
        const nonEmptyCartDiv = document.getElementById('cart-view-non-empty');

        if (!cartItemsContainer || !emptyCartDiv || !nonEmptyCartDiv) return;

        if (cart.length === 0) {
            nonEmptyCartDiv.classList.add('d-none');
            emptyCartDiv.classList.remove('d-none');
        } else {
            nonEmptyCartDiv.classList.remove('d-none');
            emptyCartDiv.classList.add('d-none');
            
            cartItemsContainer.innerHTML = cart.map(item => this.createCartItemRow(item)).join('');
            this.bindCartItemEvents();
            this.updateCartPageSummary();
        }
    }

    // Tạo HTML cho một dòng sản phẩm ở trang giỏ hàng
    createCartItemRow(item) {
        const itemTotal = item.price * item.quantity;
        return `
            <div class="tp-cart-item">
                <div class="row align-items-center">
                    <div class="col-md-5 col-sm-5 col-5">
                        <div class="tp-cart-item-product">
                            <div class="tp-cart-item-product-thumb">
                                <a href="#"><img src="${item.image || '/assets2/img/product/cartmini/default.jpg'}" alt=""></a>
                            </div>
                            <h5 class="tp-cart-item-product-title"><a href="#">${item.name}</a></h5>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-2">
                        <h5 class="tp-cart-item-price text-center">${this.formatCurrency(item.price)}</h5>
                    </div>
                    <div class="col-md-3 col-sm-3 col-3">
                        <div class="tp-product-quantity">
                            <span class="tp-cart-minus" data-id="${item.id}"><svg width="10" height="2" viewBox="0 0 10 2" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
                            <input class="tp-product-input" type="text" value="${item.quantity}" data-id="${item.id}">
                            <span class="tp-cart-plus" data-id="${item.id}"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 1V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 5H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-2">
                        <h5 class="tp-cart-item-total text-end">${this.formatCurrency(itemTotal)}</h5>
                        <button class="tp-cart-item-remove d-md-none" data-id="${item.id}"><i class="fa-regular fa-xmark"></i></button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Gắn sự kiện cho các nút trong giỏ hàng
    bindCartItemEvents() {
        document.querySelectorAll('.tp-cart-minus').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuantityChange(e, -1));
        });
        document.querySelectorAll('.tp-cart-plus').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuantityChange(e, 1));
        });
        document.querySelectorAll('.tp-product-input').forEach(input => {
            input.addEventListener('change', (e) => this.handleQuantityInputChange(e));
        });
        document.querySelectorAll('.tp-cart-item-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = parseInt(e.currentTarget.dataset.id);
                this.removeFromCart(productId);
            });
        });
    }

    // Xử lý thay đổi số lượng
    handleQuantityChange(event, change) {
        const productId = parseInt(event.currentTarget.dataset.id);
        const input = document.querySelector(`.tp-product-input[data-id="${productId}"]`);
        let newQuantity = parseInt(input.value) + change;
        if (newQuantity < 1) newQuantity = 1;
        this.updateQuantity(productId, newQuantity);
    }
    
    handleQuantityInputChange(event) {
        const productId = parseInt(event.currentTarget.dataset.id);
        let newQuantity = parseInt(event.currentTarget.value);
        if (isNaN(newQuantity) || newQuantity < 1) {
            newQuantity = 1;
        }
        this.updateQuantity(productId, newQuantity);
    }

    // Cập nhật tất cả số lượng từ trang giỏ hàng
    updateAllQuantities() {
        document.querySelectorAll('.tp-product-input').forEach(input => {
            const productId = parseInt(input.dataset.id);
            const quantity = parseInt(input.value);
            if (!isNaN(quantity) && quantity > 0) {
                 const cart = this.getCart();
                 const item = cart.find(i => i.id === productId);
                 if (item && item.quantity !== quantity) {
                     item.quantity = quantity;
                 }
                 this.saveCart(cart);
            }
        });
        this.showCustomNotification('Giỏ hàng đã được cập nhật', 'success');
    }

    // Cập nhật tổng tiền ở trang giỏ hàng
    updateCartPageSummary() {
        const total = this.getCartTotal();
        const totalElement = document.getElementById('cart-total');
        const subtotalElement = document.getElementById('cart-subtotal');
        if (totalElement) totalElement.textContent = this.formatCurrency(total);
        if (subtotalElement) subtotalElement.textContent = this.formatCurrency(total);
    }

    // Hiển thị thông báo thêm vào giỏ hàng thành công
    showAddToCartSuccess() {
        if (typeof toastr !== 'undefined') {
            toastr.success('Đã thêm sản phẩm vào giỏ hàng!');
        } else {
            this.showCustomNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
        }
    }

    // Hiển thị thông báo tùy chỉnh
    showCustomNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `custom-notification custom-notification--${type}`;
        notification.innerHTML = `
            <div class="custom-notification__content">
                <i class="fa-solid fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Hiển thị notification
        setTimeout(() => {
            notification.classList.add('active');
        }, 100);

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.classList.remove('active');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Kích hoạt sự kiện cập nhật giỏ hàng
    triggerCartUpdate() {
        const event = new CustomEvent('cartUpdated', {
            detail: {
                cart: this.getCart(),
                count: this.getCartCount(),
                total: this.getCartTotal()
            }
        });
        document.dispatchEvent(event);
    }

    // Bind các sự kiện
    bindEvents() {
        // Lắng nghe sự kiện thêm vào giỏ hàng
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                const button = e.target.closest('.add-to-cart-btn');
                const productId = button.dataset.productId;
                const productName = button.dataset.productName;
                const productPrice = parseFloat(button.dataset.productPrice);
                const productImage = button.dataset.productImage;
                const productVariant = button.dataset.productVariant || '';

                this.addToCart({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    variant: productVariant
                });
            }
        });

        // Lắng nghe sự kiện cập nhật giỏ hàng
        document.addEventListener('cartUpdated', (e) => {
            // Cập nhật mini cart nếu có
            if (typeof loadMiniCart === 'function') {
                loadMiniCart();
            }
            
            // Cập nhật trang giỏ hàng nếu đang ở trang đó
            if (window.location.pathname.includes('/shopping-cart')) {
                this.loadCart();
            }
        });
    }

    // Format giá tiền theo định dạng Việt Nam
    formatCurrency(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }

    // Kiểm tra sản phẩm có trong giỏ hàng không
    isInCart(productId) {
        const cart = this.getCart();
        return cart.some(item => item.id === productId);
    }

    // Lấy số lượng sản phẩm trong giỏ hàng
    getProductQuantity(productId) {
        const cart = this.getCart();
        const item = cart.find(item => item.id === productId);
        return item ? item.quantity : 0;
    }
}

// Khởi tạo cart khi trang load
document.addEventListener('DOMContentLoaded', function() {
    window.auroraCart = new AuroraCart();
});

// Export cho sử dụng global
window.AuroraCart = AuroraCart;

// Thêm CSS cho custom notification
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

.custom-notification--success {
    border-left-color: #28a745;
}

.custom-notification--error {
    border-left-color: #dc3545;
}

.custom-notification--warning {
    border-left-color: #ffc107;
}

.custom-notification__content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.custom-notification__content i {
    font-size: 18px;
}

.custom-notification--success .custom-notification__content i {
    color: #28a745;
}

.custom-notification--error .custom-notification__content i {
    color: #dc3545;
}

.custom-notification--warning .custom-notification__content i {
    color: #ffc107;
}

.custom-notification__content span {
    color: #333;
    font-size: 14px;
    font-weight: 500;
}
</style>
`;

// Thêm CSS vào head nếu chưa có
if (!document.querySelector('#aurora-cart-notification-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'aurora-cart-notification-styles';
    styleElement.innerHTML = notificationStyles;
    document.head.appendChild(styleElement);
} 