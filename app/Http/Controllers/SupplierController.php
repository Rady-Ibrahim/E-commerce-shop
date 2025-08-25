<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    // عرض قائمة الموردين
    public function index()
    {
        $suppliers = Supplier::withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    // عرض صفحة إنشاء مورد جديد
    public function create()
    {
        return view('suppliers.create');
    }

    // حفظ مورد جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    // عرض تفاصيل مورد
    public function show(Supplier $supplier)
    {
        $supplier->load(['products' => function($query) {
            $query->where('is_active', true)
                  ->orderBy('name');
        }]);

        $productsCount = $supplier->products_count;
        $totalValue = $supplier->products->sum(function($product) {
            return $product->price * $product->quantity;
        });

        return view('suppliers.show', compact('supplier', 'productsCount', 'totalValue'));
    }

    // عرض صفحة تعديل مورد
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    // تحديث بيانات مورد
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    // حذف مورد
    public function destroy(Supplier $supplier)
    {
        // التحقق من وجود منتجات مرتبطة بالمورد
        if ($supplier->products()->exists()) {
            return back()->with('error', 'لا يمكن حذف المورد لوجود منتجات مرتبطة به');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    // البحث عن الموردين
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $suppliers = Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('contact_person', 'like', "%{$query}%")
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('suppliers.index', compact('suppliers', 'query'));
    }

    // عرض تقرير الموردين
    public function report()
    {
        $suppliers = Supplier::withCount('products')
            ->withSum('products', DB::raw('price * quantity'))
            ->orderBy('name')
            ->paginate(10);

        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $totalProducts = Supplier::withCount('products')->get()->sum('products_count');
        $totalValue = Supplier::withSum('products', DB::raw('price * quantity'))->get()->sum('products_sum_price_quantity');

        return view('suppliers.report', compact(
            'suppliers',
            'totalSuppliers',
            'activeSuppliers',
            'totalProducts',
            'totalValue'
        ));
    }
} 