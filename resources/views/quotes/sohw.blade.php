<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Quotation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/manage.css') }}">

    <link type="text/css" rel="stylesheet" href="assets/fonts/font-awesome/css/font-awesome.min.css">
    
    <!-- Bootstrap Icons for the new design -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon" >

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/invoice.css') }}">
    
    <style>
        /* Additional styles for the new sections */
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
        }
        
        .info-card h5 {
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        
        .info-card address {
            font-style: normal;
            line-height: 1.6;
        }
        
        .info-card i {
            width: 20px;
            text-align: center;
        }
        
        .cost-breakdown {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-top: 10px;
        }
        
        .cost-breakdown h5 {
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
        }
        
        .cost-breakdown table {
            margin-bottom: 0;
        }
        
        .cost-breakdown .table-borderless tr:not(:last-child) {
            border-bottom: 1px solid #e9ecef;
        }
        
        .cost-breakdown .border-top {
            border-top: 2px solid #dee2e6 !important;
        }
        
        .cost-breakdown .border-2 {
            border-width: 2px !important;
        }
        
        .cost-breakdown .total-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .cost-breakdown .fs-4 {
            font-size: 1.25rem !important;
        }
        
        @media (max-width: 768px) {
            .mb-4 {
                margin-bottom: 1.5rem !important;
            }
            
            .cost-breakdown {
                padding: 15px;
            }
            
            .cost-breakdown .col-lg-8 {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<!-- Invoice 1 start -->
<div class="invoice-1 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner clearfix">
                    <div class="invoice-info clearfix" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row g-0" style="display: flex;">
                                <div class="col-sm-6">
                                    <div class="invoice-logo  invoice-id">
                                        <!-- logo started -->
                                        <div class="logo">
                                            <img src="{{ asset('logo.png') }}" alt="logo">
                                        </div>
                                        <!-- logo ended -->
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info">
                                        <h1 class=" inv-header-1">Quotation</h1>
                                        <p class=" mb-1">Quotation Number <span>#{{ $quote->quote_number ?? $quote->id }}</span></p>
                                        <p class=" mb-0"> Date <span>{{ $quote->created_at->format('M d, Y') }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- New From and Bill To sections -->
                        <div class="invoice-top">
                            <div class="row mb-5">
                                <div class="col-md-6 mb-4">
                                    <div class="info-card">
                                        <h5 class="d-flex align-items-center mb-3">
                                            <i class="bi bi-building me-2"></i> From
                                        </h5>
                                        <address class="mb-0">
                                            <strong>{{ $company->name ?? config('app.name') }}</strong><br>
                                            {{ $company->address ?? '123 Business Street' }}<br>
                                            {{ $company->city ?? 'City' }}, {{ $company->state ?? 'State' }} {{ $company->zip_code ?? 'ZIP' }}<br>
                                            {{ $company->country ?? 'Country' }}<br>
                                            <i class="bi bi-telephone me-1"></i> {{ $company->phone ?? '+1 (555) 123-4567' }}<br>
                                            <i class="bi bi-envelope me-1"></i> {{ $company->email ?? 'info@company.com' }}<br>
                                            @if($company->website ?? false)
                                            <i class="bi bi-globe me-1"></i> {{ $company->website }}<br>
                                            @endif
                                            @if($company->tax_id ?? false)
                                            <i class="bi bi-card-text me-1"></i> Tax ID: {{ $company->tax_id }}
                                            @endif
                                        </address>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-card">
                                        <h5 class="d-flex align-items-center mb-3">
                                            <i class="bi bi-person me-2"></i> Bill To
                                        </h5>
                                        <address class="mb-0">
                                            <strong>{{ $quote->customer->company_name ?? $quote->customer->contact_person }}</strong><br>
                                            @if($quote->customer->contact_person && $quote->customer->company_name)
                                            Attn: {{ $quote->customer->contact_person }}<br>
                                            @endif
                                            {{ $quote->customer->address ?? '' }}<br>
                                            @if($quote->customer->city)
                                            {{ $quote->customer->city }}, 
                                            {{ $quote->customer->state ?? '' }} {{ $quote->customer->postal_code ?? '' }}<br>
                                            @endif
                                            {{ $quote->customer->country ?? '' }}<br>
                                            @if($quote->customer->phone)
                                            <i class="bi bi-telephone me-1"></i> {{ $quote->customer->phone }}<br>
                                            @endif
                                            @if($quote->customer->email)
                                            <i class="bi bi-envelope me-1"></i> {{ $quote->customer->email }}<br>
                                            @endif
                                            @if($quote->customer->tax_id)
                                            <i class="bi bi-card-text me-1"></i> Tax ID: {{ $quote->customer->tax_id }}
                                            @endif
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-center">
                            <div class="table-responsive">
                                <table class="table mb-0 table-striped invoice-table">
                                    <thead class="bg-active">
                                    <tr class="tr">
                                        <th>No.</th>
                                        <th class="pl0 text-start">Item Description</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if($quote->products->count() > 0)
                                        @foreach($quote->products as $key=>$product)
                                    <tr class="tr">
                                        <td>
                                            <div class="item-desc-1">
                                                <span>{{ ++$key }}</span>
                                            </div>
                                        </td>
                                        <td class="pl0">{{ $product->product_name }}</td>
                                        <td class="text-center">{{ $product->total_amount }}</td>
                                        <td class="text-center">1</td>
                                        <td class="text-end">{{ $product->total_amount }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Cost Breakdown Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    
                                    <div class="cost-breakdown rounded">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td class="ps-0">Raw Materials Cost:</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->total_raw_material_cost, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0">Packaging Cost:</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->total_packaging_cost, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0">Manufacturing Cost ({{ $quote->manufacturing_cost_percent ?? 30 }}%):</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->manufacturing_cost, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0">Risk Cost ({{ $quote->risk_cost_percent ?? 5 }}%):</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->risk_cost, 2) }}</td>
                                                        </tr>
                                                        <tr class="border-top">
                                                            <td class="ps-0"><strong>Subtotal:</strong></td>
                                                            <td class="text-end pe-0"><strong>€{{ number_format($quote->subtotal, 2) }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0">Profit Margin ({{ $quote->profit_margin_percent ?? 30 }}%):</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->total_profit, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0"><strong>Total without Tax:</strong></td>
                                                            <td class="text-end pe-0"><strong>€{{ number_format($quote->total_without_tax, 2) }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-0">Tax ({{ $quote->tax_rate ?? 19 }}%):</td>
                                                            <td class="text-end pe-0">€{{ number_format($quote->tax_amount, 2) }}</td>
                                                        </tr>
                                                        <tr class="border-top border-2">
                                                            <td class="ps-0"><strong class="fs-4">Total Amount:</strong></td>
                                                            <td class="text-end pe-0 total-amount">€{{ number_format($quote->total_amount, 2) }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                       
                        
                    </div>
                    <div class="invoice-btn-section clearfix d-print-none">
                        <a href="javascript:window.print()" class="btn btn-lg btn-print">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>
                        <a id="invoice_download_btn" class="btn btn-lg btn-download btn-theme">
                            <i class="fa fa-download"></i> Download Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 1 end -->

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/jspdf.min.js"></script>
<script src="assets/js/html2canvas.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>