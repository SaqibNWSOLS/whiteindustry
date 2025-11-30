<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Production;
use App\Models\Order;
use App\Models\ProductionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;
use DB;
use App\Models\Product;
use App\Models\InventoryTransaction;

class InvoiceController extends Controller
{

    public function getProductionDetails(Production $production)
{
    // Eager load necessary relationships
    $production->load([
        'order.customer',
        'items.orderProduct'
    ]);

    return response()->json($production);
}

    public function index()
    {
        $invoices = Invoice::with(['production.order', 'production.order.quote.customer'])
            ->latest()
            ->paginate(20);
        
        $stats = [
            'total' => Invoice::count(),
            'draft' => Invoice::where('status', 'draft')->count(),
            'issued' => Invoice::where('status', 'issued')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'cancelled' => Invoice::where('status', 'cancelled')->count(),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create(Request $request)
    {
        $productions = Production::whereIn('status', ['completed','in_progress'])
            ->with(['order.quote.customer', 'items.orderProduct'])
            ->get();

        $selectedProduction = $request->production_id;
        if ($selectedProduction) {
            $selectedProduction = Production::with(['order.quote.customer', 'items.orderProduct'])
                ->findOrFail($selectedProduction);
        }

        return view('invoices.create', compact('productions', 'selectedProduction'));
    }

 public function store(Request $request)
{
    $request->validate([
        'production_id' => 'required|exists:productions,id',
        'invoice_date' => 'required|date',
        'due_date' => 'required|date|after:invoice_date',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'notes' => 'nullable|string',
        'invoice_items' => 'required|array',
        'invoice_items.*.production_item_id' => 'required|exists:production_items,id',
        'invoice_items.*.quantity' => 'required|integer|min:1',
        'invoice_items.*.unit_price' => 'required|numeric|min:0',
    ]);

    $production = Production::findOrFail($request->production_id);
    $order = $production->order;

    // Calculate totals
    $subtotal = 0;
    $invoiceItemsData = [];

    foreach ($request->invoice_items as $item) {
        if ($item['quantity'] <= 0 || $item['unit_price'] < 0) {
            continue;
        }

        $amount = $item['quantity'] * $item['unit_price'];
        $subtotal += $amount;

        $invoiceItemsData[] = [
            'production_item_id' => $item['production_item_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'amount' => $amount
        ];
    }

    if (empty($invoiceItemsData)) {
        return back()->withErrors(['invoice_items' => 'At least one valid invoice item is required']);
    }

    $taxAmount = ($subtotal * $request->tax_percentage) / 100;
    $totalAmount = $subtotal + $taxAmount;

    DB::beginTransaction();

    try {
        // Create invoice
        $invoice = Invoice::create([
            'customer_id' => $production->order->customer_id,
            'invoice_number' => 'INV-' . Str::random(10),
            'production_id' => $production->id,
            'order_id' => $order->id,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes
        ]);

        // Create invoice items and inventory transactions
        foreach ($invoiceItemsData as $itemData) {
            $productionItem = ProductionItem::
                find($itemData['production_item_id']);

            $description = $productionItem->orderProduct->product_name . 
                          ' (' . $productionItem->orderProduct->product_type . ')';

            $invoiceItem = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'products_id' => $productionItem->products_id,
                'production_item_id' => $itemData['production_item_id'],
                'item_description' => $description,
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'amount' => $itemData['amount']
            ]);

            // Check if there's enough stock for this product
            $product = Product::find($productionItem->products_id);
            if ($product && $product->current_stock < $itemData['quantity']) {
                throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->current_stock}, Required: {$itemData['quantity']}");
            }

            // Create inventory transaction for sales
            if ($product) {
                // Reduce stock for sold items
                $product->decrement('current_stock', $itemData['quantity']);

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'invoice_item_id' => $invoiceItem->id,
                    'production_item_id'=> $itemData['production_item_id'],
                    'transaction_type' => 'invoice',
                    'quantity_change' => -$itemData['quantity'], // Negative for sales
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'status' => 'completed',
                    'notes' => 'Stock reduced for invoice ' . $invoice->invoice_number,
                    'created_by' => auth()->id(),
                    'transaction_date' => now()
                ]);

            }
        }

        notify()
    ->title(__('notifications.titles.new_invoice'))
    ->message(__('notifications.invoice.created', ['number' => $invoice->invoice_number]))
    ->sendToRole(['Administrator','Manager','Accountant']);

        DB::commit();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully and inventory updated');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()]);
    }
}

/**
 * Update inventory balance for a product
 */

    public function show($id)
    {
        $invoice = Invoice::with(['production.order.quote.customer', 'items'])
            ->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['production', 'items'])
            ->findOrFail($id);

        if ($invoice->status !== 'draft') {
            return back()->withErrors(['invoice' => 'Only draft invoices can be edited']);
        }

        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'draft') {
            return back()->withErrors(['invoice' => 'Only draft invoices can be edited']);
        }

        $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'invoice_items' => 'required|array',
            'invoice_items.*.production_item_id' => 'required|exists:production_items,id',
            'invoice_items.*.quantity' => 'required|integer|min:1',
            'invoice_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Recalculate totals
        $subtotal = 0;
        $invoiceItemsData = [];

        foreach ($request->invoice_items as $item) {
            if ($item['quantity'] <= 0 || $item['unit_price'] < 0) {
                continue;
            }

            $amount = $item['quantity'] * $item['unit_price'];
            $subtotal += $amount;

            $invoiceItemsData[] = [
                'production_item_id' => $item['production_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $amount
            ];
        }

        $taxAmount = ($subtotal * $request->tax_percentage) / 100;
        $totalAmount = $subtotal + $taxAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes
        ]);

        // Delete old items and create new ones
        $invoice->items()->delete();

        foreach ($invoiceItemsData as $itemData) {
            $productionItem = $invoice->production
                ->items()
                ->find($itemData['production_item_id']);

            $description = $productionItem->orderProduct->product_name . 
                          ' (' . $productionItem->orderProduct->product_type . ')';

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'production_item_id' => $itemData['production_item_id'],
                'item_description' => $description,
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'amount' => $itemData['amount']
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully');
    }

    public function issue($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'draft') {
            return back()->withErrors(['invoice' => 'Only draft invoices can be issued']);
        }

        $invoice->markAsIssued();

        notify()
    ->title(__('notifications.titles.invoice_issued'))
    ->message(__('notifications.invoice.issued', ['number' => $invoice->invoice_number]))
    ->sendToRole(['Administrator','Manager','Accountant']);

        return redirect()->back()->with('success', 'Invoice issued successfully');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['invoice' => 'Cancelled invoices cannot be marked as paid']);
        }

        $invoice->markAsPaid();

        notify()
    ->title(__('notifications.titles.invoice_paid'))
    ->message(__('notifications.invoice.paid', ['number' => $invoice->invoice_number]))
    ->sendToRole(['Administrator','Manager','Accountant']);

        return redirect()->back()->with('success', 'Invoice marked as paid');
    }

    public function cancel($id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update(['status' => 'cancelled']);

        notify()
    ->title(__('notifications.titles.invoice_cancelled'))
    ->message(__('notifications.invoice.cancelled', ['number' => $invoice->invoice_number]))
    ->sendToRole(['Administrator','Manager','Accountant']);

        return redirect()->back()->with('success', 'Invoice cancelled');
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'draft') {
            return back()->withErrors(['invoice' => 'Only draft invoices can be deleted']);
        }

        $invoice->forceDelete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }

    public function pdf($id)
    {
        $invoice = Invoice::with(['production.order.quote.customer', 'order.quote.customer', 'items'])
            ->findOrFail($id);

        return view('invoices.pdf', compact('invoice'));
    }
}