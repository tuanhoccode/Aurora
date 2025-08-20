<footer>
    <div class="tp-footer-area tp-footer-style-2" data-bg-color="footer-bg-white">
        <div class="tp-footer-top pt-95 pb-40">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                        <div class="tp-footer-widget footer-col-1 mb-50">
                            <div class="tp-footer-widget-content">
                                <div class="tp-footer-logo">
                                    <a href="{{ route('home') }}">
                                        <img src="{{ asset('assets2/img/logo/logo.svg') }}" alt="logo">
                                    </a>
                                </div>
                                <p class="tp-footer-desc">Thiên nhiên tạo ra địa chấn - Thời trang Aurora tạo nên điểm nhấn.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="tp-footer-widget footer-col-2 mb-50">
                            <h4 class="tp-footer-widget-title">Chính sách</h4>
                            <div class="tp-footer-widget-content">
                                <ul>
                                    <li><a href="#">Chính sách đổi trả</a></li>
                                    <li><a href="#">Chính sách khuyến mãi</a></li>
                                    <li><a href="#">Chính sách bảo mật</a></li>
                                    <li><a href="#">Chính sách giao nhận</a></li>
                                    <li><a href="#">Chính sách thanh toán</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="tp-footer-widget footer-col-3 mb-50">
                            <h4 class="tp-footer-widget-title">Thông Tin</h4>
                            <div class="tp-footer-widget-content">
                                <ul>
                                    <li><a href="#">Về Aurora</a></li>
                                    <li><a href="#">Điều khoản</a></li>
                                    <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                                    <li><a href="#">Tin tức</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-3 col-md-4 col-sm-6">
                        <div class="tp-footer-widget footer-col-4 mb-50">
                            <h4 class="tp-footer-widget-title">Đăng ký nhận tin</h4>
                            <div class="tp-footer-contact mt-20">
                                <div class="tp-footer-contact-item d-flex align-items-center">
                                    <div class="tp-footer-contact-icon"><span><i class="fa-solid fa-phone"></i></span></div>
                                    <div class="tp-footer-contact-content"><p><a href="tel:0336689888">0336689888</a></p></div>
                                </div>
                                <div class="tp-footer-contact-item d-flex align-items-center">
                                    <div class="tp-footer-contact-icon"><span><i class="fa-solid fa-envelope"></i></span></div>
                                    <div class="tp-footer-contact-content"><p><a href="mailto:aurora@support.com">aurora@support.com</a></p></div>
                                </div>
                                <div class="tp-footer-contact-item d-flex align-items-center">
                                    <div class="tp-footer-contact-icon"><span><i class="fa-solid fa-location-dot"></i></span></div>
                                    <div class="tp-footer-contact-content"><p><a>Tòa nhà FPT Polytechnic, Cổng số 2, 13 P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội.</a></p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<style>
/* Footer Style Enhancements */
.tp-footer-area {
    background-color: #f8f9fa; /* Softer than pure white */
    border-top: 3px solid #b48c5a; /* Decorative top border */
    color: #555;
}
.tp-footer-widget-title {
    color: #1c1c1e;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0e0e0; /* Subtle underline */
}
.tp-footer-desc {
    color: #666;
}
.tp-footer-widget-content ul li a {
    color: #555;
    transition: color 0.3s, padding-left 0.3s;
}
.tp-footer-widget-content ul li a:hover {
    color: #b48c5a;
    padding-left: 5px;
}
.tp-footer-social a {
    background-color: #e9ecef;
    color: #555;
    border-radius: 50%;
    transition: background-color 0.3s, color 0.3s, transform 0.3s;
}
.tp-footer-social a:hover {
    background-color: #b48c5a;
    color: #ffffff;
    transform: translateY(-3px);
}
.tp-footer-subscribe p {
    color: #666;
    margin-bottom: 15px;
}
.tp-footer-subscribe-input {
    position: relative;
}
.tp-footer-subscribe-input input {
    width: 100%;
    height: 50px;
    padding: 10px 55px 10px 20px;
    border: 1px solid #e0e0e0;
    background-color: #ffffff;
    color: #333;
    border-radius: 8px;
    transition: border-color 0.3s, box-shadow 0.3s;
}
.tp-footer-subscribe-input input:focus {
    border-color: #b48c5a;
    box-shadow: 0 0 0 3px rgba(180, 140, 90, 0.15);
    outline: none;
}
.tp-footer-subscribe-input button {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    width: 50px;
    background-color: #b48c5a;
    color: #fff;
    border: 0;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.tp-footer-subscribe-input button:hover {
    background-color: #9d784a;
}
.tp-footer-subscribe-input button i {
    font-size: 1.1rem;
}
.tp-footer-subscribe-input button:hover i {
    transform: translateX(3px);
}
.tp-footer-contact-item {
    gap: 15px;
    margin-bottom: 12px;
}
.tp-footer-contact-icon {
    font-size: 1.1rem;
    color: #b48c5a;
    width: 20px;
    text-align: center;
}
.tp-footer-contact-content p {
    margin: 0;
}
.tp-footer-contact-content a {
    color: #555;
    transition: color 0.3s;
}
.tp-footer-contact-content a:hover {
    color: #b48c5a;
}
.tp-footer-bottom {
    background-color: #f1f2f3;
    border-top: 1px solid #e0e0e0;
    padding: 15px 0;
}
.tp-footer-copyright p,
.tp-footer-copyright p a {
    color: #777;
    margin-bottom: 0;
}
.tp-footer-copyright p a:hover {
    color: #1c1c1e;
}
</style>
