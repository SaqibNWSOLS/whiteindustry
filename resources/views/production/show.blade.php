@extends('layouts.app')
@section('title', __('production.details.title'))

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>@lang('production.details.production_number', ['number' => $production->production_number])</h2>
                    <div>
                        <!-- Inventory History Button -->
                        <a href="{{ route('production.inventory-history', $production->id) }}" class="btn btn-info mr-2">
                            <i class="fas fa-history"></i> @lang('production.details.buttons.inventory_history')
                        </a>
                        
                        @if($production->status === 'pending')
                            <a href="{{ route('production.start', $production->id) }}" class="btn btn-primary" onclick="return confirm('@lang('production.details.confirmations.start_production')')">
                                @lang('production.details.buttons.start_production')
                            </a>
                        @elseif($production->status === 'in_progress')
                            <a href="{{ route('production.complete', $production->id) }}" class="btn btn-success" onclick="return confirm('@lang('production.details.confirmations.complete_production')')">
                                @lang('production.details.buttons.complete_production')
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- PRODUCTION SUMMARY -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <h5>@lang('production.details.summary.order_number')</h5>
                                <p class="lead">{{ $production->order->order_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h5>@lang('production.details.summary.customer')</h5>
                                <p class="lead">{{ $production->order->customer->company_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h5>@lang('production.details.summary.status')</h5>
                                <p class="lead">
                                    <span class="badge badge-{{ $production->status === 'pending' ? 'warning' : ($production->status === 'completed' ? 'success' : 'info') }}">
                                        @lang('production.status.' . $production->status)
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h5>@lang('production.details.summary.progress')</h5>
                                <p class="lead">{{ $production->getProductionProgress() }}%</p>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $production->getProductionProgress() }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PRODUCTION DATES -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>@lang('production.details.dates.start_date'):</strong> {{ $production->start_date ?: __('production.dates.not_started') }}
                        </div>
                        <div class="col-md-6">
                            <strong>@lang('production.details.dates.end_date'):</strong> {{ $production->end_date ?: __('production.dates.not_completed') }}
                        </div>
                    </div>

                    @if($production->production_notes)
                        <div class="mb-4">
                            <strong>@lang('production.details.notes.title'):</strong>
                            <p>{{ $production->production_notes }}</p>
                        </div>
                    @endif

                    <!-- PRODUCTION ITEMS TABLE -->
                    <div class="table-responsive mb-4">
                        <h4>@lang('production.details.items.title')</h4>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('production.details.items.product_name')</th>
                                    <th>@lang('production.details.items.type')</th>
                                    <th>@lang('production.details.items.planned_quantity')</th>
                                    <th>@lang('production.details.items.produced_quantity')</th>
                                    <th>@lang('production.details.items.progress')</th>
                                    <th>@lang('production.details.items.status')</th>
                                    <th>@lang('production.details.items.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($production->items as $item)
                                    <tr>
                                        <td>{{ $item->orderProduct->product_name }}</td>
                                        <td>{{ $item->orderProduct->product_type }}</td>
                                        <td>{{ $item->quantity_planned }}</td>
                                        <td>{{ $item->quantity_produced }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" style="width: {{ $item->getProgressPercentage() }}%">
                                                    {{ $item->getProgressPercentage() }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $item->status === 'pending' ? 'secondary' : ($item->status === 'completed' ? 'success' : 'info') }}">
                                                @lang('production.status.' . $item->status)
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateModal{{ $item->id }}">
                                                @lang('production.details.buttons.update')
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Update Modal -->
                                    <div class="modal fade" id="updateModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">@lang('production.details.modal.title', ['product' => $item->orderProduct->product_name])</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('production.item.addReady', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>@lang('production.details.modal.current_status'):</strong><br>
                                                            @lang('production.details.modal.produced'): <strong>{{ $item->quantity_produced }}</strong> / @lang('production.details.modal.planned'): <strong>{{ $item->quantity_planned }}</strong><br>
                                                            @lang('production.details.modal.remaining'): <strong>{{ $item->quantity_planned - $item->quantity_produced }}</strong>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>@lang('production.details.modal.quantity_to_add')</label>
                                                            <input type="number" name="quantity_to_add" class="form-control" 
                                                                   placeholder="@lang('production.details.modal.quantity_placeholder')"
                                                                   min="1" 
                                                                   max="{{ $item->quantity_planned - $item->quantity_produced }}" 
                                                                   required>
                                                            <small class="form-text text-muted">@lang('production.details.modal.max_available', ['quantity' => $item->quantity_planned - $item->quantity_produced])</small>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>@lang('production.details.modal.notes')</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="@lang('production.details.modal.notes_placeholder')">{{ $item->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            @lang('production.details.buttons.close')
                                                        </button>
                                                        <button type="submit" class="btn btn-success">
                                                            @lang('production.details.buttons.add_quantity')
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($production->invoices->count() > 0)
                        <!-- INVOICES SECTION -->
                        <div class="mt-4">
                            <h4>@lang('production.details.invoices.title')</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>@lang('production.details.invoices.invoice_number')</th>
                                            <th>@lang('production.details.invoices.amount')</th>
                                            <th>@lang('production.details.invoices.status')</th>
                                            <th>@lang('production.details.invoices.date')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($production->invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>{{ priceFormat($invoice->total_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : 'warning' }}">
                                                        @lang('production.invoice_status.' . $invoice->status)
                                                    </span>
                                                </td>
                                                <td>{{ $invoice->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('production.index') }}" class="btn btn-secondary">
                            @lang('production.details.buttons.back_to_productions')
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="section">
                    <h4>@lang('production.details.documents.rnd_title')</h4>

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
</div>

<style>
    .qa-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 25px;
    }

    .qa-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .qa-header {
        padding: 20px;
        background: #f7f9fc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .qa-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }

    .badge-success {
        background: #28a745;
        padding: 6px 12px;
        color: #fff;
        border-radius: 30px;
    }

    .badge-warning {
        background: #ffc107;
        padding: 6px 12px;
        color: #000;
        border-radius: 30px;
    }

    .qa-body {
        padding: 25px;
    }

    .section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .section h4 {
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 18px;
        color: #444;
    }

    .document-box {
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 10px;
        background: #fafafa;
    }

    .document-box strong {
        color: #333;
    }

    .document-box small {
        color: #777;
    }

    textarea, input[type="file"] {
        margin-top: 8px;
    }

    .btn-primary,
    .btn-success,
    .btn-danger,
    .btn-secondary {
        padding: 8px 14px;
        font-size: 14px;
        border-radius: 6px !important;
    }

    .action-row {
        display: flex;
        gap: 20px;
    }

    .document-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .action-row {
            flex-direction: column;
        }
        
        .document-actions {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection