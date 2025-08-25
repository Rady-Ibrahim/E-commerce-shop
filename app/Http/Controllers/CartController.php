<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * كونترولر السلة
 * 
 * هذا الكونترولر يدير عمليات سلة التسوق
 * يتعامل مع طلبات المستخدم لإدارة محتويات السلة
 * يدعم المستخدمين المسجلين والزوار
 */
class CartController extends Controller
{
    /**
     * الحصول على السلة الحالية
     * 
     * هذه الدالة تبحث عن السلة المناسبة للمستخدم الحالي
     * إذا كان المستخدم مسجلاً، تبحث عن سلته
     * إذا كان زائراً، تستخدم معرف الجلسة
     * 
     * @return Cart
     */
    private function getCurrentCart(): Cart
    {
        if (Auth::check()) {
            // للمستخدمين المسجلين: نبحث عن السلة المرتبطة بحسابهم
            $cart = Cart::where('user_id', Auth::id())->first();
            if (!$cart) {
                // إذا لم تكن لديهم سلة، ننشئ واحدة جديدة
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'total' => 0
                ]);
            }
        } else {
            // للزوار: نستخدم معرف الجلسة
            $sessionId = session()->getId();
            $cart = Cart::where('session_id', $sessionId)->first();
            if (!$cart) {
                // إذا لم تكن لديهم سلة، ننشئ واحدة جديدة
                $cart = Cart::create([
                    'session_id' => $sessionId,
                    'total' => 0
                ]);
            }
        }
        return $cart;
    }

    /**
     * عرض صفحة السلة
     * 
     * تعرض صفحة السلة مع محتوياتها
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = $this->getCurrentCart();
        return view('cart.index', compact('cart'));
    }

    /**
     * إضافة منتج للسلة
     * 
     * يضيف منتجاً للسلة بالكمية المطلوبة
     * يتحقق من توفر الكمية في المخزون
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            // البحث عن المنتج وإضافته للسلة
            $product = Product::findOrFail($request->product_id);
            
            // التحقق من حالة المنتج
            if (!$product->is_active) {
                return response()->json([
                    'message' => 'عذراً، هذا المنتج غير متوفر حالياً'
                ], 422);
            }

            // التحقق من المخزون
            if ($product->stock <= 0) {
                return response()->json([
                    'message' => 'عذراً، هذا المنتج غير متوفر في المخزون'
                ], 422);
            }

            if ($request->quantity > $product->stock) {
                return response()->json([
                    'message' => 'عذراً، الكمية المطلوبة غير متوفرة في المخزون. المتوفر: ' . $product->stock . ' قطعة'
                ], 422);
            }

            $cart = $this->getCurrentCart();
            
            // التحقق من وجود المنتج في السلة
            $existingItem = $cart->items()->where('product_id', $product->id)->first();
            
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;
                
                // التحقق من المخزون مرة أخرى مع الكمية الموجودة
                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'message' => 'عذراً، الكمية الإجمالية تتجاوز المخزون المتاح. يمكنك إضافة ' . 
                                   ($product->stock - $existingItem->quantity) . ' قطعة إضافية'
                    ], 422);
                }
                
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'price' => $product->price
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price
                ]);
            }

            // تحديث إجمالي السلة
            $cart->updateTotals();

            return response()->json([
                'message' => 'تم إضافة المنتج إلى السلة بنجاح',
                'cart_count' => $cart->items()->sum('quantity'),
                'cart_total' => number_format($cart->total, 2),
                'product' => [
                    'name' => $product->name,
                    'quantity' => $request->quantity,
                    'price' => number_format($product->price, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة المنتج إلى السلة'
            ], 500);
        }
    }

    /**
     * تحديث كمية منتج في السلة
     * 
     * يحدث كمية منتج موجود في السلة
     * يتحقق من توفر الكمية في المخزون
     * 
     * @param Request $request
     * @param CartItem $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CartItem $item)
    {
        // التحقق من صحة الكمية المدخلة
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $cart = $this->getCurrentCart();
            
            // التحقق من أن العنصر ينتمي للسلة الحالية
            if ($item->cart_id !== $cart->id) {
                throw new \Exception('غير مصرح بتعديل هذا العنصر');
            }

            // تحديث الكمية
            $cart->updateItemQuantity($item, $request->quantity);

            // إرجاع رسالة نجاح مع الإجمالي الجديد
            return response()->json([
                'message' => 'تم تحديث الكمية بنجاح',
                'cart_total' => $cart->total
            ]);
        } catch (\Exception $e) {
            // إرجاع رسالة الخطأ
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * حذف منتج من السلة
     * 
     * يحذف منتجاً من السلة
     * 
     * @param CartItem $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(CartItem $item)
    {
        try {
            $cart = $this->getCurrentCart();
            
            // التحقق من أن العنصر ينتمي للسلة الحالية
            if ($item->cart_id !== $cart->id) {
                throw new \Exception('غير مصرح بحذف هذا العنصر');
            }

            // حذف العنصر
            $cart->removeItem($item);

            // إرجاع رسالة نجاح مع الإجمالي وعدد العناصر الجديد
            return response()->json([
                'message' => 'تم حذف المنتج من السلة بنجاح',
                'cart_total' => $cart->total,
                'cart_count' => $cart->getItemsCount()
            ]);
        } catch (\Exception $e) {
            // إرجاع رسالة الخطأ
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * تفريغ السلة
     * 
     * يحذف جميع المنتجات من السلة
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        try {
            $cart = $this->getCurrentCart();
            $cart->clear();

            // إرجاع رسالة نجاح
            return response()->json([
                'message' => 'تم تفريغ السلة بنجاح'
            ]);
        } catch (\Exception $e) {
            // إرجاع رسالة الخطأ
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function getCount()
    {
        try {
            $cart = $this->getCurrentCart();
            $count = $cart->items()->sum('quantity');
            
            return response()->json([
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'count' => 0
            ]);
        }
    }
} 