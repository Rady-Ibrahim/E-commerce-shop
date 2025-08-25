@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">نتائج البحث</li>
        </ol>
    </nav>

    <!-- Search Header -->
    <div class="search-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2">نتائج البحث</h1>
                <p class="text-muted mb-0">
                    @if($products->total() > 0)
                        تم العثور على {{ $products->total() }} منتج لـ "{{ $query }}"
                    @else
                        لم يتم العثور على نتائج لـ "{{ $query }}"
                    @endif
                </p>
            </div>
            <div class="col-md-4">
                <form action="{{ route('products.search') }}" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control" placeholder="ابحث عن منتجات..." 
                           value="{{ $query }}" required>
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($products->total() > 0)
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تصفية النتائج</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.search') }}" method="GET" id="filterForm">
                        <input type="hidden" name="q" value="{{ $query }}">
                        
                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6>السعر</h6>
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
                        </div>

                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6>التصنيفات</h6>
                            <div class="list-group">
                                @foreach($categories as $category)
                                <label class="list-group-item">
                                    <input class="form-check-input me-2" type="checkbox" name="category[]" 
                                           value="{{ $category->id }}"
                                           {{ in_array($category->id, (array)request('category')) ? 'checked' : '' }}>
                                    {{ $category->name }}
                                    <span class="badge bg-secondary float-end">{{ $category->products_count }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

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

                        <!-- Sale Filter -->
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

                        <button type="submit" class="btn btn-primary w-100">تطبيق التصفية</button>
                    </form>
                </div>
            </div>

            <!-- Search Tips -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">نصائح للبحث</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            استخدم كلمات مفتاحية محددة
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            تحقق من كتابة الكلمات بشكل صحيح
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            جرب كلمات مفتاحية مختلفة
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            استخدم الفلاتر لتصفية النتائج
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-md-9">
            <!-- Sort Options -->
            <div class="d-flex justify-content-end mb-4">
                <select class="form-select" style="width: auto;" onchange="window.location.href=this.value">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'relevance']) }}" 
                            {{ request('sort') == 'relevance' ? 'selected' : '' }}>
                        الأكثر صلة
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" 
                            {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                        السعر: من الأقل للأعلى
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" 
                            {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                        السعر: من الأعلى للأقل
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" 
                            {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                        الاسم: أ-ي
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}" 
                            {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                        الاسم: ي-أ
                    </option>
                </select>
            </div>

            <!-- Active Filters -->
            @if(request()->hasAny(['min_price', 'max_price', 'category', 'in_stock', 'on_sale']))
            <div class="active-filters mb-4">
                <div class="d-flex flex-wrap gap-2">
                    @if(request('min_price') || request('max_price'))
                        <span class="badge bg-light text-dark">
                            السعر: 
                            @if(request('min_price'))
                                من {{ request('min_price') }}
                            @endif
                            @if(request('max_price'))
                                إلى {{ request('max_price') }}
                            @endif
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" 
                               class="text-dark ms-2">×</a>
                        </span>
                    @endif

                    @if(request('category'))
                        @foreach((array)request('category') as $categoryId)
                            @php $category = $categories->firstWhere('id', $categoryId); @endphp
                            @if($category)
                                <span class="badge bg-light text-dark">
                                    {{ $category->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['category' => array_diff((array)request('category'), [$categoryId])]) }}" 
                                       class="text-dark ms-2">×</a>
                                </span>
                            @endif
                        @endforeach
                    @endif

                    @if(request('in_stock'))
                        <span class="badge bg-light text-dark">
                            متوفر حالياً
                            <a href="{{ request()->fullUrlWithQuery(['in_stock' => null]) }}" 
                               class="text-dark ms-2">×</a>
                        </span>
                    @endif

                    @if(request('on_sale'))
                        <span class="badge bg-light text-dark">
                            المنتجات المخفضة
                            <a href="{{ request()->fullUrlWithQuery(['on_sale' => null]) }}" 
                               class="text-dark ms-2">×</a>
                        </span>
                    @endif

                    <a href="{{ route('products.search', ['q' => $query]) }}" class="btn btn-sm btn-outline-secondary">
                        مسح جميع الفلاتر
                    </a>
                </div>
            </div>
            @endif

            <!-- Products Grid -->
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-md-4">
                    <div class="card product-card h-100">
                        @if($product->sale_price)
                            <span class="sale-badge">خصم</span>
                        @endif
                        <img src="{{ $product->main_image ?? 'https://via.placeholder.com/300x300' }}" 
                             class="card-img-top" 
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
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- No Results -->
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted"></i>
        </div>
        <h3 class="h4 mb-3">لم يتم العثور على نتائج</h3>
        <p class="text-muted mb-4">جرب استخدام كلمات مفتاحية مختلفة أو تصفح جميع المنتجات</p>
        <div>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                تصفح جميع المنتجات
            </a>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
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

.active-filters .badge {
    font-size: 0.9rem;
    padding: 8px 12px;
}

.list-group-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.search-header {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث النموذج عند تغيير أي فلتر
    const filterForm = document.getElementById('filterForm');
    const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            filterForm.submit();
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
@endsection 