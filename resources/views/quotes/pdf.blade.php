<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - {{ $quote->quote_number ?? $quote->id }}</title>
    <style>
        {!! file_get_contents(public_path('css/invoice.css')) !!}
        
        /* Additional PDF-specific styles */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .invoice-container {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .btn-print, .btn-download, .invoice-actions {
            display: none;
        }
        th{
            color:black !important;
        }
        
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <table style="width:100%">
                <tr>
                    <td>
                        <div class="company-name">EURL BUSINESS CHALLENGE</div>
                        <div class="company-tagline">{{ __('quotes.invoice.dietary_supplements') }} • {{ __('quotes.invoice.cosmetic_products') }} • {{ __('quotes.invoice.hotel_amenities') }} • {{ __('quotes.invoice.para_pharmaceuticals') }}</div>
                    </td>
                    <td style="text-align: right">
                        @if(file_exists(public_path('logo.png')))
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" style="max-width: 80px; height: auto;">
@endif
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="invoice-title">
                            <h1>{{ __('quotes.invoice.quotation') }}</h1>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="invoice-meta" style="width:100%">
                <tr>
                    <td>
                        <div>
                            {{ __('quotes.invoice.quotation_number') }}: <span>{{ $quote->quote_number ?? $quote->id }}</span>
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ __('quotes.invoice.quotation_date') }}: <span>{{ $quote->created_at->format('M d, Y') }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="client-info">
            <h3>{{ __('quotes.invoice.client') }}: {{ $quote->customer->company_name ?? $quote->customer->contact_person }}</h3>
        </div>
        
        <div class="invoice-body">
            <div class="section-title">{{ __('quotes.invoice.products_list') }}</div>
            
            <div class="table-responsive">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>{{ __('quotes.invoice.no') }}</th>
                            <th>{{ __('quotes.invoice.product_name') }}</th>
                            <th>{{ __('quotes.invoice.unit_measure') }}</th>
                            <th>{{ __('quotes.invoice.product_quantity') }}</th>
                            <th>{{ __('quotes.invoice.unit_price') }}</th>
                            <th>{{ __('quotes.invoice.amount') }}</th>
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
                                    <td class="text-center">{{ $product->quantity??'' }}</td>
                                    <td class="text-center">{{ $product->price_unit }} DA</td>
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
                        <td>{{ __('quotes.invoice.total_excluding_tax') }}:</td>
                        <td class="text-right">{{ number_format($quote->total_amount-$quote->tax_amount, 2) }} DA</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="amount-in-words">
                            {{ __('quotes.invoice.amount_in_words') }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('quotes.invoice.vat') }} {{ $quote->tax_rate ?? 19 }}%:</td>
                        <td class="text-right">{{ number_format($quote->tax_amount, 2) }} DA</td>
                    </tr>
                    <tr class="grand-total">
                        <td>{{ __('quotes.invoice.total_including_tax') }}:</td>
                        <td class="text-right">{{ number_format($quote->total_amount, 2) }} DA</td>
                    </tr>
                </table>
            </div>
            
            <div class="terms-section">
                <h4>{{ __('quotes.invoice.terms_and_deadlines') }}</h4>
                <p>{{ __('quotes.invoice.completion_time') }}</p>
                <p>{{ __('quotes.invoice.payment_terms') }}</p>
                <p>{{ __('quotes.invoice.validity_period') }}</p>
            </div>
        </div>
        
        <div class="company-details">
            <p><strong>EURL BUSINESS CHALLENGE</strong></p>
            <p>{{ __('quotes.invoice.headquarters') }}</p>
            <p>{{ __('quotes.invoice.business_details') }}</p>
            <p>{{ __('quotes.invoice.contact_info') }}</p>
        </div>
        
        <div class="invoice-footer">
            <div>
                <p class="mb-0">{{ __('quotes.invoice.thank_you') }}</p>
            </div>
        </div>
    </div>
</body>
</html>