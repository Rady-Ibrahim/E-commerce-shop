@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Gallery -->
        <div class="col-md-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img src="{{ $product->main_image ?? 'https://via.placeholder.com/600x600' }}" 
                         class="img-fluid rounded" 
                         id="mainImage"
                         alt="{{ $product->name }}">
                    @if($product->sale_price)
                        <div class="sale-badge">خصم</div>
                    @endif
                </div>
                <!-- Thumbnails -->
                @if($product->images->count() > 0)
                <div class="thumbnails d-flex gap-2">
                    <div class="thumbnail active" onclick="changeImage(this, '{{ $product->main_image }}')">
                        <img src="{{ $product->main_image }}" class="img-fluid rounded" alt="Thumbnail">
                    </div>
                    @foreach($product->images as $image)
                    <div class="thumbnail" onclick="changeImage(this, '{{ $image->url }}')">
                        <img src="{{ $image->url }}" class="img-fluid rounded" alt="Thumbnail">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="h2 mb-3">{{ $product->name }}</h1>
                    
                    <!-- السعر -->
                    <div class="mb-4">
                        @if($product->sale_price)
                            <div class="text-decoration-line-through text-muted mb-1">
                                {{ number_format($product->price, 2) }} ريال
                            </div>
                            <div class="h3 text-danger mb-0">
                                {{ number_format($product->sale_price, 2) }} ريال
                            </div>
                        @else
                            <div class="h3 mb-0">
                                {{ number_format($product->price, 2) }} ريال
                            </div>
                        @endif
                    </div>

                    <!-- المخزون -->
                    <div class="mb-4">
                        @if($product->stock > 0)
                            <span class="badge bg-success">متوفر في المخزون</span>
                            <small class="text-muted ms-2">الكمية المتوفرة: {{ $product->stock }}</small>
                        @else
                            <span class="badge bg-danger">غير متوفر في المخزون</span>
                        @endif
                    </div>

                    <!-- إضافة إلى السلة -->
                    @if($product->stock > 0)
                        <form id="addToCartForm" class="mb-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <div class="input-group" style="width: 120px;">
                                        <button type="button" class="btn btn-outline-secondary decrease-quantity" 
                                                @if($product->stock <= 1) disabled @endif>-</button>
                                        <input type="number" name="quantity" class="form-control text-center quantity-input" 
                                               value="1" min="1" max="{{ $product->stock }}"
                                               @if($product->stock <= 1) disabled @endif>
                                        <button type="button" class="btn btn-outline-secondary increase-quantity" 
                                                @if($product->stock <= 1) disabled @endif>+</button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        متوفر في المخزون: {{ $product->stock }} قطعة
                                    </small>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100" 
                                            @if($product->stock <= 0) disabled @endif>
                                        <i class="fas fa-cart-plus me-2"></i>
                                        أضف إلى السلة
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            عذراً، هذا المنتج غير متوفر حالياً في المخزون
                        </div>
                    @endif

                    <!-- الوصف -->
                    <div class="mb-4">
                        <h5 class="mb-3">الوصف</h5>
                        <div class="text-muted">
                            {{ $product->description }}
                        </div>
                    </div>

                    <!-- معلومات إضافية -->
                    <div class="mb-4">
                        <h5 class="mb-3">معلومات إضافية</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted">الرمز:</div>
                                <div>{{ $product->sku }}</div>
                            </div>
                            @if($product->category)
                            <div class="col-6">
                                <div class="text-muted">التصنيف:</div>
                                <div>{{ $product->category->name }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                        التفاصيل
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                        المواصفات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        التقييمات
                    </button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <h5 class="mb-3">تفاصيل المنتج</h5>
                    <div class="product-details">
                        {!! $product->details !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="specs" role="tabpanel">
                    <h5 class="mb-3">مواصفات المنتج</h5>
                    <div class="product-specs">
                        {!! $product->specifications !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <h5 class="mb-3">تقييمات العملاء</h5>
                    <div class="product-reviews">
                        <!-- سيتم إضافة نظام التقييمات لاحقاً -->
                        <p class="text-muted">لا توجد تقييمات بعد.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="related-products mt-5">
        <h3 class="h4 mb-4">منتجات ذات صلة</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-md-3">
                <div class="card product-card h-100">
                    @if($relatedProduct->sale_price)
                        <span class="sale-badge">خصم</span>
                    @endif
                    <img src="{{ $relatedProduct->main_image ?? 'https://via.placeholder.com/300x300' }}" 
                         class="card-img-top" 
                         alt="{{ $relatedProduct->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            @if($relatedProduct->sale_price)
                                <span class="text-decoration-line-through text-muted">{{ $relatedProduct->price }} ريال</span>
                                <span class="text-danger fw-bold">{{ $relatedProduct->sale_price }} ريال</span>
                            @else
                                <span class="fw-bold">{{ $relatedProduct->price }} ريال</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="btn btn-primary w-100">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.product-gallery {
    position: relative;
}

.main-image {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.main-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.sale-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.9rem;
}

.thumbnails {
    overflow-x: auto;
    padding-bottom: 10px;
}

.thumbnail {
    width: 80px;
    height: 80px;
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 4px;
    overflow: hidden;
}

.thumbnail.active {
    border-color: var(--bs-primary);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-meta {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.product-features {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.nav-tabs .nav-link {
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    color: var(--bs-primary);
    font-weight: bold;
}

.product-card {
    transition: transform 0.3s ease;
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card .card-img-top {
    height: 200px;
    object-fit: cover;
}

.quantity-input {
    width: 50px;
    text-align: center;
}

.input-group .btn {
    width: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
function changeImage(element, imageUrl) {
    // تحديث الصورة الرئيسية
    document.getElementById('mainImage').src = imageUrl;
    
    // تحديث حالة الصور المصغرة
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    element.classList.add('active');
}

$(document).ready(function() {
    const form = $('#addToCartForm');
    const quantityInput = form.find('.quantity-input');
    const decreaseBtn = form.find('.decrease-quantity');
    const increaseBtn = form.find('.increase-quantity');
    const submitBtn = form.find('button[type="submit"]');
    const maxStock = parseInt(quantityInput.attr('max'));

    // تحديث حالة الأزرار
    function updateButtonsState() {
        const currentValue = parseInt(quantityInput.val());
        decreaseBtn.prop('disabled', currentValue <= 1);
        increaseBtn.prop('disabled', currentValue >= maxStock);
    }

    // زيادة الكمية
    increaseBtn.click(function() {
        const currentValue = parseInt(quantityInput.val());
        if (currentValue < maxStock) {
            quantityInput.val(currentValue + 1).trigger('change');
        }
    });

    // تقليل الكمية
    decreaseBtn.click(function() {
        const currentValue = parseInt(quantityInput.val());
        if (currentValue > 1) {
            quantityInput.val(currentValue - 1).trigger('change');
        }
    });

    // تغيير الكمية يدوياً
    quantityInput.on('change input', function() {
        let value = parseInt($(this).val());
        
        // التحقق من صحة القيمة
        if (isNaN(value) || value < 1) {
            value = 1;
        } else if (value > maxStock) {
            value = maxStock;
        }
        
        $(this).val(value);
        updateButtonsState();
    });

    // إضافة إلى السلة
    form.submit(function(e) {
        e.preventDefault();
        
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...');

        $.ajax({
            url: '{{ route("cart.add") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                // تحديث عدد المنتجات في السلة
                updateCartCount(response.cart_count);
                
                // تحديث النافذة المنبثقة
                updateCartModal({
                    message: response.message,
                    cart_count: response.cart_count,
                    cart_total: response.cart_total
                });
                
                // إعادة تفعيل الزر
                submitBtn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                // إظهار رسالة الخطأ
                alert(xhr.responseJSON.message);
                
                // إعادة تفعيل الزر
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // تحديث حالة الأزرار عند التحميل
    updateButtonsState();
});
</script>
@endpush 