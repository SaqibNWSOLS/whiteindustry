<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use Illuminate\Validation\Rule;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionOrder::with(['product', 'order']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 25)));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'order_id' => 'nullable|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'production_line' => 'nullable|string|max:80',
            'batch_number' => 'nullable|string|max:80',
            'status' => ['nullable', Rule::in(['pending','mixing','filling','packaging','qc','completed','cancelled'])],
            'notes' => 'nullable|string',
        ]);

        $po = ProductionOrder::create($data);
        return response()->json(['data' => $po], 201);
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load(['product','order']);
        return response()->json(['data' => $productionOrder]);
    }

    public function update(Request $request, ProductionOrder $productionOrder)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'order_id' => 'nullable|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'production_line' => 'nullable|string|max:80',
            'batch_number' => 'nullable|string|max:80',
            'status' => ['nullable', Rule::in(['pending','mixing','filling','packaging','qc','completed','cancelled'])],
            'notes' => 'nullable|string',
        ]);

        $productionOrder->update($data);
        return response()->json(['data' => $productionOrder]);
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $user = request()->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('administrator') || $user->hasRole('manager'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $productionOrder->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function statistics()
    {
        return response()->json([
            'active' => ProductionOrder::whereIn('status', ['pending', 'mixing', 'filling', 'packaging'])->count(),
            'pending_launch' => ProductionOrder::where('status', 'pending')->count(),
            'qc_review' => ProductionOrder::where('status', 'qc')->count(),
            'completed_today' => ProductionOrder::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
        ]);
    }
}
