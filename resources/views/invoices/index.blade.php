<!-- ==================== INVOICES INDEX VIEW ====================-->
<!-- resources/views/invoices/index.blade.php -->

@extends('layouts.app')
@section('title', 'Invoices')
@section('page_title', 'Invoices Management')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-file-invoice" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total Invoices</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All invoices</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-edit" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Draft</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['draft'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Draft invoices</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-paper-plane" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Sent</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['sent'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Sent to customers</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-check-circle" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Paid</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['paid'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Paid invoices</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #ffebee;">
                <i class="fas fa-exclamation-circle" style="color: #d32f2f;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Overdue</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['overdue'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Overdue invoices</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #f3e5f5;">
                <i class="fas fa-dollar-sign" style="color: #7b1fa2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total Revenue</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">${{ number_format($stats['total_revenue'], 2) }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Total invoice amount</div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table id="quotesTable">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Invoice Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer->company_name }}</td>
                        <td>{{ $invoice->invoice_date ? $invoice->invoice_date : 'N/A' }}</td>
                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            <span class="badge {{ 
                                $invoice->status === 'paid' ? 'badge-success' : 
                                ($invoice->status === 'sent' ? 'badge-info' : 
                                ($invoice->status === 'overdue' ? 'badge-danger' : 'badge-secondary')) 
                            }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                      <td>
    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-eye"></i> 
    </a>

    <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-secondary" >
        <i class="fas fa-file-pdf"></i> 
    </a>
</td>

                    </tr>
                @empty
                  
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


</style>
@endsection