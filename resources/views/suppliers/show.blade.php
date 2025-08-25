@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">{{ $supplier->name }}</h1>
            <p class="text-muted mt-2">تفاصيل المورد</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                تعديل المورد
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Supplier Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">معلومات المورد</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block">الحالة</label>
                        @if($supplier->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">غير نشط</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">البريد الإلكتروني</label>
                        <div>{{ $supplier->email ?: 'غير محدد' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">رقم الهاتف</label>
                        <div>{{ $supplier->phone }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">الشخص المسؤول</label>
                        <div>{{ $supplier->contact_person ?: 'غير محدد' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">الرقم الضريبي</label>
                        <div>{{ $supplier->tax_number ?: 'غير محدد' }}</div>
                    </div>

                    @if($supplier->notes)
                    <div class="mb-3">
                        <label class="text-muted d-block">ملاحظات</label>
                        <div>{{ $supplier->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">العنوان</h5>
                </div>
                <div class="card-body">
                    @if($supplier->address)
                        <div class="mb-2">{{ $supplier->address }}</div>
                    @endif

                    <div class="row g-2">
                        @if($supplier->city)
                        <div class="col-6">
                            <label class="text-muted d-block">المدينة</label>
                            <div>{{ $supplier->city }}</div>
                        </div>
                        @endif

                        @if($supplier->country)
                        <div class="col-6">
                            <label class="text-muted d-block">الدولة</label>
                            <div>{{ $supplier->country }}</div>
                        </div>
                        @endif

                        @if($supplier->postal_code)
                        <div class="col-6">
                            <label class="text-muted d-block">الرمز البريدي</label>
                            <div>{{ $supplier->postal_code }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">المنتجات</h5>
                        <div>
                            <span class="badge bg-primary me-2">
                                {{ $supplier->products_count }} منتج
                            </span>
                            <span class="badge bg-success">
                                {{ number_format($totalValue, 2) }} ريال
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($supplier->products->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>السعر</th>
                                        <th>المخزون</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-medium">{{ $product->name }}</div>
                                                    <small class="text-muted">{{ $product->sku }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->sale_price)
                                                <div class="text-decoration-line-through text-muted">
                                                    {{ number_format($product->price, 2) }}
                                                </div>
                                                <div class="text-danger">
                                                    {{ number_format($product->sale_price, 2) }}
                                                </div>
                                            @else
                                                {{ number_format($product->price, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted mb-3">لا توجد منتجات لهذا المورد</div>
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة منتج جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
}
</style>
@endpush
@endsection 