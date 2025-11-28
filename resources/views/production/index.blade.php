<!-- ==================== PRODUCTION INDEX VIEW ==================== -->
<!-- resources/views/production/index.blade.php -->

@extends('layouts.app')
@section('title', 'Production')
@section('page_title', 'Production Management')

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-industry" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Total Production</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">All production jobs</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Pending</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">Awaiting production</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">In Progress</h6>
                        <div class="h4 fw-bold">{{ $stats['in_progress'] }}</div>
                        <small class="text-muted">Currently in production</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff8e1; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search" style="color: #ffa000;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Quality Check</h6>
                        <div class="h4 fw-bold">{{ $stats['quality_check'] }}</div>
                        <small class="text-muted">Under quality inspection</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Completed</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">Completed production</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #f3e5f5; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice" style="color: #7b1fa2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">With Invoices</h6>
                        <div class="h4 fw-bold">{{ $stats['with_invoices'] }}</div>
                        <small class="text-muted">Production with invoices</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Production Jobs</h2>
        <a href="{{ route('production.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Production
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotesTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Production #</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Invoices</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productions as $prod)
                            <tr>
                                <td><strong>{{ $prod->production_number }}</strong></td>
                                <td>{{ $prod->order->order_number }}</td>
                                <td>{{ $prod->order->customer->company_name }}</td>
                                <td>{{ $prod->start_date ? $prod->start_date : 'Not started' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $prod->status === 'completed' ? 'success' : 
                                        ($prod->status === 'in_progress' ? 'info' : 
                                        ($prod->status === 'quality_check' ? 'warning' : 'secondary')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $prod->status)) }}
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
                                    <a href="{{ route('production.show', $prod->id) }}" class="btn btn-sm btn-outline-primary" title="View Production">
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
                search: "Search production jobs:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>


@endsection