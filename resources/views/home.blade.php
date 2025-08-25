@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner rounded">
            <div class="carousel-item active">
                <img src="\storage\images\banner3.jpg" class="d-block w-100" alt="عرض خاص">
                <div class="carousel-caption">
                    <h2>عروض حصرية</h2>
                    <p>خصومات تصل إلى 50% على المنتجات المختارة</p>
                    <a href="{{ url('/products') }}" class="btn btn-primary">تسوق الآن</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="\storage\images\banner3.jpg" class="d-block w-100" alt="منتجات جديدة">
                <div class="carousel-caption">
                    <h2>منتجات جديدة</h2>
                    <p>اكتشف أحدث المنتجات في متجرنا</p>
                    <a href="{{ url('/products') }}" class="btn btn-primary">استكشف</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="\storage\images\banner3.jpg" class="d-block w-100" alt="شحن مجاني">
                <div class="carousel-caption">
                    <h2>شحن مجاني</h2>
                    <p>لجميع الطلبات التي تزيد عن 200 ريال</p>
                    <a href="{{ url('/products') }}" class="btn btn-primary">تسوق الآن</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Categories Section -->
    <section class="mb-5">
        <h2 class="mb-4">تصفح حسب التصنيف</h2>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-4">
                <a href="{{ url('/categories/' . $category->slug) }}" class="text-decoration-none">
                    <div class="category-card">
                        <img src="{{ $category->image ?? 'https://via.placeholder.com/400x300' }}" alt="{{ $category->name }}">
                        <div class="category-overlay">
                            <h3 class="h5 mb-0">{{ $category->name }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="mb-5">
        <h2 class="mb-4">منتجات مميزة</h2>
        <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    @if($product->sale_price)
                        <span class="sale-badge">خصم</span>
                    @endif
                    <img src="{{ $product->main_image ?? 'https://via.placeholder.com/300x300' }}" 
                         class="card-img-top product-image" 
                         alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($product->sale_price)
                                    <span class="text-decoration-line-through text-muted">{{ $product->price }} ريال</span>
                                    <span class="text-danger fw-bold">{{ $product->sale_price }} ريال</span>
                                @else
                                    <span class="fw-bold">{{ $product->price }} ريال</span>
                                @endif
                            </div>
                            <a href="{{ url('/products/' . $product->slug) }}" class="btn btn-primary">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Features Section -->
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                    <h5>توصيل سريع</h5>
                    <p class="text-muted">توصيل لجميع أنحاء المملكة</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="fas fa-undo fa-3x mb-3 text-primary"></i>
                    <h5>إرجاع مجاني</h5>
                    <p class="text-muted">خلال 14 يوم من الشراء</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="fas fa-lock fa-3x mb-3 text-primary"></i>
                    <h5>دفع آمن</h5>
                    <p class="text-muted">حماية كاملة للمعاملات</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
                    <h5>دعم 24/7</h5>
                    <p class="text-muted">خدمة عملاء على مدار الساعة</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="bg-light p-5 rounded mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>اشترك في النشرة البريدية</h3>
                <p class="text-muted">احصل على آخر العروض والتخفيضات</p>
            </div>
            <div class="col-md-6">
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="أدخل بريدك الإلكتروني">
                    <button type="submit" class="btn btn-primary">اشتراك</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection 