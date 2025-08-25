<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'quantity',
        'sku',
        'is_active',
        'category_id'
    ];

    // العلاقة مع الفئة
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // العلاقة مع الصور
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // العلاقة مع سلة التسوق
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // العلاقة مع تفاصيل الطلبات
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // الحصول على الصورة الرئيسية
    public function getMainImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first()?->image_path 
            ?? $this->images()->first()?->image_path 
            ?? 'default-product.jpg';
    }
} 