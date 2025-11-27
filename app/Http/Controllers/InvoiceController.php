<?php

namespace App\Http\Controllers;


use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;

class InvoiceController extends Controller
{
    public function index()
{
    $invoices = Invoice::with(['customer', 'items'])->latest()->get();
    
    $stats = [
        'total' => Invoice::count(),
        'draft' => Invoice::where('status', 'draft')->count(),
        'sent' => Invoice::where('status', 'sent')->count(),
        'paid' => Invoice::where('status', 'paid')->count(),
        'overdue' => Invoice::where('status', 'overdue')->count(),
        'total_revenue' => Invoice::sum('total_amount'),
    ];

    return view('invoices.index', compact('invoices', 'stats'));
}

    public function create($productionId)
    {
        $production = Production::with(['order.products', 'order.quote.customer'])->findOrFail($productionId);
        return view('invoices.create', compact('production'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_id' => 'required|exists:productions,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string'
        ]);

        $production = Production::with(['order.items', 'order.quote.customer'])->findOrFail($request->production_id);
        $order = $production->order;

        $subtotal = $order->items()->sum('total_price');
        $taxRate = 19;
        $taxAmount = ($taxRate / 100) * $subtotal;
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . Str::random(8),
            'production_id' => $request->production_id,
            'customer_id' => $order->quote->customer_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'notes' => $request->notes
        ]);

        foreach ($order->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_item_id' => $item->id,
                'product_name' => $item->quoteProduct->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice created successfully');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['production.order.quote.customer', 'items'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function sendInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'sent']);

        return redirect()->back()->with('success', 'Invoice sent to customer');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Invoice marked as paid');
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['production.order.quote.customer', 'items'])->findOrFail($id);
        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}