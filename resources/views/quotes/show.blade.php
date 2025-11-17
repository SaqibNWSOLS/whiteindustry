@extends('layouts.app')

@section('title', isset($quote) ? 'Show Quotation' : 'Show Quotation')
@section('page_title', isset($quote) ? 'Show Quotation' : 'Show Quotation')

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
            <h1>Quotation</h1>
        </div>
        </td>
            </tr>
        </table>
        <table class="invoice-meta" style="width:100%">
           <tr>
               <td><div>
                Quotation Number: <span>{{ $quote->quote_number ?? $quote->id }}</span>
            </div>
           </td>
               <td><div>
                Quotation Date: <span>{{ $quote->created_at->format('M d, Y') }}</span>
            </div></td>
           </tr>
        </table>
        
        
        <div >
             
        </div>
    </div>
    
    <div class="client-info">
        <h3>CLIENT: {{ $quote->customer->company_name ?? $quote->customer->contact_person }}</h3>
    </div>
    
    <div class="invoice-body">
        <div class="section-title">Products List</div>
        
        <div class="table-responsive">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product Name</th>
                        <th>Unit, Measure</th>
                        <th>Product Quantity</th>
                        <th>Unit Price (Excl. Tax)</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                     @if($quote->products->count() > 0)
                                        @foreach($quote->products as $key=>$product)
                    <tr>
                       <td>
                                            <div class="item-desc-1">
                                                <span>{{ ++$key }}</span>
                                            </div>
                                        </td>
                                                                                <td class="pl0">{{ $product->product_name }}</td>

                                        <td>{{ $product->packaging->volume??'' }} {{ $product->packaging->unit??'' }}</td>
                                                                                <td class="text-center">1</td>

                                        <td class="text-center">{{ $product->total_amount }} DA</td>
                                        <td class="text-end">{{ $product->total_amount }} DA</td>
                    </tr>
                    @endforeach
                                                        @endif

                </tbody>
            </table>
        </div>
        
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Total Excluding Tax:</td>
                    <td class="text-right">{{ number_format($quote->total_amount-$quote->tax_amount, 2) }} DA</td>
                </tr>
                <tr>
                    <td colspan="2" class="amount-in-words">
                        Forty-six million one hundred eighty-three thousand six hundred fifty dinars and ten centimes
                    </td>
                </tr>
                <tr>
                    <td>VAT {{ $quote->tax_rate ?? 19 }}%:</td>
                    <td class="text-right">{{ number_format($quote->tax_amount, 2) }} DA</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Including Tax:</td>
                    <td class="text-right">{{ number_format($quote->total_amount, 2) }} DA</td>
                </tr>
            </table>
        </div>
        
        <div class="terms-section">
            <h4>Terms and Deadlines</h4>
            <p>Product completion time is 120 days from order confirmation.</p>
            <p>Payment terms: 50% upon order, 50% upon delivery.</p>
            <p>This proforma invoice is valid for one week. Terms may be modified after this date.</p>
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