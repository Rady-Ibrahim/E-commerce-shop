<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'parent_id'
    ];

    // العلاقة مع الفئات الفرعية
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // العلاقة مع الفئة الأب
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // العلاقة مع المنتجات
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
} 