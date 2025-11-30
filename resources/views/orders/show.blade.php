@extends('layouts.app')

@section('title', 'Order Details')
@section('page_title', 'Order Details')

@section('content')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="    {{ asset('css/invoice.css') }}">

   
<div class="col-12" style="margin-bottom: 10px;">
    <div class="invoice-actions">
            <a href="javascript:window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i> Print
            </a>
            <a id="invoice_download_btn" class="btn btn-download">
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
</div>
<div class="invoice-container">
   
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
            <h1>Order Details</h1>
        </div>
        </td>
            </tr>
        </table>
        <table class="invoice-meta" style="width:100%">
           <tr>
               <td><div>
                Order Number: <span>{{ $order->order_number }}</span>
            </div>
           </td>
               <td><div>
                Order Date: <span>{{ $order->order_date }}</span>
            </div></td>
           </tr>
           <tr>
               <td><div>
                Quotation: <span>{{ isset($order->quote->quotation_number)?$order->quote->quotation_number:'' }}</span>
            </div>
           </td>
               <td><div>
                Status: <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div></td>
           </tr>
        </table>
    </div>
    
    <div class="client-info">
        <h3>CLIENT: {{ $order->customer->company_name ?? $order->customer->contact_person }}</h3>
    </div>
    
    <div class="invoice-body">
        <div class="section-title">Order Items</div>
        
        <div class="table-responsive">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->products as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ priceFormat($item->total_amount/$item->quantity , 2) }}</td>
                        <td class="text-right">{{ priceFormat($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="totals-section">
            <table class="totals-table">
                <tr class="grand-total">
                    <td>Total Amount:</td>
                    <td class="text-right">{{ priceFormat($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <div class="terms-section">
            <h4>Order Information</h4>
            <p><strong>Delivery Date:</strong> {{ $order->delivery_date }}</p>
            <p><strong>Created At:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
            <p><strong>Last Updated:</strong> {{ $order->updated_at->format('M d, Y H:i') }}</p>
        </div>
        
        @if($order->status === 'pending')
            <div class="action-buttons">
                <form action="{{ route('orders.confirm', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Confirm this order?')">
                        <i class="bi bi-check-circle"></i> Confirm Order
                    </button>
                </form>
            </div>
        @endif

       {{--  @if($order->status === 'confirmed' && !$order->production)
            <div class="action-buttons">
                <a href="{{ route('production.create', ['order_id' => $order->id]) }}" class="btn btn-primary">
                    <i class="bi bi-gear"></i> Create Production
                </a>
            </div>
        @elseif($order->production)
            <div class="action-buttons">
                <a href="{{ route('production.show', $order->production->id) }}" class="btn btn-secondary">
                    <i class="bi bi-eye"></i> View Production
                </a>
            </div>
        @endif --}}
    </div>
    
    <div class="company-details">
        <p><strong>EURL BUSINESS CHALLENGE</strong></p>
        <p>Main headquarters: Hai OS juillet group n° 17 lot n°13 rdc Bab Ezzouar – Algiers</p>
        <p>RC : 16/00–0050650 B 17 – NIF : 00171500505049 – NIS:001715100038165 – ART : 16296007124</p>
        <p>Phone : 020 199 828 – Email : businesschallengegroup@gmail.com</p>
    </div>
    
    <div class="invoice-footer">
        <div>
            <p class="mb-0">Thank you for your trust!</p>
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