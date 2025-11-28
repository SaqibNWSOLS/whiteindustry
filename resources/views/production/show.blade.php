@extends('layouts.app')
@section('title', 'Production Details')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Production {{ $production->production_number }}</h2>
            <div>
                @if($production->status === 'pending')
                    <a href="{{ route('production.start', $production->id) }}" class="btn btn-primary" onclick="return confirm('Start production?')">Start Production</a>
                @elseif($production->status === 'in_progress')
                    <a href="{{ route('production.complete', $production->id) }}" class="btn btn-success" onclick="return confirm('Complete production?')">Complete Production</a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- PRODUCTION SUMMARY -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box">
                        <h5>Order Number</h5>
                        <p class="lead">{{ $production->order->order_number }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <h5>Customer</h5>
                        <p class="lead">{{ $production->order->customer->company_name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <h5>Status</h5>
                        <p class="lead">
                            <span class="badge badge-{{ $production->status === 'pending' ? 'warning' : ($production->status === 'completed' ? 'success' : 'info') }}">
                                {{ ucfirst($production->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <h5>Progress</h5>
                        <p class="lead">{{ $production->getProductionProgress() }}%</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $production->getProductionProgress() }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRODUCTION DATES -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>Start Date:</strong> {{ $production->start_date }}
                </div>
                <div class="col-md-6">
                    <strong>End Date:</strong> {{ $production->end_date }}
                </div>
            </div>

            @if($production->production_notes)
                <div class="mb-4">
                    <strong>Notes:</strong>
                    <p>{{ $production->production_notes }}</p>
                </div>
            @endif

            <!-- PRODUCTION ITEMS TABLE -->
            <div class="table-responsive mb-4">
                <h4>Production Items</h4>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Type</th>
                            <th>Planned Quantity</th>
                            <th>Produced Quantity</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production->items as $item)
                            <tr>
                                <td>{{ $item->orderProduct->product_name }}</td>
                                <td>{{ $item->orderProduct->product_type }}</td>
                                <td>{{ $item->quantity_planned }}</td>
                                <td>{{ $item->quantity_produced }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: {{ $item->getProgressPercentage() }}%">
                                            {{ $item->getProgressPercentage() }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $item->status === 'pending' ? 'secondary' : ($item->status === 'completed' ? 'success' : 'info') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateModal{{ $item->id }}">
                                        Update
                                    </button>
                                </td>
                            </tr>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update {{ $item->orderProduct->product_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('production.item.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Quantity Produced {{ $item->id }}</label>
                                                    <input type="number" name="quantity_produced" class="form-control" 
                                                           value="{{ $item->quantity_produced }}" 
                                                           min="0" 
                                                           max="{{ $item->quantity_planned }}" required>
                                                    <small class="form-text text-muted">Max: {{ $item->quantity_planned }}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="quality_check" {{ $item->status === 'quality_check' ? 'selected' : '' }}>Quality Check</option>
                                                        <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3">{{ $item->notes }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Item</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                      
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($production->invoices->count() > 0)
                <!-- INVOICES SECTION -->
                <div class="mt-4">
                    <h4>Related Invoices</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($production->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $invoice->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Back to Productions</a>
            </div>
        </div>
    </div>
</div>

@endsection