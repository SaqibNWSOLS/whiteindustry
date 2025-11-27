@extends('layouts.app')
@section('title', 'Quotes')
@section('page_title', 'Quotes')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-file-alt" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total Quotes</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All quotes</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-edit" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Draft</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['draft'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">In draft status</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-paper-plane" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Sent</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['sent'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Sent to customers</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-check-circle" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Accepted</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['accepted'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Accepted by customers</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #ffebee;">
                <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Rejected</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['rejected'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Rejected by customers</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #f3e5f5;">
                <i class="fas fa-flag-checkered" style="color: #7b1fa2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Completed</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['completed'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Completed quotes</div>
            </div>
        </div>
    </div>

    <div class="module-header">
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                <i class="ti ti-file-plus"></i> New Quote
            </a>
        </div>
        
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-container">
        <table id="quotesTable">
            <thead>
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
                        <td>{{ $quote->quotation_number }}</td>
                        <td>{{ $quote->customer->company_name ?? 'N/A' }}</td>
                        <td>${{ number_format($quote->total_amount, 2) }}</td>
                        <td>
                            <span class="badge {{ $quote->status === 'draft' ? 'badge-secondary' : ($quote->status === 'sent' ? 'badge-info' : ($quote->status === 'accepted' ? 'badge-success' : ($quote->status === 'rejected' ? 'badge-danger' : ($quote->status === 'completed' ? 'badge-completed' : 'badge-warning')))) }}">
                                {{ ucfirst(str_replace('_', ' ', $quote->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($quote->status === 'draft')
                                <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            
                            @if(!$quote->rndQuote)
                                <form action="{{ route('rnd.send', $quote->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Send to R&D">
                                        <i class="fas fa-paper-plane"></i> Send to R&D
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#quotesTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            searching: true
        });
    });
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}


</style>
@endsection