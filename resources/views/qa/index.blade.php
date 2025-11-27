<!-- ==================== Q&A INDEX VIEW ====================-->
<!-- resources/views/qa/index.blade.php -->

@extends('layouts.app')
@section('title', 'QA Department')
@section('page_title', 'QA Review')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-clipboard-check" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total QA Quotes</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All QA quotes</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-clock" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Pending</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['pending'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Awaiting QA review</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-search" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">In Review</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['in_review'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Currently in QA</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-check-circle" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Approved</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['approved'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">QA approved</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #ffebee;">
                <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Rejected</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['rejected'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">QA rejected</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #f3e5f5;">
                <i class="fas fa-file-alt" style="color: #7b1fa2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">With Documents</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['with_documents'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Quotes with QA files</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-container">
        <h3>QA Quotations Pending Review</h3>
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
                @forelse($qaDepartments as $qa)
                    <tr>
                        <td>{{ $qa->order->order_number }}</td>
                        <td>{{ $qa->order->customer->company_name }}</td>
                        <td>
                            {{ $qa->sent_at ? $qa->sent_at->format('Y-m-d H:i') : 'Pending' }}
                        </td>
                        <td>
                            <span class="badge {{ $qa->status === 'approved' ? 'badge-success' : ($qa->status === 'rejected' ? 'badge-danger' : ($qa->status === 'in_review' ? 'badge-info' : 'badge-warning')) }}">
                                {{ ucfirst(str_replace('_', ' ', $qa->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $qa->documents->count() }} files</span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $qa->orders_id) }}" class="btn btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            <a href="{{ route('qa.show', $qa->id) }}" class="btn btn-sm btn-secondary"> <i class="fas fa-plus"></i> </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No quotations pending</td>
                    </tr>
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

.card-content {
    flex: 1;
}

.card-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.wi-highlight {
    color: #555;
    font-weight: 600;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.table-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
}

.table-container h3 {
    margin: 0 0 1rem 0;
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.text-center {
    text-align: center;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .table-container {
        overflow-x: auto;
    }
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