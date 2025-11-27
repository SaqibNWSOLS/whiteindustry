<!-- ==================== PRODUCTION INDEX VIEW ====================-->
<!-- resources/views/production/index.blade.php -->

@extends('layouts.app')
@section('title', 'Production')
@section('page_title', 'Production Management')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-industry" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total Production</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All production jobs</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-clock" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Pending</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['pending'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Awaiting production</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-play-circle" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">In Progress</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['in_progress'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Currently in production</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff8e1;">
                <i class="fas fa-search" style="color: #ffa000;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Quality Check</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['quality_check'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Under quality inspection</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-check-circle" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Completed</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['completed'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Completed production</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #f3e5f5;">
                <i class="fas fa-file-invoice" style="color: #7b1fa2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">With Invoices</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['with_invoices'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Production with invoices</div>
            </div>
        </div>
    </div>

    <div class="module-header">
        <a href="{{ route('production.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Production
        </a>
    </div>

    <div class="table-container">
        <table id="quotesTable">
            <thead>
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
                        <td>{{ $prod->production_number }}</td>
                        <td>{{ $prod->order->order_number }}</td>
                        <td>{{ $prod->order->quote->customer->company_name }}</td>
                        <td>{{ $prod->start_date ? $prod->start_date : 'Not started' }}</td>
                        <td>
                            <span class="badge {{ 
                                $prod->status === 'completed' ? 'badge-success' : 
                                ($prod->status === 'in_progress' ? 'badge-info' : 
                                ($prod->status === 'quality_check' ? 'badge-warning' : 'badge-secondary')) 
                            }}">
                                {{ ucfirst(str_replace('_', ' ', $prod->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $prod->invoices->count() }}</span>
                        </td>
                        <td>
                            <a href="{{ route('production.show', $prod->id) }}" class="btn btn-sm btn-secondary"> <i class="fas fa-eye"></i> </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No production found</td>
                    </tr>
                @endforelse
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

.badge-secondary {
    background-color: #6c757d;
    color: white;
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

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

</style>
@endsection