<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // عرض صفحة المنتجات الرئيسية
    public function index()
    {
        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0);

        // تصفية حسب السعر
        if (request('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }

        // تصفية حسب التصنيف
        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        // تصفية حسب التوفر
        if (request('in_stock')) {
            $query->where('quantity', '>', 0);
        }

        // تصفية حسب العروض
        if (request('on_sale')) {
            $query->whereNotNull('sale_price');
        }

        // ترتيب المنتجات
        switch (request('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    // عرض تفاصيل منتج معين
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'images'])
            ->firstOrFail();

        // جلب المنتجات ذات الصلة (من نفس التصنيف)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    // البحث عن المنتجات
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where('quantity', '>', 0)
            ->paginate(12);

        return view('products.search', compact('products', 'query'));
    }
} 