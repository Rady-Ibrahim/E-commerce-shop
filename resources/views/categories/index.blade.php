@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">التصنيفات</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 mb-3">تصفح التصنيفات</h1>
        <p class="lead text-muted">اكتشف مجموعتنا الواسعة من المنتجات من خلال تصفح تصنيفاتنا</p>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4">
        @foreach($categories as $category)
        <div class="col-md-4">
            <div class="card category-card h-100">
                <div class="card-img-wrapper">
                    <img src="{{ $category->image }}" 
                         class="card-img-top" 
                         alt="{{ $category->name }}">
                    @if($category->children->count() > 0)
                        <div class="subcategories-badge">
                            {{ $category->children->count() }} تصنيف فرعي
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $category->name }}</h5>
                    @if($category->description)
                        <p class="card-text text-muted">{{ Str::limit($category->description, 100) }}</p>
                    @endif
                    
                    @if($category->children->count() > 0)
                        <div class="subcategories-list mt-3">
                            <h6 class="mb-2">التصنيفات الفرعية:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($category->children->take(3) as $subcategory)
                                    <a href="{{ route('categories.show', $subcategory->slug) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        {{ $subcategory->name }}
                                    </a>
                                @endforeach
                                @if($category->children->count() > 3)
                                    <span class="btn btn-sm btn-outline-secondary">
                                        +{{ $category->children->count() - 3 }} المزيد
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="btn btn-primary w-100">
                        عرض المنتجات
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Features Section -->
    <div class="row mt-5 pt-5">
        <div class="col-12 text-center mb-4">
            <h2 class="h3">لماذا تختار متجرنا؟</h2>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <i class="fas fa-truck fa-2x text-primary mb-3"></i>
                <h5>توصيل سريع</h5>
                <p class="text-muted">توصيل لجميع أنحاء المملكة</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <i class="fas fa-undo fa-2x text-primary mb-3"></i>
                <h5>إرجاع مجاني</h5>
                <p class="text-muted">خلال 14 يوم من الشراء</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <i class="fas fa-lock fa-2x text-primary mb-3"></i>
                <h5>دفع آمن</h5>
                <p class="text-muted">حماية كاملة للمعاملات</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <i class="fas fa-headset fa-2x text-primary mb-3"></i>
                <h5>دعم 24/7</h5>
                <p class="text-muted">خدمة عملاء على مدار الساعة</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.category-card {
    transition: transform 0.3s ease;
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.category-card:hover {
    transform: translateY(-5px);
}

.card-img-wrapper {
    position: relative;
    overflow: hidden;
    padding-top: 75%; /* 4:3 Aspect Ratio */
}

.card-img-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.subcategories-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.subcategories-list {
    font-size: 0.9rem;
}

.subcategories-list .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush
@endsection 