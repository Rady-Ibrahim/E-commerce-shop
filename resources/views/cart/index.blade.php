@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- عنوان الصفحة وأزرار التحكم -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">سلة المشتريات</h1>
                @if($cart->items->isNotEmpty())
                    <!-- زر تفريغ السلة (يظهر فقط إذا كانت السلة غير فارغة) -->
                    <button type="button" class="btn btn-outline-danger" id="clearCart">
                        <i class="fas fa-trash me-2"></i>
                        تفريغ السلة
                    </button>
                @endif
            </div>

            <!-- عرض محتويات السلة -->
            @if($cart->items->isNotEmpty())
                <div class="card">
                    <!-- جدول المنتجات -->
                    <div class="table-responsive">
                        <table class="table">
                            <!-- رأس الجدول -->
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <!-- محتوى الجدول -->
                            <tbody>
                                @foreach($cart->items as $item)
                                <tr>
                                    <!-- معلومات المنتج -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <!-- صورة المنتج -->
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="rounded me-3" 
                                                     style="width: 64px; height: 64px; object-fit: cover;">
                                            @endif
                                            <!-- اسم المنتج ورقم التعريف -->
                                            <div>
                                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                <small class="text-muted">{{ $item->product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- عرض السعر (العادي أو المخفض) -->
                                    <td>
                                        @if($item->product->sale_price)
                                            <div class="text-decoration-line-through text-muted">
                                                {{ number_format($item->product->price, 2) }}
                                            </div>
                                            <div class="text-danger">
                                                {{ number_format($item->product->sale_price, 2) }}
                                            </div>
                                        @else
                                            {{ number_format($item->product->price, 2) }}
                                        @endif
                                    </td>
                                    <!-- التحكم في الكمية -->
                                    <td>
                                        <div class="input-group" style="width: 120px;">
                                            <!-- زر تقليل الكمية -->
                                            <button type="button" class="btn btn-outline-secondary btn-sm decrease-quantity" 
                                                    data-item-id="{{ $item->id }}">-</button>
                                            <!-- حقل إدخال الكمية -->
                                            <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                                   value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                                   data-item-id="{{ $item->id }}">
                                            <!-- زر زيادة الكمية -->
                                            <button type="button" class="btn btn-outline-secondary btn-sm increase-quantity" 
                                                    data-item-id="{{ $item->id }}">+</button>
                                        </div>
                                    </td>
                                    <!-- المجموع الفرعي للمنتج -->
                                    <td>
                                        {{ number_format($item->subtotal, 2) }} ريال
                                    </td>
                                    <!-- زر حذف المنتج -->
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item" 
                                                data-item-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- رسالة السلة الفارغة -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">سلة المشتريات فارغة</h5>
                    <p class="text-muted mb-4">لم تقم بإضافة أي منتجات إلى السلة بعد</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>
                        تصفح المنتجات
                    </a>
                </div>
            @endif
        </div>

        <!-- ملخص الطلب (يظهر فقط إذا كانت السلة غير فارغة) -->
        @if($cart->items->isNotEmpty())
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">ملخص الطلب</h5>
                    </div>
                    <div class="card-body">
                        <!-- عدد المنتجات -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>عدد المنتجات:</span>
                            <span>{{ $cart->items->sum('quantity') }}</span>
                        </div>
                        <!-- إجمالي السلة -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>الإجمالي:</span>
                            <span class="fw-bold">{{ number_format($cart->total, 2) }} ريال</span>
                        </div>
                        <hr>
                        <!-- زر متابعة الشراء -->
                        <a href="{{ route('checkout') }}" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card me-2"></i>
                            متابعة الشراء
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // دالة تحديث كمية المنتج في السلة
    function updateQuantity(itemId, quantity) {
        $.ajax({
            url: `/cart/update/${itemId}`,
            type: 'PUT',
            data: { quantity: quantity },
            success: function(response) {
                // تحديث الصفحة بعد نجاح العملية
                location.reload();
            },
            error: function(xhr) {
                // عرض رسالة الخطأ
                alert(xhr.responseJSON.message);
            }
        });
    }

    // معالجة زيادة الكمية
    $('.increase-quantity').click(function() {
        const itemId = $(this).data('item-id');
        const input = $(`.quantity-input[data-item-id="${itemId}"]`);
        const currentValue = parseInt(input.val());
        const maxValue = parseInt(input.attr('max'));
        
        // التحقق من عدم تجاوز الحد الأقصى
        if (currentValue < maxValue) {
            input.val(currentValue + 1).trigger('change');
        }
    });

    // معالجة تقليل الكمية
    $('.decrease-quantity').click(function() {
        const itemId = $(this).data('item-id');
        const input = $(`.quantity-input[data-item-id="${itemId}"]`);
        const currentValue = parseInt(input.val());
        
        // التحقق من عدم النزول عن الحد الأدنى
        if (currentValue > 1) {
            input.val(currentValue - 1).trigger('change');
        }
    });

    // معالجة تغيير الكمية يدوياً
    $('.quantity-input').change(function() {
        const itemId = $(this).data('item-id');
        const quantity = parseInt($(this).val());
        updateQuantity(itemId, quantity);
    });

    // معالجة حذف منتج من السلة
    $('.remove-item').click(function() {
        if (confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
            const itemId = $(this).data('item-id');
            
            $.ajax({
                url: `/cart/remove/${itemId}`,
                type: 'DELETE',
                success: function(response) {
                    // تحديث الصفحة بعد الحذف
                    location.reload();
                }
            });
        }
    });

    // معالجة تفريغ السلة
    $('#clearCart').click(function() {
        if (confirm('هل أنت متأكد من تفريغ السلة؟')) {
            $.ajax({
                url: '/cart/clear',
                type: 'DELETE',
                success: function(response) {
                    // تحديث الصفحة بعد التفريغ
                    location.reload();
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
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
@endsection 