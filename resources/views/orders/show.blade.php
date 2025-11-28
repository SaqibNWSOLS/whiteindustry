@extends('layouts.app')

@section('title', 'Order Details')
@section('page_title', 'Order Details')

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
            <a id="invoice_download_btn" class="btn btn-download">
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
</div>
<div class="invoice-container">
     <style>
        :root {
            --primary: #143619;
            --primary-dark: #1a252f;
            --secondary: #7f8c8d;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --border: #e9ecef;
            --radius: 5px;
            --font-size-sm: 0.9rem;
            --font-size-base: 0.8rem;
            --font-size-md: 0.9rem;
            --font-size-lg: 1rem;
        }
        
        
        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: var(--radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .invoice-header {
            background: var(--primary);
            color: white;
            padding: 15px 25px;
            position: relative;
        }
        
        .company-name {
            font-size: var(--font-size-lg);
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: var(--font-size-sm);
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .invoice-title {
            text-align: center;
            margin: 0px 0;
        }
        
        .invoice-title h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }
        
        .invoice-meta {
            justify-content: space-between;
            margin-bottom: 0px;
            font-size: var(--font-size-sm);
        }
        
        .invoice-meta div {
            display: flex;
            flex-direction: column;
        }
        
        .invoice-meta span {
            font-weight: 600;
        }
        
        .client-info {
            background: var(--light);
            padding: 15px 25px;
            border-bottom: 1px solid var(--border);
        }
        
        .client-info h3 {
            margin: 0 0 10px 0;
            font-size: var(--font-size-md);
            color: var(--primary);
        }
        
        .invoice-body {
            padding: 0 25px;
        }
        
        .table-responsive {
            overflow-x: auto;
            margin: 20px 0;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: var(--font-size-sm);
        }
        
        .invoice-table th {
            background: var(--primary);
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid var(--primary-dark);
        }
        
        .invoice-table td {
            padding: 8px;
            border: 1px solid var(--border);
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .section-title {
            font-size: var(--font-size-md);
            font-weight: 600;
            color: var(--primary);
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border);
        }
        
        .totals-section {
            margin: 20px 0;
            padding: 15px;
            background: var(--light);
            border-radius: var(--radius);
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 5px;
            border-bottom: 1px solid var(--border);
        }
        
        .totals-table tr:last-child td {
            border-bottom: none;
        }
        
        .totals-table .total-row {
            font-weight: 600;
            font-size: var(--font-size-md);
        }
        
        .totals-table .grand-total {
            font-weight: 700;
            font-size: var(--font-size-lg);
            color: var(--primary);
        }
        
        .amount-in-words {
            font-style: italic;
            margin: 10px 0;
            font-size: var(--font-size-sm);
        }
        
        .terms-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: var(--font-size-sm);
        }
        
        .terms-section h4 {
            margin: 0 0 10px 0;
            font-size: var(--font-size-md);
            color: var(--primary);
        }
        
        .company-details {
            background: var(--light);
            padding: 15px 25px;
            border-top: 1px solid var(--border);
            font-size: var(--font-size-sm);
        }
        
        .company-details p {
            margin: 5px 0;
        }
        
        .invoice-footer {
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-sm);
        }
        
        .btn-print {
            background: var(--primary);
            color: white;
        }
        
        .btn-print:hover {
            background: var(--primary-dark);
        }
        
        .btn-download {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .btn-download:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: var(--font-size-sm);
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .invoice-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .invoice-footer {
                flex-direction: column;
                gap: 10px;
            }
            
            .invoice-footer .btn {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: 600;
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

        @if($order->status === 'confirmed' && !$order->production)
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
        @endif
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