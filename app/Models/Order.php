<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_postal_code',
        'shipping_phone',
        'notes'
    ];

    // العلاقة مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع تفاصيل الطلب
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // تحديث إجمالي الطلب
    public function updateTotal()
    {
        $this->total_amount = $this->items->sum('total');
        $this->save();
    }
} 