@extends('layouts.app')
@section('title', 'R&D Department')
@section('page_title', 'R&D Review')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-file-contract" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total R&D Quotes</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All R&D quotes</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-clock" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Pending</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['pending'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Awaiting review</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-search" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">In Review</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['in_review'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Currently reviewing</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-check-circle" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Approved</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['approved'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">R&D approved</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #ffebee;">
                <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Rejected</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['rejected'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">R&D rejected</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #f3e5f5;">
                <i class="fas fa-file-pdf" style="color: #7b1fa2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">With Documents</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['with_documents'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Quotes with files</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-container">
        <h3>R&D Quotations Pending Review</h3>
        <table id="quotesTable">
            <thead>
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
                        <td>{{ $rnd->quote->quotation_number }}</td>
                        <td>{{ $rnd->quote->customer->company_name }}</td>
                        <td>{{ $rnd->sent_at ? $rnd->sent_at : 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $rnd->status === 'approved' ? 'badge-success' : ($rnd->status === 'rejected' ? 'badge-danger' : ($rnd->status === 'in_review' ? 'badge-info' : 'badge-warning')) }}">
                                {{ ucfirst(str_replace('_', ' ', $rnd->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $rnd->documents->count() }} files</span>
                        </td>
                        <td>
                            <a href="{{ route('quotes.show', $rnd->quote_id) }}" class="btn btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('rnd.show', $rnd->id) }}" class="btn btn-sm btn-secondary"> <i class="fas fa-plus"></i> </a>
                        </td>
                    </tr>
                @empty
                    
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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
@endsection