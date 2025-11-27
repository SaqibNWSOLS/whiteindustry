{{-- resources/views/crm/index.blade.php --}}
@extends('layouts.app')

@section('title', 'CRM')
@section('page_title', 'CRM')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <a href="{{ route('customers.index', ['type' => 'customer']) }}" class="tab-button {{ $type === 'customer' ? 'active' : '' }}">
                <i class="ti ti-users"></i> Customers
            </a>
           
            <a href="{{ route('quotes.index') }}" class="tab-button {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i> Quotes
            </a>
        </div>
    </div>

    @if($type === 'customer')
        {{-- CUSTOMERS TAB --}}
        <div id="customers-tab" class="tab-content active">
            <div class="module-header">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('customers.create', ['type' => 'customer']) }}" class="btn btn-primary">
                        <i class="ti ti-user-plus"></i> Add Customer
                    </a>
                    <a href="{{ route('customers.export', ['type' => 'customer']) }}" class="btn btn-secondary">
                        <i class="ti ti-download"></i> Export
                    </a>
                </div>
               
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <div class="table-container">
               

                @if($customers->isEmpty())
                    <div class="alert alert-info">No customers found</div>
                @else
                    <table id="quotesTable">
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                 <th>Type</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->company_name }}</td>
                                    <td>{{ $item->contact_person }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->city ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                   <td>
    <!-- Edit Button with Icon -->
    <a href="{{ route('customers.edit', $item->id) }}" class="btn btn-sm btn-primary" title="Edit">
        <i class="fas fa-edit"></i>
    </a>

    <!-- Delete Form with Icon -->
    <form method="POST" action="{{ route('customers.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($customers->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

   
    @endif
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
@endsection
