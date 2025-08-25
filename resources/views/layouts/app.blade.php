<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجرنا الإلكتروني</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --background-color: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 3rem 0;
            margin-top: 3rem;
        }

        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .category-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .category-card:hover img {
            transform: scale(1.1);
        }

        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 1rem;
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #34495e;
            border-color: #34495e;
        }

        .sale-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--secondary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">متجرنا</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/categories') }}">التصنيفات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/products') }}">المنتجات</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="{{ url('/cart') }}" class="btn btn-outline-light me-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        السلة
                        <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.7rem; display: none;">
                            0
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>عن المتجر</h5>
                    <p>متجرنا الإلكتروني يوفر أفضل المنتجات بأفضل الأسعار مع خدمة عملاء متميزة.</p>
                </div>
                <div class="col-md-4">
                    <h5>روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/about') }}" class="text-white">من نحن</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-white">اتصل بنا</a></li>
                        <li><a href="{{ url('/privacy') }}" class="text-white">سياسة الخصوصية</a></li>
                        <li><a href="{{ url('/terms') }}" class="text-white">الشروط والأحكام</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>تواصل معنا</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- نافذة منبثقة للسلة - تظهر عند إضافة منتج للسلة -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- رأس النافذة المنبثقة -->
                <div class="modal-header">
                    <h5 class="modal-title">تمت الإضافة للسلة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- محتوى النافذة المنبثقة -->
                <div class="modal-body">
                    <!-- رسالة التأكيد -->
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="cart-message mb-0"></p>
                    </div>
                    <!-- معلومات السلة -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>عدد المنتجات في السلة:</span>
                        <span class="cart-items-count fw-bold">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>إجمالي السلة:</span>
                        <span class="cart-total fw-bold">0 ريال</span>
                    </div>
                </div>
                <!-- أزرار التحكم -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">متابعة التسوق</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-primary">عرض السلة</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // دالة تحديث عدد المنتجات في السلة في شريط التنقل
    function updateCartCount(count) {
        const cartCount = $('.cart-count');
        if (count > 0) {
            cartCount.text(count).show(); // إظهار العدد إذا كان أكبر من صفر
        } else {
            cartCount.hide(); // إخفاء العداد إذا كانت السلة فارغة
        }
    }

    // دالة تحديث محتوى النافذة المنبثقة للسلة
    function updateCartModal(data) {
        // تحديث الرسالة وعدد المنتجات والإجمالي
        $('.cart-message').text(data.message);
        $('.cart-items-count').text(data.cart_count);
        $('.cart-total').text(data.cart_total + ' ريال');
        
        // عرض النافذة المنبثقة
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        cartModal.show();
    }

    // تحديث عدد المنتجات في السلة عند تحميل الصفحة
    $(document).ready(function() {
        // طلب عدد المنتجات من الخادم
        $.get('{{ route("cart.count") }}', function(data) {
            updateCartCount(data.count);
        });
    });
    </script>
    @endpush
</body>
</html> 