<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->get();
        
        $featuredProducts = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->latest()
            ->take(8)
            ->get();
        
        return view('home', compact('categories', 'featuredProducts'));
    }
} 