<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        :root {
            --primary: #143619;
            --primary-dark: #1a252f;
            --secondary: #7f8c8d;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --border: #e9ecef;
            --radius: 5px;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
        }
        
        .invoice-header {
            background: var(--primary);
            color: white;
            padding: 20px 30px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .company-info h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .invoice-title {
            text-align: center;
            margin: 20px 0;
        }
        
        .invoice-title h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 14px;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        
        .meta-label {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .client-info {
            background: var(--light);
            padding: 20px 30px;
            border-bottom: 1px solid var(--border);
        }
        
        .client-info h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: var(--primary);
        }
        
        .client-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }
        
        .invoice-body {
            padding: 0 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .invoice-table th {
            background: var(--primary);
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            border: 1px solid var(--primary-dark);
        }
        
        .invoice-table td {
            padding: 10px;
            border: 1px solid var(--border);
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .totals-section {
            margin: 25px 0;
            padding: 20px;
            background: var(--light);
            border-radius: var(--radius);
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .totals-table td {
            padding: 8px 5px;
            border-bottom: 1px solid var(--border);
        }
        
        .totals-table tr:last-child td {
            border-bottom: none;
        }
        
        .grand-total {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary);
        }
        
        .notes-section {
            margin: 25px 0;
            padding: 20px;
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            border-radius: var(--radius);
        }
        
        .notes-section h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: var(--primary);
        }
        
        .company-details {
            background: var(--light);
            padding: 20px 30px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            margin-top: 30px;
        }
        
        .company-details p {
            margin: 3px 0;
        }
        
        .invoice-footer {
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            margin-top: 30px;
            font-size: 14px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .status-sent {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
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
        
        /* Print styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="header-top">
                <div class="company-info">
                    <h1>EURL BUSINESS CHALLENGE</h1>
                    <p class="company-tagline">Dietary Supplements • Cosmetic Products • Hotel Amenities • Para-Pharmaceuticals</p>
                </div>
                <div style="text-align: right;">
                    <!-- Add logo if available -->
                    <!-- <img src="{{ asset('logo.png') }}" style="height: 60px;"> -->
                </div>
            </div>
            
            <div class="invoice-title">
                <h2>INVOICE</h2>
            </div>
            
            <div class="invoice-meta">
                <div class="meta-item">
                    <span class="meta-label">Invoice Number:</span>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Invoice Date:</span>
                    <span>{{ $invoice->invoice_date }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Due Date:</span>
                    <span>{{ $invoice->due_date }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status:</span>
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>
        
        <div class="client-info">
            <h3>BILL TO:</h3>
            <div class="client-details">
                <div>
                    <p class="text-bold">{{ $invoice->customer->company_name }}</p>
                    <p><strong>Contact:</strong> {{ $invoice->customer->contact_person }}</p>
                    <p><strong>Email:</strong> {{ $invoice->customer->email }}</p>
                </div>
                <div>
                    @if($invoice->customer->phone)
                        <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                    @endif
                    @if($invoice->customer->address)
                        <p><strong>Address:</strong> {{ $invoice->customer->address }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="invoice-body">
            <div class="section-title">Invoice Items</div>
            
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
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax (19%):</td>
                        <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL DUE:</td>
                        <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            
            @if($invoice->notes)
                <div class="notes-section">
                    <h4>Notes:</h4>
                    <p>{{ $invoice->notes }}</p>
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
                <p class="text-bold">Thank you for your business!</p>
            </div>
            <div>
                <p>Invoice generated on: {{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>