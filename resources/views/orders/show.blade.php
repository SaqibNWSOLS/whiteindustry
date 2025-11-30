@extends('layouts.app')

@section('title', __('orders_show.page_title'))
@section('page_title', __('orders_show.page_title'))

@section('content')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<div class="col-12" style="margin-bottom: 10px;">
    <div class="invoice-actions">
        <a href="{{ route('orders.index') }}" class="">
            <i class="bi bi-back"></i> {{ __('orders_show.buttons.back') }}
        </a>
        <a href="javascript:window.print()" class="btn btn-print">
            <i class="bi bi-printer"></i> {{ __('orders_show.buttons.print') }}
        </a>
        <a href="{{ route('orders.download-pdf', $order->id) }}" class="btn btn-download">
            <i class="bi bi-download"></i> {{ __('orders_show.buttons.download_pdf') }}
        </a>
    </div>
</div>

<div class="invoice-container" id="invoice-content">
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
                <td>
                    <div class="company-name">EURL BUSINESS CHALLENGE</div>
                    <div class="company-tagline">
                        {{ __('orders_show.invoice.dietary_supplements') }} • 
                        {{ __('orders_show.invoice.cosmetic_products') }} • 
                        {{ __('orders_show.invoice.hotel_amenities') }} • 
                        {{ __('orders_show.invoice.para_pharmaceuticals') }}
                    </div>
                </td>
                <td style="text-align: right"><img src="{{ asset('logo.png') }}"></td>
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
        
        @if($order->status === 'pending')
            <div class="action-buttons">
                <form action="{{ route('orders_show.confirm', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('orders_show.messages.confirm_order') }}')">
                        <i class="bi bi-check-circle"></i> {{ __('orders_show.buttons.confirm_order') }}
                    </button>
                </form>
            </div>
        @endif
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

<script>
document.querySelector('.btn-print').addEventListener('click', function() {
    const invoiceContent = document.querySelector('.invoice-container').innerHTML;
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = invoiceContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
});
</script>

@endsection