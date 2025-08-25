<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * نموذج عنصر السلة
 * 
 * هذا النموذج يمثل عنصراً في سلة التسوق
 * كل عنصر يربط بين سلة ومنتج معين
 * يحتوي على معلومات الكمية والسعر والمجموع الفرعي
 */
class CartItem extends Model
{
    /**
     * الحقول التي يمكن تعبئتها مباشرة
     * 
     * @var array
     */
    protected $fillable = [
        'cart_id',      // معرف السلة - للربط مع جدول السلة
        'product_id',   // معرف المنتج - للربط مع جدول المنتجات
        'quantity',     // كمية المنتج في السلة - لا يمكن أن تكون سالبة
        'price',        // سعر المنتج وقت إضافته للسلة - نحتفظ به لأن سعر المنتج قد يتغير
        'subtotal'      // المجموع الفرعي - حاصل ضرب السعر × الكمية
    ];

    /**
     * العلاقة مع السلة
     * كل عنصر ينتمي لسلة واحدة
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * العلاقة مع المنتج
     * كل عنصر يمثل منتج واحد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 