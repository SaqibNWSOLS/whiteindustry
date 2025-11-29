@extends('layouts.app')
@section('title', __('invoice.title'))

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.total') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">{{ __('invoice.stats.all_invoices') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.draft') }}</h6>
                        <div class="h4 fw-bold text-warning">{{ $stats['draft'] }}</div>
                        <small class="text-muted">{{ __('invoice.stats.draft_invoices') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paper-plane" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.issued') }}</h6>
                        <div class="h4 fw-bold text-info">{{ $stats['issued'] }}</div>
                        <small class="text-muted">{{ __('invoice.stats.issued_invoices') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.paid') }}</h6>
                        <div class="h4 fw-bold text-success">{{ $stats['paid'] }}</div>
                        <small class="text-muted">{{ __('invoice.stats.paid_invoices') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.cancelled') }}</h6>
                        <div class="h4 fw-bold text-danger">{{ $stats['cancelled'] }}</div>
                        <small class="text-muted">{{ __('invoice.stats.cancelled_invoices') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff8e1; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #ffa000;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('invoice.stats.pending') }}</h6>
                        <div class="h4 fw-bold text-warning">{{ priceFormat(App\Models\Invoice::sum('pending_amount'), 2) }}</div>
                        <small class="text-muted">{{ __('invoice.stats.pending_amount') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('invoice.title') }}</h2>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('invoice.buttons.create_invoice') }}
            </a>
        </div>

        <div class="card-body">
            <!-- INVOICES TABLE -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('invoice.headers.invoice_number') }}</th>
                            <th>{{ __('invoice.headers.production') }}</th>
                            <th>{{ __('invoice.headers.customer') }}</th>
                            <th>{{ __('invoice.headers.total') }}</th>
                            <th>{{ __('invoice.headers.paid') }}</th>
                            <th>{{ __('invoice.headers.pending') }}</th>
                            <th>{{ __('invoice.headers.progress') }}</th>
                            <th>{{ __('invoice.headers.status') }}</th>
                            <th>{{ __('invoice.headers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->production->production_number }}</td>
                                <td>{{ $invoice->customer->company_name }}</td>
                                <td>{{ priceFormat($invoice->total_amount) }}</td>
                                <td><span class="text-success">{{ priceFormat($invoice->paid_amount) }}</span></td>
                                <td><span class="text-danger">{{ priceFormat($invoice->pending_amount) }}</span></td>
                                <td>
                                    <div class="progress" style="height: 20px; min-width: 100px;">
                                        <div class="progress-bar bg-success" style="width: {{ $invoice->payment_progress }}%;" 
                                             title="{{ $invoice->payment_progress }}%">
                                            {{ $invoice->payment_progress }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $invoice->status === 'paid' ? 'success' : 
                                        ($invoice->status === 'issued' ? 'info' : 
                                        ($invoice->status === 'draft' ? 'warning' : 'danger'))
                                    }}">
                                        {{ __("invoice.status.{$invoice->status}") }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-info" title="{{ __('invoice.buttons.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($invoice->status === 'draft')
                                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-warning" title="{{ __('invoice.buttons.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">{{ __('invoice.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $invoices->links() }}
        </div>
    </div>
</div>

<style>
.progress {
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar {
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection