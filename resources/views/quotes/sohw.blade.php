<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quote->quote_number ?? $quote->id }} - {{ config('app.name') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Modern Quotation Styles */
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s ease;
        }

        .quotation-theme {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quotation-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .cost-breakdown {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .product-card {
            border-left: 4px solid var(--primary);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow);
        }

        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 180px;
            font-weight: 900;
            transform: rotate(-45deg);
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
        }

        .action-btn {
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .info-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border-left: 4px solid var(--info);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-modern {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-modern thead {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .table-modern th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table-modern td {
            padding: 1rem;
            border-color: #f1f3f4;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        @media print {
            .no-print { display: none !important; }
            .quotation-card { box-shadow: none !important; }
            .action-btn { display: none !important; }
            body { background: white !important; }
            .watermark { opacity: 0.1 !important; }
        }

        .progress-step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .progress-step.active {
            background: var(--primary);
            color: white;
        }

        .progress-step.completed {
            background: var(--success);
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header with Actions -->
    <div class="container-fluid py-3 bg-white border-bottom no-print">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('crm') }}" class="text-decoration-none">CRM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}" class="text-decoration-none">Quotations</a></li>
                            <li class="breadcrumb-item active">Quotation #{{ $quote->quote_number ?? $quote->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <button onclick="window.print()" class="btn btn-outline-primary action-btn">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="bi bi-file-pdf"></i> PDF</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel"></i> Excel</a></li>
                            </ul>
                        </div>
                        <button onclick="sendEmail()" class="btn btn-outline-info action-btn">
                            <i class="bi bi-envelope"></i> Email
                        </button>
                        @if($quote->status === 'draft')
                        <button onclick="markAsSent()" class="btn btn-outline-warning action-btn">
                            <i class="bi bi-send"></i> Mark Sent
                        </button>
                        @endif
                        <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-primary action-btn">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Quotation Content -->
    <div class="container my-4">
        <!-- Status Progress -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="card quotation-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="mb-0">Quotation Progress</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    @php
                                        $steps = ['draft', 'sent', 'accepted', 'completed'];
                                        $currentIndex = array_search($quote->status, $steps);
                                    @endphp
                                    @foreach($steps as $index => $step)
                                        <div class="text-center">
                                            <div class="progress-step {{ $index <= $currentIndex ? 'completed' : '' }} {{ $index == $currentIndex ? 'active' : '' }} mx-auto mb-2">
                                                {{ $index + 1 }}
                                            </div>
                                            <small class="text-muted text-capitalize">{{ $step }}</small>
                                        </div>
                                        @if($index < count($steps) - 1)
                                            <div class="flex-fill">
                                                <div class="progress" style="height: 3px;">
                                                    <div class="progress-bar {{ $index < $currentIndex ? 'bg-success' : 'bg-light' }}" 
                                                         style="width: 100%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotation Document -->
        <div class="card quotation-card">
            <!-- Header -->
            <div class="card-header quotation-theme py-4 position-relative">
                <div class="watermark">QUOTATION</div>
                <div class="row align-items-center position-relative" style="z-index: 1;">
                    <div class="col-md-6">
                        @if($company->logo_path ?? false)
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}" class="img-fluid" style="max-height: 80px;">
                        @else
                            <h1 class="h2 mb-1">{{ $company->name ?? config('app.name') }}</h1>
                            <p class="mb-0 opacity-75">{{ $company->tagline ?? 'Professional Services' }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h2 class="h1 mb-2 fw-bold">QUOTATION</h2>
                        <div class="glass-effect d-inline-block px-3 py-2 rounded">
                            <p class="mb-1">Quote #: <strong>{{ $quote->quote_number ?? 'Q-' . str_pad($quote->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                            <p class="mb-1">Date: <strong>{{ $quote->created_at->format('M d, Y') }}</strong></p>
                            <p class="mb-0">Valid Until: <strong>{{ $quote->valid_until ? \Carbon\Carbon::parse($quote->valid_until)->format('M d, Y') : '30 days' }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body position-relative">
                <!-- Watermark Status -->
                @if($quote->status === 'draft')
                <div class="watermark text-danger" style="top: 40%; left: 20%;">
                    DRAFT
                </div>
                @elseif($quote->status === 'sent')
                <div class="watermark text-warning" style="top: 40%; left: 20%;">
                    SENT
                </div>
                @elseif($quote->status === 'accepted')
                <div class="watermark text-success" style="top: 40%; left: 20%;">
                    ACCEPTED
                </div>
                @endif

                <!-- Company & Customer Information -->
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

                <!-- Products Section -->
                @if($quote->products->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="d-flex align-items-center mb-4">
                            <i class="bi bi-box-seam me-2"></i> Products
                            <span class="badge bg-primary ms-2">{{ $quote->products->count() }}</span>
                        </h5>
                        <div class="row">
                            @foreach($quote->products as $product)
                            <div class="col-md-6 mb-3">
                                <div class="card product-card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $product->product_name }}</h6>
                                        <div class="row small text-muted">
                                            <div class="col-6">
                                                <strong>Type:</strong><br>
                                                <span class="badge bg-info text-capitalize">{{ $product->product_type }}</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>Volume:</strong><br>
                                                {{ $product->final_product_volume }} {{ $product->volume_unit }}
                                            </div>
                                        </div>
                                        @if($product->rawMaterialItems->count() > 0)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Materials:</strong> {{ $product->rawMaterialItems->count() }} items
                                            </small>
                                        </div>
                                        @endif
                                        @if($product->packagingItems->count() > 0)
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <strong>Packaging:</strong> {{ $product->packagingItems->count() }} items
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Raw Materials Section -->
                @if($quote->products->sum(fn($product) => $product->rawMaterialItems->count()) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="bi bi-droplet me-2"></i> Raw Materials
                            <span class="badge bg-primary ms-2">{{ $quote->products->sum(fn($product) => $product->rawMaterialItems->count()) }}</span>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Material Name</th>
                                        <th class="text-center">Percentage</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quote->products as $product)
                                        @foreach($product->rawMaterialItems as $item)
                                        <tr>
                                            <td><small class="text-muted">{{ $product->product_name }}</small></td>
                                            <td>{{ $item->item->name ?? $item->item_name }}</td>
                                            <td class="text-center">{{ number_format($item->percentage, 2) }}%</td>
                                            <td class="text-end">€{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">€{{ number_format($item->total_cost, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><strong>Total Raw Materials Cost:</strong></td>
                                        <td class="text-end"><strong>€{{ number_format($quote->total_raw_material_cost, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Packaging Section -->
                @if($quote->products->sum(fn($product) => $product->packagingItems->count()) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="bi bi-box me-2"></i> Packaging
                            <span class="badge bg-primary ms-2">{{ $quote->products->sum(fn($product) => $product->packagingItems->count()) }}</span>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Packaging Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quote->products as $product)
                                        @foreach($product->packagingItems as $item)
                                        <tr>
                                            <td><small class="text-muted">{{ $product->product_name }}</small></td>
                                            <td>{{ $item->item->name ?? $item->item_name }}</td>
                                            <td class="text-center">{{ number_format($item->quantity) }}</td>
                                            <td class="text-end">€{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">€{{ number_format($item->total_cost, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><strong>Total Packaging Cost:</strong></td>
                                        <td class="text-end"><strong>€{{ number_format($quote->total_packaging_cost, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Cost Breakdown -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="d-flex align-items-center mb-4">
                            <i class="bi bi-calculator me-2"></i> Cost Breakdown
                        </h5>
                        <div class="cost-breakdown rounded">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
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

                <!-- Terms & Conditions -->
                @if($quote->notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="bi bi-journal-text me-2"></i> Notes & Terms
                        </h5>
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="small">
                                    {!! nl2br(e($quote->notes)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="card-footer bg-transparent border-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i> Generated on {{ now()->format('M d, Y \a\t h:i A') }}<br>
                            <i class="bi bi-hash me-1"></i> Quote ID: {{ $quote->id }}
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted">
                            {{ $company->name ?? config('app.name') }}<br>
                            {{ $company->address ?? '' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function downloadPDF() {
            // Implement PDF download functionality
            alert('PDF download functionality would be implemented here');
            // You can use libraries like jsPDF or make an API call to generate PDF
        }

        function sendEmail() {
            // Implement email sending functionality
            alert('Email sending functionality would be implemented here');
        }

        function markAsSent() {
            if(confirm('Are you sure you want to mark this quote as sent?')) {
                fetch(`/quotes/{{ $quote->id }}/mark-sent`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('Quote marked as sent successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error marking quote as sent');
                    console.error('Error:', error);
                });
            }
        }

        // Add keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });

        // Add smooth scrolling for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>