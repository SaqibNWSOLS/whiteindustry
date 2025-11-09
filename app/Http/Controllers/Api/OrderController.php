<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Eager load relations only if their tables exist (avoids runtime SQL errors when migrations are partial)
        $relations = ['customer', 'product'];
        if (Schema::hasTable('invoices')) $relations[] = 'invoice';

        $query = Order::with($relations);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $attempts = 0;
        while (true) {
            try {
                $order = Order::create($data);
                return response()->json($order->load(['customer', 'product']), 201);
            } catch (\Illuminate\Database\QueryException $e) {
                $attempts++;
                // 1062 = duplicate entry (unique constraint). If it's the order_number unique key, retry.
                if ($attempts >= 3 || strpos($e->getMessage(), '1062') === false) {
                    throw $e;
                }
                // regenerate a new order_number by instantiating an Order and letting model boot() run
                try {
                    $tmp = new Order();
                    // touch created_at so whereYear queries in model see current year
                    $tmp->created_at = now();
                    $tmp->save();
                    // delete the temporary record immediately
                    $tmp->delete();
                } catch (\Exception $ex) {
                    // if temporary save fails, break and rethrow the original exception
                    throw $e;
                }
                // loop and retry
            }
        }
    }

    public function show(Order $order)
    {
        $relations = ['customer', 'product', 'productionOrder'];
        if (Schema::hasTable('invoices')) $relations[] = 'invoice';

        return response()->json($order->load($relations));
    }

    public function update(OrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return response()->json($order->load(['customer', 'product']));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }

    public function statistics()
    {
        return response()->json([
            'total_ytd' => Order::whereYear('created_at', date('Y'))->count(),
            'total_value' => Order::whereYear('created_at', date('Y'))->sum('total_value'),
            'pending' => Order::where('status', 'pending')->count(),
            'in_production' => Order::where('status', 'in_production')->count(),
            'completed' => Order::where('status', 'completed')->count(),
        ]);
    }

    /**
     * Return a suggested unique order number for the current year.
     */
    public function suggestedNumber()
    {
        $year = date('Y');
        // find existing numbers for this year and compute next available
        $existing = Order::withTrashed()->whereYear('created_at', $year)
            ->pluck('order_number')
            ->filter()
            ->map(function ($v) use ($year) {
                // expect format ORD-YYYY-NNN
                if (preg_match('/ORD-' . $year . '-(\d{3})$/', $v, $m)) return (int) $m[1];
                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $next = 1;
        foreach ($existing as $n) {
            if ($n != $next) break;
            $next++;
        }

        $number = 'ORD-' . $year . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        return response()->json(['order_number' => $number]);
    }
}