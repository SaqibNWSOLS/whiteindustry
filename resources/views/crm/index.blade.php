{{-- resources/views/crm/index.blade.php --}}
@extends('layouts.app')

@section('title', __('crm.title'))
@section('page_title', __('crm.page_title'))

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <a href="{{ route('customers.index', ['type' => 'customer']) }}" class="tab-button {{ $type === 'customer' ? 'active' : '' }}">
                <i class="ti ti-users"></i> {{ __('crm.tabs.customers') }}
            </a>
           
            <a href="{{ route('quotes.index') }}" class="tab-button {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i> {{ __('crm.tabs.quotes') }}
            </a>
        </div>
    </div>

    @if($type === 'customer')
        {{-- CUSTOMERS TAB --}}
        <div id="customers-tab" class="tab-content active">
            <div class="module-header">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('customers.create', ['type' => 'customer']) }}" class="btn btn-primary">
                        <i class="ti ti-user-plus"></i> {{ __('crm.actions.add_customer') }}
                    </a>
                    <a href="{{ route('customers.export', ['type' => 'customer']) }}" class="btn btn-secondary">
                        <i class="ti ti-download"></i> {{ __('crm.actions.export') }}
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
                    <div class="alert alert-info">{{ __('crm.messages.no_customers') }}</div>
                @else
                    <table id="quotesTable">
                        <thead>
                            <tr>
                                <th>{{ __('crm.table.customer_id') }}</th>
                                <th>{{ __('crm.table.type') }}</th>
                                <th>{{ __('crm.table.company_name') }}</th>
                                <th>{{ __('crm.table.contact_person') }}</th>
                                <th>{{ __('crm.table.email') }}</th>
                                <th>{{ __('crm.table.phone') }}</th>
                                <th>{{ __('crm.table.city') }}</th>
                                <th>{{ __('crm.table.status') }}</th>
                                <th>{{ __('crm.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @if(isset($item->type) && isset(__('crm.types')[$item->type]))
                                            {{ __('crm.types')[$item->type] }}
                                        @else
                                            {{ $item->type ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $item->company_name }}</td>
                                    <td>{{ $item->contact_person }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->city ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            @if(isset($item->status) && isset(__('crm.status')[$item->status]))
                                                {{ __('crm.status')[$item->status] }}
                                            @else
                                                {{ $item->status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Edit Button with Icon -->
                                        <a href="{{ route('customers.edit', $item->id) }}" class="btn btn-sm btn-primary" title="{{ __('crm.actions.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Form with Icon -->
                                        <form method="POST" action="{{ route('customers.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('crm.actions.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('crm.actions.delete') }}">
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
            searching: true,
            language: {
                @if(app()->getLocale() === 'fr')
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
                @else
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
                @endif
            }
        });
    });
</script>
@endsection