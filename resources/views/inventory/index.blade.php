@extends('layouts.app')

@section('title', 'Products')
@section('page_title', 'Products')

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
                <button class="tab-button active" data-tab="raw">Raw Materials</button>
                <button class="tab-button" data-tab="packaging">Packaging</button>
                <button class="tab-button" data-tab="blend">Blend</button>
                <button class="tab-button" data-tab="final">Final Products</button>
            </div>
        </div>

        <!-- Raw Materials Tab -->
        <div id="products-raw" class="tab-content active">
            <div class="module-header">
              

                <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=raw_material" data-ajax-popup="true"  data-title="{{__('Add Raw Product')}}" class="btn btn-primary"><i class="ti ti-package"></i>Add Raw Product</a>
                <a href="{{ route('products.export', ['category' => 'raw_material']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-raw-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['raw_material']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Raw material products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Ingredients</span></h3>
                    <div id="stat-raw-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['raw_material']['active'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Premium ingredients</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-raw-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['raw_material']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Per unit</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>Raw Materials Catalog</h3>
                </div>
                <table id="products-raw-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Final Products Tab -->
        <div id="products-final" class="tab-content">
            <div class="module-header">
                 <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=final_product" data-ajax-popup="true"  data-title="{{__('Add Final Product')}}" class="btn btn-primary"><i class="ti ti-package"></i>Add Final Product</a>
               
                <a href="{{ route('products.export', ['category' => 'final_product']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-final-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['final_product']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Final products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Products</span></h3>
                    <div id="stat-final-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['final_product']['active'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Active products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-final-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['final_product']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Per unit</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>Final Products Catalog</h3>
                </div>
                <table id="products-final-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Packaging Tab -->
        <div id="products-packaging" class="tab-content">
            <div class="module-header">
            
                <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=packaging" data-ajax-popup="true"  data-title="{{__('Add Packaging Product')}}" class="btn btn-primary"><i class="ti ti-package"></i>Add Packaging Product</a>

                <a href="{{ route('products.export', ['category' => 'packaging']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-packaging-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['packaging']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Packaging products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Container Types</span></h3>
                    <div id="stat-packaging-types" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['packaging']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Different types</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-packaging-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['packaging']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Per unit</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>Packaging Products</h3>
                </div>
                <table id="products-packaging-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Blend Tab -->
        <div id="products-blend" class="tab-content">
            <div class="module-header">
               
                  <a href="#" data-size="lg" data-url="{{ route('products.create') }}?category=blend" data-ajax-popup="true"  data-title="{{__('Add Blend Product')}}" class="btn btn-primary"><i class="ti ti-package"></i>Add Blend Product</a>

                <a href="{{ route('products.export', ['category' => 'blend']) }}" class="btn btn-secondary" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Blends</span></h3>
                    <div id="stat-blend-total" style="font-size: 2rem; font-weight: bold; color: #000;">
                        {{ $stats['blend']['total'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Blend products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Blends</span></h3>
                    <div id="stat-blend-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">
                        {{ $stats['blend']['active'] ?? 0 }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Active blends</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-blend-avg" style="font-size: 2rem; font-weight: bold; color: #000;">
                        DZD {{ number_format($stats['blend']['avg_price'] ?? 0, 2) }}
                    </div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Per unit</div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <h3>Blend Products Catalog</h3>
                </div>
                <table id="products-blend-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                                return `<span class="${badgeClass}">${data}</span>`;
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