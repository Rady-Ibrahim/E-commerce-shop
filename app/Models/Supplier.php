<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'contact_person',
        'tax_number',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // العلاقة مع المنتجات
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // الحصول على الموردين النشطين فقط
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // الحصول على عدد المنتجات للمورد
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
} 