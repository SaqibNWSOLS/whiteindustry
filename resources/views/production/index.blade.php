<!-- ==================== PRODUCTION INDEX VIEW ==================== -->
<!-- resources/views/production/index.blade.php -->

@extends('layouts.app')
@section('title', __('production.title'))
@section('page_title', __('production.page_title'))

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-industry" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.total')</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.total')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.pending')</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.pending')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.in_progress')</h6>
                        <div class="h4 fw-bold">{{ $stats['in_progress'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.in_progress')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff8e1; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search" style="color: #ffa000;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.quality_check')</h6>
                        <div class="h4 fw-bold">{{ $stats['quality_check'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.quality_check')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.completed')</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.completed')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #f3e5f5; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice" style="color: #7b1fa2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('production.stats.with_invoices')</h6>
                        <div class="h4 fw-bold">{{ $stats['with_invoices'] }}</div>
                        <small class="text-muted">@lang('production.stats.descriptions.with_invoices')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">@lang('production.headers.production_jobs')</h2>
        <a href="{{ route('production.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>@lang('production.buttons.create')
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotesTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('production.headers.production_number')</th>
                            <th>@lang('production.headers.order_number')</th>
                            <th>@lang('production.headers.customer')</th>
                            <th>@lang('production.headers.start_date')</th>
                            <th>@lang('production.headers.status')</th>
                            <th>@lang('production.headers.invoices')</th>
                            <th>@lang('production.headers.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productions as $prod)
                            <tr>
                                <td><strong>{{ $prod->production_number }}</strong></td>
                                <td>{{ $prod->order->order_number }}</td>
                                <td>{{ $prod->order->customer->company_name }}</td>
                                <td>{{ $prod->start_date ? $prod->start_date : __('production.status.not_started') }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $prod->status === 'completed' ? 'success' : 
                                        ($prod->status === 'in_progress' ? 'info' : 
                                        ($prod->status === 'quality_check' ? 'warning' : 'secondary')) 
                                    }}">
                                        @if($prod->status === 'pending')
                                            @lang('production.status.pending')
                                        @elseif($prod->status === 'in_progress')
                                            @lang('production.status.in_progress')
                                        @elseif($prod->status === 'quality_check')
                                            @lang('production.status.quality_check')
                                        @elseif($prod->status === 'completed')
                                            @lang('production.status.completed')
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $prod->status)) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($prod->invoices->count() > 0)
                                        <span class="badge bg-info">
                                            <i class="fas fa-file-invoice me-1"></i>
                                            {{ $prod->invoices->count() }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('production.show', $prod->id) }}" class="btn btn-sm btn-outline-primary" title="@lang('production.buttons.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            
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
                search: "@lang('production.datatable.search')",
                lengthMenu: "@lang('production.datatable.show_entries')"
            }
        });
    });
</script>
@endsection