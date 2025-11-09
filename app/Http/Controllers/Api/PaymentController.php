<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.customer']);

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function store(PaymentRequest $request)
    {
        $payment = Payment::create($request->validated());
        return response()->json($payment->load(['invoice']), 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load(['invoice.customer']));
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        // update invoice status after edit
        try { $payment->invoice->updatePaymentStatus(); } catch (\Exception $e) {}
        return response()->json($payment->load(['invoice']));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }

    public function statistics()
    {
        return response()->json([
            'total_this_month' => Payment::whereMonth('payment_date', date('m'))->sum('amount'),
            'count_this_month' => Payment::whereMonth('payment_date', date('m'))->count(),
            'average_payment' => Payment::avg('amount'),
        ]);
    }

    /**
     * Return a suggested unique payment number for the current year.
     */
    public function suggestedNumber()
    {
        $year = date('Y');
        $existing = Payment::withTrashed()->whereYear('created_at', $year)
            ->pluck('payment_number')
            ->filter()
            ->map(function ($v) use ($year) {
                if (preg_match('/PAY-' . $year . '-(\d{3})$/', $v, $m)) return (int) $m[1];
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

        $number = 'PAY-' . $year . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        return response()->json(['payment_number' => $number]);
    }
}
