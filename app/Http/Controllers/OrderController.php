<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QaQuote;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
{
    $orders = Order::with(['quote.customer', 'items', 'production'])->latest()->get();
    
    $stats = [
        'total' => Order::count(),
        'pending' => Order::where('status', 'pending')->count(),
        'confirmed' => Order::where('status', 'confirmed')->count(),
        'production' => Order::where('status', 'production')->count(),
        'completed' => Order::where('status', 'completed')->count(),
        'cancelled' => Order::where('status', 'cancelled')->count(),
    ];

    return view('orders.index', compact('orders', 'stats'));
}
    public function create()
    {
        $qaApproved = QaQuote::where('status', 'approved')->with(['quote.customer', 'quote.products'])->get();
        return view('orders.create', compact('qaApproved'));
    }

    public function store(Request $request)
    {
/*        echo json_encode($request->all()); exit;
*/        $request->validate([
            'qa_quotes_id' => 'required|exists:qa_quotes,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after:order_date',
            'order_notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.quote_product_id' => 'required|exists:quote_products,id',
            'items.*.quantity' => 'required|numeric|min:1'
        ]);

/*        echo 1; exit;
*/
        $qa = QaQuote::findOrFail($request->qa_quotes_id);
/*echo json_encode($qa); exit;
*/        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'quote_id' => $qa->quote_id,
            'qa_quotes_id' => $qa->id,
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'total_amount' => 0,
            'order_notes' => $request->order_notes,
            'status' => 'pending'
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $quoteProduct = $qa->quote->products()->find($item['quote_product_id']);
            
            if ($quoteProduct) {
                $totalPrice = $quoteProduct->total_amount * $item['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'quote_product_id' => $quoteProduct->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $quoteProduct->total_amount,
                    'total_price' => $totalPrice
                ]);

                $totalAmount += $totalPrice;
            }
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Order created successfully');
    }

    public function show($id)
    {
        $order = Order::with(['quote.customer', 'items', 'production'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function confirm($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Order confirmed');
    }
}