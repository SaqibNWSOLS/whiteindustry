@extends('layouts.app')

@section('title', __('products.title'))
@section('page_title', __('products.page_title'))

@section('content')
    <div class="content">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="tabs">
            <div class="tab-nav">
                <button class="tab-button active" data-tab="raw">@lang('products.tabs.raw_materials')</button>
                <button class="tab-button" data-tab="packaging">@lang('products.tabs.packaging')</button>
                <button class="tab-button" data-tab="final">@lang('products.tabs.final_products')</button>
            </div>
        </div>

        <!-- Raw Materials Tab -->
        <div id="products-raw" class="tab-content active">
            <div class="module-header">
                <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=raw_material" data-ajax-popup="true"  data-title="@lang('products.actions.add_raw_product')" class="btn btn-primary"><i class="ti ti-package"></i>@lang('products.actions.add_raw_product')</a>
                <a href="{{ route('products.export', ['category' => 'raw_material']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> @lang('products.actions.export')
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.total_products')</span></h3>
                    <div id="stat-raw-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['raw_material']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.raw_material_products')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.active_ingredients')</span></h3>
                    <div id="stat-raw-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['raw_material']['active'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.premium_ingredients')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.avg_price')</span></h3>
                    <div id="stat-raw-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['raw_material']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.per_unit')</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>@lang('products.table.raw_materials_catalog')</h3>
                </div>
                <table id="products-raw-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('products.table.product_code')</th>
                            <th>@lang('products.table.name')</th>
                            <th>@lang('products.table.category')</th>
                            <th>@lang('products.table.unit_price')</th>
                            <th>@lang('products.table.status')</th>
                            <th>@lang('products.table.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Final Products Tab -->
        <div id="products-final" class="tab-content">
            <div class="module-header">
                 <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=final_product" data-ajax-popup="true"  data-title="@lang('products.actions.add_final_product')" class="btn btn-primary"><i class="ti ti-package"></i>@lang('products.actions.add_final_product')</a>
               
                <a href="{{ route('products.export', ['category' => 'final_product']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> @lang('products.actions.export')
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.total_products')</span></h3>
                    <div id="stat-final-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['final_product']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.final_products')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.active_products')</span></h3>
                    <div id="stat-final-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['final_product']['active'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.active_products_count')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.avg_price')</span></h3>
                    <div id="stat-final-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['final_product']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.per_unit')</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>@lang('products.table.final_products_catalog')</h3>
                </div>
                <table id="products-final-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('products.table.product_code')</th>
                            <th>@lang('products.table.name')</th>
                            <th>@lang('products.table.category')</th>
                            <th>@lang('products.table.unit_price')</th>
                            <th>@lang('products.table.status')</th>
                            <th>@lang('products.table.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Packaging Tab -->
        <div id="products-packaging" class="tab-content">
            <div class="module-header">
                <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=packaging" data-ajax-popup="true"  data-title="@lang('products.actions.add_packaging_product')" class="btn btn-primary"><i class="ti ti-package"></i>@lang('products.actions.add_packaging_product')</a>

                <a href="{{ route('products.export', ['category' => 'packaging']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> @lang('products.actions.export')
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.total_products')</span></h3>
                    <div id="stat-packaging-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['packaging']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.packaging_products')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.container_types')</span></h3>
                    <div id="stat-packaging-types" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['packaging']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.different_types')</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">@lang('products.stats.avg_price')</span></h3>
                    <div id="stat-packaging-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['packaging']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">@lang('products.stats.per_unit')</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>@lang('products.table.packaging_products')</h3>
                </div>
                <table id="products-packaging-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('products.table.product_code')</th>
                            <th>@lang('products.table.name')</th>
                            <th>@lang('products.table.category')</th>
                            <th>@lang('products.table.unit_price')</th>
                            <th>@lang('products.table.status')</th>
                            <th>@lang('products.table.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .dropdown-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize all DataTables
            initializeDataTables();
            
            // Tab switching
            $('.tab-button').on('click', function() {
                const tab = $(this).data('tab');
                switchTab(tab);
            });
        });
        
        let dataTables = {};
        
        function initializeDataTables() {
            const categories = ['raw', 'packaging', 'blend', 'final'];
            
            categories.forEach(category => {
                const tableId = `products-${category}-table`;
                const categoryValue = getCategoryValue(category);
                
                dataTables[category] = $(`#${tableId}`).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('products.index') }}',
                        data: function(d) {
                            d.category = categoryValue;
                        }
                    },
                    columns: [
                        { data: 'product_code', name: 'product_code' },
                        { data: 'name', name: 'name' },
                        { 
                            data: 'category', 
                            name: 'category',
                            render: function(data) {
                                return `<span class="badge badge-info">${data}</span>`;
                            }
                        },
                        { 
                            data: 'unit_price', 
                            name: 'unit_price',
                            render: function(data) {
                                return data ? 'DZD ' + Number(data).toLocaleString() : '—';
                            }
                        },
                        { 
                            data: 'status', 
                            name: 'status',
                            render: function(data, type, row) {
                                const badgeClass = data === 'active' ? 'badge-success' : 
                                                 data === 'inactive' ? 'badge-warning' : 'badge-secondary';
                                const statusText = data === 'active' ? '@lang("products.status.active")' : 
                                                 data === 'inactive' ? '@lang("products.status.inactive")' : 
                                                 data === 'archived' ? '@lang("products.status.archived")' : data;
                                return `<span class="${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    dom: '<"table-header"lf>rt<"table-footer"ip>'
                });
            });
        }
        
        function getCategoryValue(category) {
            const map = {
                'raw': 'raw_material',
                'packaging': 'packaging',
                'blend': 'blend',
                'final': 'final_product'
            };
            return map[category] || category;
        }
        
        function switchTab(tab) {
            // Update active tab button
            $('.tab-button').removeClass('active');
            $(`.tab-button[data-tab="${tab}"]`).addClass('active');
            
            // Update active tab content
            $('.tab-content').removeClass('active');
            $(`#products-${tab}`).addClass('active');
            
            // Redraw the DataTable if it exists
            
        }
    </script>
@endpush