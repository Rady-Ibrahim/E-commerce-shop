@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">الموردين</h1>
            <p class="text-muted mt-2">إدارة موردي المتجر</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.report') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i>
                تقرير الموردين
            </a>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة مورد جديد
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('suppliers.search') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" 
                           placeholder="ابحث عن مورد بالاسم، البريد الإلكتروني، رقم الهاتف أو اسم الشخص المسؤول..." 
                           value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهاتف</th>
                            <th>الشخص المسؤول</th>
                            <th>عدد المنتجات</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $supplier->products_count }} منتج
                                </span>
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('suppliers.show', $supplier) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                @if(request('q'))
                                    <div class="text-muted">
                                        لا توجد نتائج للبحث "{{ request('q') }}"
                                    </div>
                                @else
                                    <div class="text-muted">
                                        لا يوجد موردين حالياً
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suppliers->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-group {
    gap: 0.25rem;
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