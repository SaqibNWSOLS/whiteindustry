<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('orders_show.invoice.order_details') }} - {{ $order->order_number }}</title>
    <style>
        {!! file_get_contents(public_path('css/invoice.css')) !!}
        
        /* PDF-specific styles */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        
        .invoice-container {
            box-shadow: none;
            border: none;
            max-width: 100%;
        }
        
        .btn-print, .btn-download, .invoice-actions, .action-buttons {
            display: none !important;
        }
        
        .company-name {
            font-size: 16px;
        }
        
        .invoice-title h1 {
            font-size: 18px;
        }
        
        .invoice-table {
            font-size: 10px;
        }
        
        .invoice-table th,
        .invoice-table td {
            padding: 6px 4px;
        }
        
        @page {
            margin: 15px;
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
                        <div class="company-tagline">
                            {{ __('orders_show.invoice.dietary_supplements') }} • 
                            {{ __('orders_show.invoice.cosmetic_products') }} • 
                            {{ __('orders_show.invoice.hotel_amenities') }} • 
                            {{ __('orders_show.invoice.para_pharmaceuticals') }}
                        </div>
                    </td>
                    <td style="text-align: right">
                        @if(file_exists(public_path('logo.png')))
                            <img src="{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" style="max-width: 80px; height: auto;">
                        @endif
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="invoice-title">
                            <h1>{{ __('orders_show.invoice.order_details') }}</h1>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="invoice-meta" style="width:100%">
                <tr>
                    <td>
                        <div>
                            {{ __('orders_show.invoice.order_number') }}: <span>{{ $order->order_number }}</span>
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ __('orders_show.invoice.order_date') }}: <span>{{ $order->order_date }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            {{ __('orders_show.invoice.quotation') }}: <span>{{ isset($order->quote->quotation_number)?$order->quote->quotation_number:'' }}</span>
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ __('orders_show.invoice.status') }}: <span class="status-badge status-{{ $order->status }}">{{ __('orders_show.status.' . $order->status) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="client-info">
            <h3>{{ __('orders_show.invoice.client') }}: {{ $order->customer->company_name ?? $order->customer->contact_person }}</h3>
        </div>
        
        <div class="invoice-body">
            <div class="section-title">{{ __('orders_show.invoice.order_items') }}</div>
            
            <div class="table-responsive">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>{{ __('orders_show.invoice.no') }}</th>
                            <th>{{ __('orders_show.invoice.product_name') }}</th>
                            <th>{{ __('orders_show.invoice.quantity') }}</th>
                            <th>{{ __('orders_show.invoice.unit_price') }}</th>
                            <th>{{ __('orders_show.invoice.total_price') }}</th>
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
                        <td>{{ __('orders_show.invoice.total_amount') }}:</td>
                        <td class="text-right">{{ priceFormat($order->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="terms-section">
                <h4>{{ __('orders_show.invoice.order_information') }}</h4>
                <p><strong>{{ __('orders_show.invoice.delivery_date') }}:</strong> {{ $order->delivery_date }}</p>
                <p><strong>{{ __('orders_show.invoice.created_at') }}:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                <p><strong>{{ __('orders_show.invoice.last_updated') }}:</strong> {{ $order->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
        
        <div class="company-details">
            <p><strong>EURL BUSINESS CHALLENGE</strong></p>
            <p>{{ __('orders_show.invoice.headquarters') }}</p>
            <p>{{ __('orders_show.invoice.business_details') }}</p>
            <p>{{ __('orders_show.invoice.contact_info') }}</p>
        </div>
        
        <div class="invoice-footer">
            <div>
                <p class="mb-0">{{ __('orders_show.invoice.thank_you') }}</p>
            </div>
        </div>
    </div>
</body>
</html>