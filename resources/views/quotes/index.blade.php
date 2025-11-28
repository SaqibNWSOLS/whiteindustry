@extends('layouts.app')
@section('title', 'Quotes')
@section('page_title', 'Quotes')

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-alt" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Total Quotes</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">All quotes</small>
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
                        <h6 class="card-title mb-1">Draft</h6>
                        <div class="h4 fw-bold">{{ $stats['draft'] }}</div>
                        <small class="text-muted">In draft status</small>
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
                        <h6 class="card-title mb-1">Sent</h6>
                        <div class="h4 fw-bold">{{ $stats['sent'] }}</div>
                        <small class="text-muted">Sent to customers</small>
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
                        <h6 class="card-title mb-1">Accepted</h6>
                        <div class="h4 fw-bold">{{ $stats['accepted'] }}</div>
                        <small class="text-muted">Accepted by customers</small>
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
                        <h6 class="card-title mb-1">Rejected</h6>
                        <div class="h4 fw-bold">{{ $stats['rejected'] }}</div>
                        <small class="text-muted">Rejected by customers</small>
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
                        <h6 class="card-title mb-1">Completed</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">Completed quotes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Quotes List</h2>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Quote
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
                            <th>Quotation #</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotes as $quote)
                            <tr>
                                <td><strong>{{ $quote->quotation_number }}</strong></td>
                                <td>{{ $quote->customer->company_name ?? 'N/A' }}</td>
                                <td>{{ priceFormat($quote->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $quote->status === 'draft' ? 'secondary' : 
                                        ($quote->status === 'sent' ? 'info' : 
                                        ($quote->status === 'accepted' ? 'success' : 
                                        ($quote->status === 'rejected' ? 'danger' : 
                                        ($quote->status === 'completed' ? 'success' : 'warning'))))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $quote->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($quote->status === 'draft')
                                            <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        @if(!$quote->rndQuote)
                                            <form action="{{ route('rnd.send', $quote->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Send to R&D">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quote?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
                search: "Search quotes:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>


@endsection