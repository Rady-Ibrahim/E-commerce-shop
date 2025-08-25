@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
            @if($category->parent)
                <li class="breadcrumb-item"><a href="{{ url('/categories/{slug}' . $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">التصفية</h5>
                </div>
                <div class="card-body">
                    <!-- Price Range Filter -->
                    <div class="mb-4">
                        <h6>السعر</h6>
                        <form action="{{ url()->current() }}" method="GET" id="priceFilter">
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="من" 
                                           value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="إلى" 
                                           value="{{ request('max_price') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">تطبيق</button>
                        </form>
                    </div>

                    <!-- Subcategories -->
                    @if($category->children->count() > 0)
                    <div class="mb-4">
                        <h6>التصنيفات الفرعية</h6>
                        <div class="list-group">
                            @foreach($category->children as $subcategory)
                            <a href="{{ url('/categories/' . $subcategory->slug) }}" 
                               class="list-group-item list-group-item-action {{ request()->is('categories/' . $subcategory->slug) ? 'active' : '' }}">
                                {{ $subcategory->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Availability Filter -->
                    <div class="mb-4">
                        <h6>التوفر</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="inStock" 
                                   {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="inStock">
                                متوفر حالياً
                            </label>
                        </div>
                    </div>

                    <!-- Discount Filter -->
                    <div class="mb-4">
                        <h6>العروض</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="on_sale" id="onSale" 
                                   {{ request('on_sale') ? 'checked' : '' }}>
                            <label class="form-check-label" for="onSale">
                                المنتجات المخفضة
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <!-- Category Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-0">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-muted mt-2">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="d-flex align-items-center">
                    <select class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ url()->current() }}?sort=latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                            الأحدث
                        </option>
                        <option value="{{ url()->current() }}?sort=price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            السعر: من الأقل للأعلى
                        </option>
                        <option value="{{ url()->current() }}?sort=price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            السعر: من الأعلى للأقل
                        </option>
                        <option value="{{ url()->current() }}?sort=name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                            الاسم: أ-ي
                        </option>
                        <option value="{{ url()->current() }}?sort=name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                            الاسم: ي-أ
                        </option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-4">
                @forelse($products as $product)
                <div class="col-md-4">
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
                                <!-- مجموعة الأزرار -->
                                <div class="btn-group">
                                    <!-- زر إزالة (placeholder للحفاظ على تصميم الصورة) -->
                                    <button type="button" class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-times"></i> 
                                    </button>
                                    <!-- زر عرض تفاصيل المنتج -->
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <!-- زر إضافة للسلة -->
                            <div class="mt-3">
                                @if($product->stock > 0)
                                    <button type="button" class="btn btn-primary w-100 add-to-cart" 
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-product-price="{{ $product->sale_price ?? $product->price }}">
                                        <i class="fas fa-cart-plus me-2"></i> أضف إلى السلة
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-times me-2"></i> نفذ المخزون
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        لا توجد منتجات في هذا التصنيف.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // معالجة الفلاتر: تحديث الرابط عند تغيير أي فلتر
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // تحديث معلمات الرابط بناءً على حالة الفلتر
            const url = new URL(window.location.href);
            if (this.checked) {
                url.searchParams.set(this.name, '1'); // إضافة الفلتر إذا كان مفعلاً
            } else {
                url.searchParams.delete(this.name); // إزالة الفلتر إذا كان معطلاً
            }
            window.location.href = url.toString(); // الانتقال إلى الرابط المحدث
        });
    });

    // معالجة إضافة المنتجات للسلة
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            // الحصول على بيانات المنتج من خصائص الزر
            const productId = this.dataset.productId; // معرف المنتج
            const productName = this.dataset.productName; // اسم المنتج
            const productPrice = this.dataset.productPrice; // سعر المنتج
            
            // تعطيل الزر وإظهار أيقونة التحميل
            this.disabled = true; // منع النقر المتكرر
            const originalHtml = this.innerHTML; // حفظ المحتوى الأصلي للزر
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; // إظهار أيقونة التحميل

            // إرسال طلب AJAX لإضافة المنتج للسلة
            fetch('{{ route("cart.add") }}', {
                method: 'POST', // طريقة الطلب
                headers: {
                    'Content-Type': 'application/json', // نوع البيانات
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // رمز الحماية
                },
                body: JSON.stringify({
                    product_id: productId, // معرف المنتج
                    quantity: 1 // الكمية (افتراضياً 1)
                })
            })
            .then(response => response.json()) // تحويل الاستجابة إلى JSON
            .then(data => {
                // تحديث واجهة المستخدم بعد نجاح العملية
                updateCartCount(data.cart_count); // تحديث عدد المنتجات في السلة
                
                // تحديث النافذة المنبثقة مع تفاصيل العملية
                updateCartModal({
                    message: data.message, // رسالة النجاح
                    cart_count: data.cart_count, // عدد المنتجات في السلة
                    cart_total: data.cart_total, // إجمالي السلة
                    product: {
                        name: productName, // اسم المنتج المضاف
                        quantity: 1, // الكمية المضافة
                        price: productPrice // سعر المنتج
                    }
                });
            })
            .catch(error => {
                // معالجة الأخطاء
                alert('حدث خطأ أثناء إضافة المنتج إلى السلة');
            })
            .finally(() => {
                // إعادة تفعيل الزر وإعادة محتواه الأصلي
                this.disabled = false; // إعادة تفعيل الزر
                this.innerHTML = originalHtml; // إعادة المحتوى الأصلي
            });
        });
    });
});
</script>
@endpush 