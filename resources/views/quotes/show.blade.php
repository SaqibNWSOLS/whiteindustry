@extends('layouts.app')

@section('title', isset($quote) ? __('quotes.page_title') : __('quotes.page_title'))
@section('page_title', isset($quote) ? __('quotes.page_title') : __('quotes.page_title'))

@section('content')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<div class="col-12" style="margin-bottom: 10px;">
    <div class="invoice-actions">
        <a href="{{ route('quotes.index') }}" class="">
            <i class="bi bi-back"></i> {{ __('quotes.buttons.back') }}
        </a>
        <a href="javascript:window.print()" class="btn btn-print">
            <i class="bi bi-printer"></i> {{ __('quotes.buttons.print') }}
        </a>
        <a id="invoice_download_btn" class="btn btn-download" href="{{ route('quotes.download-pdf', $quote->id) }}">
            <i class="bi bi-download"></i> {{ __('quotes.buttons.download_pdf') }}
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-9">
        
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
                    <div class="company-tagline">{{ __('quotes.invoice.dietary_supplements') }} • {{ __('quotes.invoice.cosmetic_products') }} • {{ __('quotes.invoice.hotel_amenities') }} • {{ __('quotes.invoice.para_pharmaceuticals') }}</div>
                </td>
                <td style="text-align: right"><img src="{{ asset('logo.png') }}"></td>
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
    </div>
    <div class="col-md-3">
        <div class="card">
                <div class="section">
                    <h4>@lang('production.details.documents.rnd_title')</h4>
<h6>Status: {{ $quote->rndQuote->status ?? '' }}</h6>

@if(isset($quote->rndQuote->status) && strtolower($quote->rndQuote->status) === 'approved' && empty($quote->orders))
    <a href="{{ route('quotes.convert-to-order', $quote->id) }}" 
       class="btn btn-success convert-to-order">
        <i class="fas fa-shopping-cart"></i> Convert to Order
    </a>
@endif
                    @forelse($rndDocuments as $doc)
                        <div class="document-box">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong>{{ $doc->document_name }}</strong>
                                <a href="{{ asset(Storage::url($doc->file_path)) }}" class="btn btn-sm btn-secondary" download>
                                    @lang('production.details.buttons.download')
                                </a>
                            </div>
                        </div>
                    @empty
                        <p>@lang('production.details.documents.no_rnd_documents')</p>
                    @endforelse
                </div>
            </div>
            <div class="card mt-3">
                <div class="section">
                    <h4>@lang('production.details.documents.qa_title')</h4>


                    @forelse($qaDocuments as $doc)
                        <div class="document-box">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong>{{ $doc->document_name }}</strong>
                                <a href="{{ asset(Storage::url($doc->file_path)) }}" class="btn btn-sm btn-secondary" download>
                                    @lang('production.details.buttons.download')
                                </a>
                            </div>
                        </div>
                    @empty
                        <p>@lang('production.details.documents.no_qa_documents')</p>
                    @endforelse
                </div>
            </div>
    </div>
</div>
<script>
document.querySelector('.btn-print').addEventListener('click', function() {
    // Get the head content with all CSS
    const headContent = document.head.innerHTML;
    const invoiceContent = document.querySelector('.invoice-container').innerHTML;
    const originalContent = document.body.innerHTML;
    
    // Replace entire document with print version
    document.documentElement.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>${headContent}</head>
        <body>${invoiceContent}</body>
        </html>
    `;
    
    window.print();
    
    // Restore original content
    location.reload();
});
</script>

@endsection