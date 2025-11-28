@extends('layouts.app')
@section('title', 'R&D Department')
@section('page_title', 'R&D Review')

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-contract" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Total R&D Quotes</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">All R&D quotes</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Pending</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">Awaiting review</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">In Review</h6>
                        <div class="h4 fw-bold">{{ $stats['in_review'] }}</div>
                        <small class="text-muted">Currently reviewing</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Approved</h6>
                        <div class="h4 fw-bold">{{ $stats['approved'] }}</div>
                        <small class="text-muted">R&D approved</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Rejected</h6>
                        <div class="h4 fw-bold">{{ $stats['rejected'] }}</div>
                        <small class="text-muted">R&D rejected</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4  mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #f3e5f5; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-pdf" style="color: #7b1fa2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">With Documents</h6>
                        <div class="h4 fw-bold">{{ $stats['with_documents'] }}</div>
                        <small class="text-muted">Quotes with files</small>
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
            <h3 class="card-title mb-0">R&D Quotations Pending Review</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotesTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Quotation #</th>
                            <th>Customer</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rndDepartments as $rnd)
                            <tr>
                                <td><strong>{{ $rnd->quote->quotation_number }}</strong></td>
                                <td>{{ $rnd->quote->customer->company_name }}</td>
                                <td>{{ $rnd->sent_at ? $rnd->sent_at : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $rnd->status === 'approved' ? 'success' : 
                                        ($rnd->status === 'rejected' ? 'danger' : 
                                        ($rnd->status === 'in_review' ? 'info' : 'warning'))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $rnd->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($rnd->documents->count() > 0)
                                        <span class="badge bg-info">
                                            <i class="fas fa-file me-1"></i>
                                            {{ $rnd->documents->count() }} files
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            No files
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('quotes.show', $rnd->quote_id) }}" class="btn btn-sm btn-outline-secondary" title="View Quote">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rnd.show', $rnd->id) }}" class="btn btn-sm btn-outline-primary" title="R&D Details">
                                            <i class="fas fa-clipboard-list"></i>
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
                search: "Search R&D quotes:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>

<style>

</style>
@endsection