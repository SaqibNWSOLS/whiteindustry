<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    
      public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query();
            
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
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
        
        return view('inventory.index', compact('stats'));
    }

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
