@extends('layouts.app')
@section('title', 'Production Inventory History')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Inventory Transaction History - {{ $production->production_number }}</h4>
                            <a href="{{ route('production.show', $production->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Production
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Production Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>Production Number</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->production_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>Order Number</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->order->order_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>Customer</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->order->quote->customer->company_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>Total Transactions</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->inventoryTransactions->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Production Item</th>
                                        <th>Transaction Type</th>
                                        <th>Quantity Change</th>
                                        <th>Stock After</th>
                                        <th>Created By</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($production->inventoryTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($transaction->product)
                                                    <strong>{{ $transaction->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">Code: {{ $transaction->product->product_code }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->productionItem && $transaction->productionItem->orderProduct)
                                                    {{ $transaction->productionItem->orderProduct->product_name }}
                                                    <br>
                                                    <small class="text-muted">
                                                        Planned: {{ $transaction->productionItem->quantity_planned }} | 
                                                        Produced: {{ $transaction->productionItem->quantity_produced }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info text-uppercase">
                                                    {{ $transaction->transaction_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold {{ $transaction->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ $transaction->quantity_change }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->product)
                                                    {{ $transaction->product->current_stock }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $transaction->createdBy->name ?? 'System' }}
                                            </td>
                                            <td>
                                                @if($transaction->notes)
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-toggle="popover" 
                                                            title="Transaction Notes" 
                                                            data-content="{{ $transaction->notes }}">
                                                        View Notes
                                                    </button>
                                                @else
                                                    <span class="text-muted">No notes</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-history fa-2x mb-3"></i>
                                                    <p>No inventory transactions found for this production.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        @if($production->inventoryTransactions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="mb-0">Transaction Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <h6>Total Added to Stock</h6>
                                                <h3 class="text-success">
                                                    +{{ $production->inventoryTransactions->where('quantity_change', '>', 0)->sum('quantity_change') }}
                                                </h3>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6>Total Transactions</h6>
                                                <h3>{{ $production->inventoryTransactions->count() }}</h3>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6>Unique Products</h6>
                                                <h3>{{ $production->inventoryTransactions->pluck('product_id')->unique()->count() }}</h3>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6>Completed</h6>
                                                <h3 class="text-success">
                                                    {{ $production->inventoryTransactions->where('status', 'completed')->count() }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        placement: 'top'
    });
});
</script>
@endpush

<style>
.info-box {
    border-left: 4px solid #007bff;
}
.table th {
    border-top: none;
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
}
</style>