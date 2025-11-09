<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $category = $request->get('category');
        $query = Product::query();
        if ($category) {
            $query->where('category', $category);
        }
        if ($q) {
            $query->where('name', 'like', "%{$q}%")->orWhere('product_code', 'like', "%{$q}%");
        }
        $products = $query->latest()->paginate(25);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'product_code' => ['required', 'string', 'max:80', Rule::unique('products', 'product_code')],
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:120',
            'product_type' => 'nullable|string|max:80',
            'unit_price' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string|max:30',
            'status' => ['nullable', Rule::in(['active','inactive','archived'])],
        ]);

        $product = Product::create($data);
        return response()->json(['data' => $product], 201);
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'product_code' => ['required', 'string', 'max:80', Rule::unique('products', 'product_code')->ignore($product->id)],
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:120',
            'product_type' => 'nullable|string|max:80',
            'unit_price' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string|max:30',
            'status' => ['nullable', Rule::in(['active','inactive','archived'])],
        ]);

        $product->update($data);
        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $user = request()->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Return statistics for products. If category provided, returns stats for that category; otherwise returns breakdown.
    public function statistics(Request $request)
    {
        $category = $request->get('category');

        if ($category) {
            $total = Product::where('category', $category)->count();
            $active = Product::where('category', $category)->where('status', 'active')->count();
            $avg = Product::where('category', $category)->avg('unit_price') ?? 0;
            $best = null;
            if ($category === 'final_product') {
                $bestP = Product::where('category', $category)->orderBy('unit_price', 'desc')->first();
                $best = $bestP ? $bestP->name : null;
            }

            return response()->json([
                'category' => $category,
                'total' => (int) $total,
                'active' => (int) $active,
                'avg_price' => (float) $avg,
                'best_seller' => $best,
            ]);
        }

        // breakdown for all categories
        $cats = ['raw_material', 'packaging', 'final_product', 'blend'];
        $data = [];
        foreach ($cats as $c) {
            $data[$c] = [
                'total' => Product::where('category', $c)->count(),
                'active' => Product::where('category', $c)->where('status', 'active')->count(),
                'avg_price' => Product::where('category', $c)->avg('unit_price') ?? 0,
            ];
        }

        return response()->json(['breakdown' => $data]);
    }
}
