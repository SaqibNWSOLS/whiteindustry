<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query();
            
            if ($request->has('category') && $request->category) {
                if ($request->has('category')=='raw_material') {
                    $query->whereIn('category', ['blend',$request->category]);
                } else {
                    $query->where('category', $request->category);
                }
                
            }
            
            return DataTables::of($query)
                ->addColumn('actions', function($product) {
                    return view('products.partials.actions', compact('product'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        // Get statistics for the view
        $stats = $this->getStatistics();
        
        return view('products.index', compact('stats'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'product_code' => ['required', 'string', 'max:80', Rule::unique('products', 'product_code')],
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'category' => 'required|string|max:120',
            'product_type' => 'nullable|string|max:80',
            'unit_price' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string|max:30',
            'status' => ['nullable', Rule::in(['active','inactive','archived'])],
        ]);

        Product::create($data);

         return handleResponse($request, 'Product created successfully.', 'products.index');

    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_code' => ['required', 'string', 'max:80', Rule::unique('products', 'product_code')->ignore($product->id)],
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'category' => 'required|string|max:120',
            'product_type' => 'nullable|string|max:80',
            'unit_price' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string|max:30',
            'status' => ['nullable', Rule::in(['active','inactive','archived'])],
        ]);

        $product->update($data);
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => ['required', Rule::in(['active','inactive','archived'])]
        ]);
        
        $product->update(['status' => $request->status]);
        
        return redirect()->back()
            ->with('success', 'Product status updated successfully.');
    }

    public function export(Request $request)
    {
        $category = $request->get('category');
        $fileName = 'products-' . ($category ? $category : 'all') . '-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $products = Product::when($category, function($query, $category) {
            return $query->where('category', $category);
        })->get();

        $columns = ['Product Code', 'Name', 'Category', 'Unit Price', 'Unit of Measure', 'Status', 'Description'];

        $callback = function() use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->product_code,
                    $product->name,
                    $product->category,
                    $product->unit_price,
                    $product->unit_of_measure,
                    $product->status,
                    $product->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Helper method to get statistics
    private function getStatistics()
    {
        $cats = ['raw_material', 'packaging', 'final_product', 'blend'];
        $data = [];
        
        foreach ($cats as $c) {
            $data[$c] = [
                'total' => Product::where('category', $c)->count(),
                'active' => Product::where('category', $c)->where('status', 'active')->count(),
                'avg_price' => Product::where('category', $c)->avg('unit_price') ?? 0,
            ];
        }

        return $data;
    }
}