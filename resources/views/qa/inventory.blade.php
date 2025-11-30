@extends('layouts.app')
@section('title', __('production.inventory_history.title'))

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">@lang('production.inventory_history.header', ['number' => $production->production_number])</h4>
                            <a href="{{ route('production.show', $production->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> @lang('production.inventory_history.buttons.back_to_production')
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Production Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>@lang('production.inventory_history.summary.production_number')</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->production_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>@lang('production.inventory_history.summary.order_number')</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->order->order_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>@lang('production.inventory_history.summary.customer')</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->order->customer->company_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light p-3 rounded">
                                    <h6>@lang('production.inventory_history.summary.total_transactions')</h6>
                                    <p class="mb-0 font-weight-bold">{{ $production->inventoryTransactions->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($pendingTransactions->count() > 0 && auth()->user()->can('Approve Inventory'))
                        <div class="row mb-4" style="display:none">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>@lang('production.inventory_history.messages.pending_transactions', ['count' => $pendingTransactions->count()])</strong>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-success btn-sm" id="approveAllBtn">
                                                <i class="fas fa-check-circle"></i> @lang('production.inventory_history.buttons.approve_all')
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" id="rejectAllBtn">
                                                <i class="fas fa-times-circle"></i> @lang('production.inventory_history.buttons.reject_all')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Inventory Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>@lang('production.inventory_history.table.date')</th>
                                        <th>@lang('production.inventory_history.table.product')</th>
                                        <th>@lang('production.inventory_history.table.production_item')</th>
                                        <th>@lang('production.inventory_history.table.transaction_type')</th>
                                        <th>@lang('production.inventory_history.table.quantity_change')</th>
                                        <th>@lang('production.inventory_history.table.stock_after')</th>
                                        <th>@lang('production.inventory_history.table.created_by')</th>
                                        <th>@lang('production.inventory_history.table.notes')</th>
                                        <th>@lang('production.inventory_history.table.status')</th>
                                        <th>@lang('production.inventory_history.table.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($production->inventoryTransactions as $transaction)
                                        <tr data-transaction-id="{{ $transaction->id }}">
                                            <td>{{ $transaction->transaction_date->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($transaction->product)
                                                    <strong>{{ $transaction->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">@lang('production.inventory_history.table.product_code'): {{ $transaction->product->product_code }}</small>
                                                @else
                                                    <span class="text-muted">@lang('production.inventory_history.table.not_available')</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->productionItem && $transaction->productionItem->orderProduct)
                                                    {{ $transaction->productionItem->orderProduct->product_name }}
                                                    <br>
                                                    <small class="text-muted">
                                                        @lang('production.inventory_history.table.planned'): {{ $transaction->productionItem->quantity_planned }} | 
                                                        @lang('production.inventory_history.table.produced'): {{ $transaction->productionItem->quantity_produced }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">@lang('production.inventory_history.table.not_available')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info text-uppercase">
                                                    @lang('production.inventory_history.transaction_types.' . $transaction->transaction_type)
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
                                                    <span class="text-muted">@lang('production.inventory_history.table.not_available')</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $transaction->createdBy->name ?? __('production.inventory_history.table.system') }}
                                            </td>
                                            <td>
                                                @if($transaction->notes)
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-toggle="popover" 
                                                            title="@lang('production.inventory_history.table.notes_title')" 
                                                            data-content="{{ $transaction->notes }}">
                                                        @lang('production.inventory_history.table.view_notes')
                                                    </button>
                                                @else
                                                    <span class="text-muted">@lang('production.inventory_history.table.no_notes')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $transaction->getStatusBadgeClass() }}">
                                                    @lang('production.inventory_history.status.' . $transaction->status)
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->status === 'pending' && auth()->user()->can('Approve Inventory'))
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-success approve-btn" 
                                                                data-transaction-id="{{ $transaction->id }}"
                                                                data-toggle="tooltip" 
                                                                title="@lang('production.inventory_history.buttons.approve')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger reject-btn" 
                                                                data-transaction-id="{{ $transaction->id }}"
                                                                data-toggle="tooltip" 
                                                                title="@lang('production.inventory_history.buttons.reject')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @elseif($transaction->status === 'completed')
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle"></i>
                                                        
                                                        @lang('production.inventory_history.messages.approved_by') 
                                                        {{ $transaction->approvedBy->name ?? __('production.inventory_history.table.system') }}
                                                    </span>
                                                @elseif($transaction->status === 'rejected')
                                                    <span class="text-danger">
                                                        <i class="fas fa-times-circle"></i>
                                                        @lang('production.inventory_history.messages.rejected_by') 
                                                        {{ $transaction->approvedBy->name ?? __('production.inventory_history.table.system') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">@lang('production.inventory_history.table.no_actions')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-history fa-2x mb-3"></i>
                                                    <p>@lang('production.inventory_history.messages.no_transactions')</p>
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
                                        <h5 class="mb-0">@lang('production.inventory_history.summary_title')</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.total_added')</h6>
                                                <h3 class="text-success">
                                                    +{{ $production->inventoryTransactions->where('quantity_change', '>', 0)->sum('quantity_change') }}
                                                </h3>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.total_removed')</h6>
                                                <h3 class="text-danger">
                                                    {{ $production->inventoryTransactions->where('quantity_change', '<', 0)->sum('quantity_change') }}
                                                </h3>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.total_transactions')</h6>
                                                <h3>{{ $production->inventoryTransactions->count() }}</h3>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.unique_products')</h6>
                                                <h3>{{ $production->inventoryTransactions->pluck('product_id')->unique()->count() }}</h3>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.pending')</h6>
                                                <h3 class="text-warning">{{ $pendingTransactions->count() }}</h3>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6>@lang('production.inventory_history.summary.completed')</h6>
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

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">@lang('production.inventory_history.modal.reject_title')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    @csrf
                    <input type="hidden" name="transaction_id" id="rejectTransactionId">
                    <div class="form-group">
                        <label for="reject_reason">@lang('production.inventory_history.modal.reject_reason')</label>
                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="4" 
                                  placeholder="@lang('production.inventory_history.modal.reject_placeholder')" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('production.inventory_history.buttons.cancel')</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">@lang('production.inventory_history.buttons.confirm_reject')</button>
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

    $('[data-toggle="tooltip"]').tooltip();

    // Approve single transaction
    $('.approve-btn').click(function() {
        const transactionId = $(this).data('transaction-id');
        approveTransaction(transactionId);
    });

    // Reject single transaction - open modal
    $('.reject-btn').click(function() {
        const transactionId = $(this).data('transaction-id');
        $('#rejectTransactionId').val(transactionId);
        $('#rejectModal').modal('show');
    });

    // Confirm reject
    $('#confirmRejectBtn').click(function() {
        const transactionId = $('#rejectTransactionId').val();
        const rejectReason = $('#reject_reason').val();
        
        if (!rejectReason.trim()) {
            alert('@lang('production.inventory_history.messages.reject_reason_required')');
            return;
        }

        rejectTransaction(transactionId, rejectReason);
    });

    // Approve all pending transactions
    $('#approveAllBtn').click(function() {
        if (confirm('@lang('production.inventory_history.messages.confirm_approve_all')')) {
            approveAllTransactions();
        }
    });

    // Reject all pending transactions
    $('#rejectAllBtn').click(function() {
        if (confirm('@lang('production.inventory_history.messages.confirm_reject_all')')) {
            $('#rejectModal').modal('show');
            $('#rejectTransactionId').val('all');
        }
    });

    function approveTransaction(transactionId) {
        $.ajax({
            url: '{{ route("inventory-transactions.approve", "") }}/' + transactionId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PATCH'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('@lang('production.inventory_history.messages.approve_error')');
            }
        });
    }

    function rejectTransaction(transactionId, reason) {
        const url = transactionId === 'all' 
            ? '{{ route("inventory-transactions.reject-all", $production->id) }}'
            : '{{ route("inventory-transactions.reject", "") }}/' + transactionId;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PATCH',
                reject_reason: reason
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('@lang('production.inventory_history.messages.reject_error')');
            }
        });
    }

    function approveAllTransactions() {
        $.ajax({
            url: '{{ route("inventory-transactions.approve-all", $production->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PATCH'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('@lang('production.inventory_history.messages.approve_all_error')');
            }
        });
    }

    // Clear reject form when modal is hidden
    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejectForm')[0].reset();
        $('#rejectTransactionId').val('');
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
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>