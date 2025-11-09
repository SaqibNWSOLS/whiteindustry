<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'order', 'payments']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();
        
        // Calculate tax and total
        $data['tax_amount'] = ($data['subtotal'] * $data['tax_rate']) / 100;
        $data['total_amount'] = $data['subtotal'] + $data['tax_amount'];
        $data['balance'] = $data['total_amount'];
        
        $invoice = Invoice::create($data);
        return response()->json($invoice->load(['customer', 'order']), 201);
    }

    public function show($invoice)
    {
        // Accept either numeric ID or invoice_number (e.g. INV-2025-0002)
        $inv = Invoice::with(['customer', 'order', 'payments'])
            ->where('id', $invoice)
            ->orWhere('invoice_number', $invoice)
            ->firstOrFail();

        return response()->json($inv);
    }

    public function update(InvoiceRequest $request, $invoice)
    {
        $data = $request->validated();

        $inv = Invoice::where('id', $invoice)->orWhere('invoice_number', $invoice)->firstOrFail();

        if (isset($data['subtotal']) || isset($data['tax_rate'])) {
            $subtotal = $data['subtotal'] ?? $inv->subtotal;
            $taxRate = $data['tax_rate'] ?? $inv->tax_rate;

            $data['tax_amount'] = ($subtotal * $taxRate) / 100;
            $data['total_amount'] = $subtotal + $data['tax_amount'];
            $data['balance'] = $data['total_amount'] - $inv->paid_amount;
        }

        $inv->update($data);
        return response()->json($inv->load(['customer', 'order']));
    }

    public function destroy($invoice)
    {
        $inv = Invoice::where('id', $invoice)->orWhere('invoice_number', $invoice)->firstOrFail();
        $inv->delete();
        return response()->json(null, 204);
    }

    public function statistics()
    {
        return response()->json([
            'outstanding' => Invoice::whereIn('status', ['unpaid', 'partial'])->sum('balance'),
            'overdue' => Invoice::where('status', 'overdue')->sum('balance'),
            'overdue_count' => Invoice::where('status', 'overdue')->count(),
            'paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', date('m'))
                ->sum('total_amount'),
        ]);
    }
}
