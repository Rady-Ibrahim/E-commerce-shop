<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * كونترولر الفئات
 * 
 * هذا الكونترولر يدير عرض الفئات والمنتجات ضمن كل فئة
 * يتعامل مع عرض الفئات الرئيسية والفرعية وتصفية المنتجات
 */
class CategoryController extends Controller
{
    /**
     * عرض صفحة الفئات الرئيسية
     * 
     * تعرض قائمة بالفئات الرئيسية النشطة وفئاتها الفرعية
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // جلب الفئات الرئيسية النشطة مع فئاتها الفرعية
        $categories = Category::where('parent_id', null)
            ->where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * عرض صفحة فئة معينة
     * 
     * تعرض المنتجات ضمن فئة محددة مع إمكانية التصفية
     * 
     * @param string|null $slug معرف الفئة
     * @return \Illuminate\View\View
     */
    public function show($slug = null)
    {
        // إذا لم يتم تحديد فئة، نعرض صفحة الفئات الرئيسية
        if (!$slug) {
            return $this->index();
        }

        // البحث عن الفئة المطلوبة
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // بناء استعلام المنتجات
        $query = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->where('quantity', '>', 0);

        // تصفية حسب نطاق السعر
        if (request('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }

        // تصفية حسب التوفر في المخزون
        if (request('in_stock')) {
            $query->where('quantity', '>', 0);
        }

        // تصفية حسب المنتجات المخفضة
        if (request('on_sale')) {
            $query->whereNotNull('sale_price');
        }

        // جلب المنتجات مع التصفية
        $products = $query->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }
} 