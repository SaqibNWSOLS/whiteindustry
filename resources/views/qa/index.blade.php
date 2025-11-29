<!-- ==================== QUALITY CONTROL INDEX VIEW ==================== -->
<!-- resources/views/quality_control.blade.php -->

@extends('layouts.app')
@section('title', __('quality_control.title'))
@section('page_title', __('quality_control.page_title'))

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-check" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('quality_control.stats.total_quotes')</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.all_quotes')</small>
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
                        <h6 class="card-title mb-1">@lang('quality_control.stats.pending')</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.awaiting_review')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('quality_control.stats.in_review')</h6>
                        <div class="h4 fw-bold">{{ $stats['in_review'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.currently_reviewing')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('quality_control.stats.approved')</h6>
                        <div class="h4 fw-bold">{{ $stats['approved'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.qa_approved')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('quality_control.stats.rejected')</h6>
                        <div class="h4 fw-bold">{{ $stats['rejected'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.qa_rejected')</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #f3e5f5; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-alt" style="color: #7b1fa2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">@lang('quality_control.stats.with_documents')</h6>
                        <div class="h4 fw-bold">{{ $stats['with_documents'] }}</div>
                        <small class="text-muted">@lang('quality_control.stats.quotes_with_files')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">@lang('quality_control.table.title')</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotesTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('quality_control.table.quotation_number')</th>
                            <th>@lang('quality_control.table.customer')</th>
                            <th>@lang('quality_control.table.sent_date')</th>
                            <th>@lang('quality_control.table.status')</th>
                            <th>@lang('quality_control.table.documents')</th>
                            <th>@lang('quality_control.table.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qaDepartments as $qa)
                            <tr>
                                <td><strong>{{ $qa->order->order_number }}</strong></td>
                                <td>{{ $qa->order->customer->company_name }}</td>
                                <td>
                                    {{ $qa->sent_at ? $qa->sent_at->format('Y-m-d H:i') : 'Pending' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $qa->status === 'approved' ? 'success' : 
                                        ($qa->status === 'rejected' ? 'danger' : 
                                        ($qa->status === 'in_review' ? 'info' : 'warning'))
                                    }}">
                                        @lang('quality_control.status.' . $qa->status)
                                    </span>
                                </td>
                                <td>
                                    @if($qa->documents->count() > 0)
                                        <span class="badge bg-info">
                                            <i class="fas fa-file me-1"></i>
                                            @lang('quality_control.table.files_count', ['count' => $qa->documents->count()])
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            @lang('quality_control.table.no_files')
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('orders.show', $qa->orders_id) }}" class="btn btn-sm btn-outline-secondary" title="@lang('quality_control.actions.view_order')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('qa.show', $qa->id) }}" class="btn btn-sm btn-outline-primary" title="@lang('quality_control.actions.qa_review')">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                    </div>
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
                search: "@lang('quality_control.table.search_placeholder')",
                lengthMenu: "@lang('quality_control.table.show_entries')"
            }
        });
    });
</script>


@endsection

