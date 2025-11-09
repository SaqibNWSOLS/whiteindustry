<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Lead, Quote, Order, Payment, Invoice};
use App\Services\{DocumentService, EmailService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    protected $documentService;
    protected $emailService;

    public function __construct(DocumentService $documentService, EmailService $emailService)
    {
        $this->documentService = $documentService;
        $this->emailService = $emailService;
    }

    /**
     * Step 1: Create Quote from Lead
     */
    public function createQuoteFromLead(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after:today',
            'payment_terms' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'send_email' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Create quote
            $totalAmount = $validated['quantity'] * $validated['unit_price'];
            
            $quote = Quote::create([
                'customer_id' => null, // Lead not yet converted
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'unit_price' => $validated['unit_price'],
                'total_amount' => $totalAmount,
                'valid_until' => $validated['valid_until'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            // Link quote to lead (add lead_id to quotes table or use metadata)
            $quote->update(['notes' => ($validated['notes'] ?? '') . "\nLead: {$lead->lead_id}"]);

            // Generate PDF
            $pdfPath = $this->documentService->generateQuotePDF($quote, $lead);

            // Send email if requested
            if ($request->input('send_email', false)) {
                $this->emailService->sendQuotation($lead, $quote, $pdfPath);
            }

            activity()
                ->causedBy($request->user())
                ->performedOn($quote)
                ->log("Created quotation {$quote->quote_number} from lead {$lead->lead_id}");

            DB::commit();

            return response()->json([
                'quote' => $quote->load('product'),
                'pdf_url' => url('storage/' . $pdfPath),
                'email_sent' => $request->input('send_email', false),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create quote: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Step 2: Accept Quote and Create Order
     */
    public function acceptQuote(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date|after:today',
            'priority' => 'nullable|in:normal,high,urgent',
            'special_instructions' => 'nullable|string',
            'convert_lead' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Check if quote is valid
            if ($quote->status !== 'pending') {
                return response()->json(['message' => 'Quote is not in pending status'], 400);
            }

            if ($quote->valid_until < now()) {
                return response()->json(['message' => 'Quote has expired'], 400);
            }

            // Convert lead to customer if needed
            $customerId = $quote->customer_id;
            if (!$customerId && $request->input('convert_lead', false)) {
                // Find lead from quote notes or create conversion logic
                // For now, assume customer exists or create one
                $customerId = $this->convertLeadToCustomer($quote);
            }

            if (!$customerId) {
                return response()->json(['message' => 'Customer required to create order'], 400);
            }

            // Update quote status
            $quote->update(['status' => 'accepted']);

            // Create order from quote
            $order = Order::create([
                'customer_id' => $customerId,
                'product_id' => $quote->product_id,
                'quantity' => $quote->quantity,
                'unit' => $quote->unit,
                'total_value' => $quote->total_amount,
                'order_date' => now(),
                'delivery_date' => $validated['delivery_date'],
                'priority' => $validated['priority'] ?? 'normal',
                'status' => 'pending',
                'special_instructions' => $validated['special_instructions'],
            ]);

            // Generate order document
            $pdfPath = $this->documentService->generateOrderPDF($order);

            // Send email confirmation
            $this->emailService->sendOrderConfirmation($order, $pdfPath);

            activity()
                ->causedBy($request->user())
                ->performedOn($order)
                ->log("Created order {$order->order_number} from quote {$quote->quote_number}");

            DB::commit();

            return response()->json([
                'order' => $order->load(['customer', 'product']),
                'quote' => $quote,
                'pdf_url' => url('storage/' . $pdfPath),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Step 3: Record Payment for Order
     */
    public function recordPaymentForOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|in:bank_transfer,credit_card,check,cash,wire_transfer',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Check if invoice exists, if not create one
            $invoice = $order->invoice;
            
            if (!$invoice) {
                $invoice = $this->createInvoiceForOrder($order);
            }

            // Calculate remaining balance
            $remainingBalance = $invoice->balance;

            if ($validated['amount'] > $remainingBalance) {
                return response()->json([
                    'message' => 'Payment amount exceeds remaining balance',
                    'remaining_balance' => $remainingBalance
                ], 400);
            }

            // Record payment
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'transaction_reference' => $validated['transaction_reference'],
                'notes' => $validated['notes'],
            ]);

            // Invoice status is automatically updated by Payment model observer
            $invoice->refresh();

            // Check if payment is 100% complete
            if ($invoice->status === 'paid') {
                // Generate final invoice PDF
                $pdfPath = $this->documentService->generateInvoicePDF($invoice);
                
                // Send invoice by email
                $this->emailService->sendInvoice($invoice, $pdfPath);

                activity()
                    ->causedBy($request->user())
                    ->performedOn($invoice)
                    ->log("Order {$order->order_number} fully paid. Invoice {$invoice->invoice_number} generated and sent.");
            }

            activity()
                ->causedBy($request->user())
                ->performedOn($payment)
                ->log("Recorded payment {$payment->payment_number} for order {$order->order_number}");

            DB::commit();

            return response()->json([
                'payment' => $payment->load('invoice'),
                'invoice' => $invoice->load(['payments', 'customer']),
                'order' => $order->fresh(),
                'fully_paid' => $invoice->status === 'paid',
                'pdf_url' => $invoice->status === 'paid' ? url('storage/' . $pdfPath) : null,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper: Create invoice for order
     */
    protected function createInvoiceForOrder(Order $order)
    {
        $subtotal = $order->total_value;
        $taxRate = 19; // Default DZD tax
        $taxAmount = ($subtotal * $taxRate) / 100;
        $totalAmount = $subtotal + $taxAmount;

        return Invoice::create([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance' => $totalAmount,
            'payment_terms' => 'Net 30',
            'status' => 'unpaid',
        ]);
    }

    /**
     * Helper: Convert lead to customer
     */
    protected function convertLeadToCustomer(Quote $quote)
    {
        // Implementation depends on your lead structure
        // This is a placeholder
        return null;
    }

    /**
     * Get workflow status for an order
     */
    public function getOrderWorkflowStatus(Order $order)
    {
        $invoice = $order->invoice;
        $payments = $invoice ? $invoice->payments : collect();

        return response()->json([
            'order' => $order->load(['customer', 'product']),
            'invoice' => $invoice ? $invoice->load('customer') : null,
            'payments' => $payments,
            'workflow_status' => [
                'order_created' => true,
                'invoice_created' => (bool) $invoice,
                'payment_started' => $payments->count() > 0,
                'payment_complete' => $invoice && $invoice->status === 'paid',
                'payment_percentage' => $invoice ? round(($invoice->paid_amount / $invoice->total_amount) * 100, 2) : 0,
            ],
        ]);
    }
}

