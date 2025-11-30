@extends('layouts.app')

@section('title', 'Invoice Details')
@section('page_title', 'Invoice Details')

@section('content')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

   
<div class="col-12" style="margin-bottom: 10px;">
    <div class="invoice-actions">
            <a href="javascript:window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i> Print
            </a>
            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-download" download>
                <i class="bi bi-download"></i> Download PDF
            </a>
            @if($invoice->status === 'draft')
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('invoices.issue', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-check"></i> Issue Invoice
                    </button>
                </form>
            @endif
        </div>
</div>
<div class="invoice-container">
<style>
{!! file_get_contents(public_path('css/invoice.css')) !!}

/* Additional print styles */
@media print {
    body {
        margin: 0 !important;
        padding: 20px !important;
    }
    .invoice-actions, .action-buttons {
        display: none !important;
    }
    .invoice-container {
        box-shadow: none !important;
        border: none !important;
        margin: 0 auto !important;
    }
}
</style>
    <div class="invoice-header">
        <table style="width:100%">
            <tr>
                <td><div class="company-name">EURL BUSINESS CHALLENGE</div>
        <div class="company-tagline">Dietary Supplements • Cosmetic Products • Hotel Amenities • Para-Pharmaceuticals</div></td>
        <td style="text-align: right"><img src="{{ asset('logo.png') }}"></td>
            </tr>
            
        </table>
        <table style="width: 100%;">
            <tr>
                <td><div class="invoice-title">
            <h1>Invoice</h1>
        </div>
        </td>
            </tr>
        </table>
        <table class="invoice-meta" style="width:100%">
           <tr>
               <td><div>
                Invoice Number: <span>{{ $invoice->invoice_number }}</span>
            </div>
           </td>
               <td><div>
                Invoice Date: <span>{{ $invoice->invoice_date }}</span>
            </div></td>
           </tr>
           <tr>
               <td><div>
                Due Date: <span>{{ $invoice->due_date }}</span>
            </div>
           </td>
               <td><div>
                Status: <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div></td>
           </tr>
        </table>
    </div>
    
    <div class="client-info">
        <h3>CLIENT: {{ $invoice->customer->company_name }}</h3>
        <p><strong>Contact:</strong> {{ $invoice->customer->contact_person }}</p>
        <p><strong>Email:</strong> {{ $invoice->customer->email }}</p>
        @if($invoice->customer->phone)
            <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
        @endif
        @if($invoice->customer->address)
            <p><strong>Address:</strong> {{ $invoice->customer->address }}</p>
        @endif
    </div>
    
    <div class="invoice-body">
        <div class="section-title">Invoice Items</div>
        
        <div class="table-responsive">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->productItem->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ priceFormat($item->unit_price) }}</td>
                        <td class="text-right">{{ priceFormat($item->unit_price*$item->quantity) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ priceFormat($invoice->subtotal) }}</td>
                </tr>
                <tr>
                    <td>Tax (19%):</td>
                    <td class="text-right">{{ priceFormat($invoice->tax_amount) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Amount:</td>
                    <td class="text-right">{{ priceFormat($invoice->total_amount) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Status Section - Added from second view -->
        <div class="payment-status-section">
            <h4 class="section-title">Payment Status</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="text-muted">Total Amount</p>
                        <p class="stat-value">{{ priceFormat($invoice->total_amount, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="text-muted">Paid Amount</p>
                        <p class="stat-value text-success">{{ priceFormat($invoice->paid_amount, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="text-muted">Pending Amount</p>
                        <p class="stat-value text-danger">{{ priceFormat($invoice->pending_amount, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="text-muted">Progress</p>
                        <p class="stat-value">{{ $invoice->payment_progress }}%</p>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-3">
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $invoice->payment_progress }}%;" 
                         aria-valuenow="{{ $invoice->paid_amount }}" 
                         aria-valuemin="0" 
                         aria-valuemax="{{ $invoice->total_amount }}">
                        {{ $invoice->payment_progress }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History - Added from second view -->
        <div class="payment-status-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="section-title">Payment History</h4>
                @if($invoice->status !== 'cancelled' && $invoice->pending_amount > 0)
                    <a href="{{ route('payments.create', $invoice->id) }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Payment
                    </a>
                @endif
            </div>

            @if($invoice->payments && $invoice->payments->count() > 0)
                <div class="table-responsive">
                    <table class="invoice-table table-hover">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Transaction ID</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date }}</td>
                                    <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($payment->notes, 30) ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this payment?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No payments recorded yet.</p>
            @endif
        </div>
        
        @if($invoice->notes)
            <div class="notes-section">
                <h4>Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif
        
        <!-- Enhanced Action Buttons - Combined from both views -->
        <div class="action-buttons">
            {{-- @if($invoice->status === 'draft')
                <form action="{{ route('invoices.send', $invoice->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Send invoice to customer?')">
                        <i class="bi bi-send"></i> Send Invoice
                    </button>
                </form>
                
                <form action="{{ route('invoices.issue', $invoice->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-check"></i> Issue Invoice
                    </button>
                </form>
            @endif
            
            @if($invoice->status !== 'paid' && $invoice->pending_amount > 0)
                <form action="{{ route('invoices.paid', $invoice->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark as paid?')">
                        <i class="bi bi-check-circle"></i> Mark as Paid
                    </button>
                </form>
            @endif
            
            @if($invoice->status === 'issued' && $invoice->pending_amount == 0)
                <form action="{{ route('invoices.markAsPaid', $invoice->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </form>
            @endif
             --}}
            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-secondary" >
                <i class="bi bi-download"></i> Download PDF
            </a>
            
            {{-- @if($invoice->status === 'sent' || $invoice->status === 'overdue')
                <form action="{{ route('invoices.remind', $invoice->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Send payment reminder?')">
                        <i class="bi bi-bell"></i> Send Reminder
                    </button>
                </form>
            @endif
            
            @if($invoice->status !== 'cancelled')
                <form action="{{ route('invoices.cancel', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this invoice?')">
                        <i class="bi bi-x-circle"></i> Cancel Invoice
                    </button>
                </form>
            @endif --}}
            
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
            </a>
        </div>
    </div>
    
    <div class="company-details">
        <p><strong>EURL BUSINESS CHALLENGE</strong></p>
        <p>Main headquarters: Hai OS juillet group n° 17 lot n°13 rdc Bab Ezzouar – Algiers</p>
        <p>RC : 16/00–0050650 B 17 – NIF : 00171500505049 – NIS:001715100038165 – ART : 16296007124</p>
        <p>Phone : 020 199 828 – Email : businesschallengegroup@gmail.com</p>
    </div>
    
    <div class="invoice-footer">
        <div>
            <p class="mb-0">Thank you for your business!</p>
        </div>
        <div>
            <p class="mb-0">Invoice generated on: {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</div>
<script>
document.querySelector('.btn-print').addEventListener('click', function() {
    // Select the invoice content
    const invoiceContent = document.querySelector('.invoice-container').innerHTML;

    // Save the current body content
    const originalContent = document.body.innerHTML;

    // Replace body with invoice content
    document.body.innerHTML = invoiceContent;

    // Trigger print
    window.print();

    // Restore original body content
    document.body.innerHTML = originalContent;

    // Optional: reload JS if needed (for event listeners)
    location.reload();
});
</script>

@endsection