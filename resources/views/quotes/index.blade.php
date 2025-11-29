@extends('layouts.app')
@section('title', __('quotes.title'))
@section('page_title', __('quotes.page_title'))

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <a href="{{ route('customers.index', ['type' => 'customer']) }}" class="tab-button ">
                <i class="ti ti-users"></i> {{ __('crm.tabs.customers') }}
            </a>
           
            <a href="{{ route('quotes.index') }}" class="tab-button {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i> {{ __('quotes.title') }}
            </a>
        </div>
    </div>
    
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-alt" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.total_quotes') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.all_quotes') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.draft') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['draft'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.in_draft_status') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paper-plane" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.sent') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['sent'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.sent_to_customers') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.accepted') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['accepted'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.accepted_by_customers') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.rejected') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['rejected'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.rejected_by_customers') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #f3e5f5; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-flag-checkered" style="color: #7b1fa2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('quotes.stats.completed') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">{{ __('quotes.stats.completed_quotes') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ __('quotes.quotes_list') }}</h2>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('quotes.new_quote') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotesTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('quotes.table.quotation_number') }}</th>
                            <th>{{ __('quotes.table.customer') }}</th>
                            <th>{{ __('quotes.table.total_amount') }}</th>
                            <th>{{ __('quotes.table.status') }}</th>
                            <th>{{ __('quotes.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotes as $quote)
                            <tr>
                                <td><strong>{{ $quote->quotation_number }}</strong></td>
                                <td>{{ $quote->customer->company_name ?? __('quotes.messages.customer_not_found') }}</td>
                                <td>{{ priceFormat($quote->total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'sent' => 'info', 
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'completed' => 'success',
                                            'expired' => 'warning'
                                        ];
                                        $color = $statusColors[$quote->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ __("quotes.status.{$quote->status}") }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('quotes.actions.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($quote->status === 'draft')
                                            <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('quotes.actions.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        @if(!$quote->rndQuote)
                                            <form action="{{ route('rnd.send', $quote->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ __('quotes.actions.send_to_rnd') }}">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('quotes.actions.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('quotes.actions.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">{{ __('quotes.messages.no_quotes') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#quotesTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            searching: true,
            language: {
                search: "{{ __('quotes.table.search_quotes') }}",
                lengthMenu: "{{ __('quotes.table.show_entries') }}",
                @if(app()->getLocale() === 'fr')
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
                @else
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
                @endif
            }
        });
    });
</script>
@endsection