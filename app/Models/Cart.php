<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * نموذج السلة
 * 
 * هذا النموذج يمثل سلة التسوق في النظام
 * يمكن أن تكون السلة مرتبطة بمستخدم مسجل أو بزائر (من خلال session_id)
 * يحتوي على العلاقات والدوال اللازمة لإدارة محتويات السلة
 */
class Cart extends Model
{
    /**
     * الحقول التي يمكن تعبئتها مباشرة
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',      // معرف المستخدم (اختياري) - للربط مع المستخدمين المسجلين
        'session_id',   // معرف الجلسة (اختياري) - للربط مع الزوار
        'total'         // إجمالي قيمة السلة - يتم تحديثه تلقائياً عند تغيير المحتويات
    ];

    /**
     * العلاقة مع المستخدم
     * كل سلة يمكن أن تنتمي لمستخدم واحد (إذا كان مسجلاً)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع عناصر السلة
     * كل سلة تحتوي على عدة عناصر (منتجات)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * حساب عدد العناصر في السلة
     * يجمع كميات جميع المنتجات في السلة
     * 
     * @return int
     */
    public function getItemsCount(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * تحديث إجمالي السلة
     * يحسب مجموع المجاميع الفرعية لجميع العناصر
     * 
     * @return void
     */
    public function updateTotal(): void
    {
        $this->total = $this->items()->sum('subtotal');
        $this->save();
    }

    /**
     * إضافة منتج للسلة
     * 
     * @param Product $product المنتج المراد إضافته
     * @param int $quantity الكمية المطلوبة (الافتراضي: 1)
     * @return CartItem
     * @throws \Exception إذا كانت الكمية غير متوفرة في المخزون
     */
    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        // التحقق من توفر الكمية في المخزون
        if ($product->stock < $quantity) {
            throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
        }

        // البحث عن المنتج في السلة
        $cartItem = $this->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // إذا كان المنتج موجود، نزيد الكمية
            $newQuantity = $cartItem->quantity + $quantity;
            
            // التحقق مرة أخرى من توفر الكمية الجديدة
            if ($product->stock < $newQuantity) {
                throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
            }

            // تحديث الكمية والمجموع الفرعي
            $cartItem->quantity = $newQuantity;
            $cartItem->subtotal = $cartItem->price * $newQuantity;
            $cartItem->save();
        } else {
            // إذا كان المنتج جديد، نضيفه للسلة
            $cartItem = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'subtotal' => $product->price * $quantity
            ]);
        }

        // تحديث إجمالي السلة
        $this->updateTotal();

        return $cartItem;
    }

    /**
     * تحديث كمية منتج في السلة
     * 
     * @param CartItem $item عنصر السلة المراد تحديثه
     * @param int $quantity الكمية الجديدة
     * @return CartItem
     * @throws \Exception إذا كانت الكمية غير متوفرة في المخزون
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        // التحقق من توفر الكمية في المخزون
        if ($item->product->stock < $quantity) {
            throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
        }

        // تحديث الكمية والمجموع الفرعي
        $item->quantity = $quantity;
        $item->subtotal = $item->price * $quantity;
        $item->save();

        // تحديث إجمالي السلة
        $this->updateTotal();

        return $item;
    }

    /**
     * حذف منتج من السلة
     * 
     * @param CartItem $item عنصر السلة المراد حذفه
     * @return void
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
        $this->updateTotal();
    }

    /**
     * تفريغ السلة
     * حذف جميع المنتجات من السلة
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->total = 0;
        $this->save();
    }
} 