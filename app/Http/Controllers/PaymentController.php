<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        
        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['invoice' => 'Cannot add payment to cancelled invoice']);
        }

        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,card,cash,cheque',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $amount = $request->amount;
        $pending = $invoice->total_amount - $invoice->paid_amount;

        if ($amount > $pending) {
            return back()->withErrors(['amount' => "Payment amount exceeds pending amount of \${$pending}"]);
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes
        ]);

        // Update invoice status if fully paid
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment recorded successfully');
    }

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        return view('payments.edit', compact('payment', 'invoice'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,card,cash,cheque',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $oldAmount = $payment->amount;
        $newAmount = $request->amount;
        $pending = $invoice->total_amount - ($invoice->paid_amount - $oldAmount);

        if ($newAmount > $pending) {
            return back()->withErrors(['amount' => "Payment amount exceeds pending amount of \${$pending}"]);
        }

        $payment->update([
            'amount' => $newAmount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment updated successfully');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        $payment->delete();

        // Update invoice status if no longer fully paid
        if ($invoice->status === 'paid' && $invoice->paid_amount < $invoice->total_amount) {
            $invoice->update(['status' => 'issued']);
        }

        return back()->with('success', 'Payment deleted successfully');
    }
}