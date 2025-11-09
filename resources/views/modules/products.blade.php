@extends('layouts.app')

@section('title', 'Products')
@section('page_title', 'Products')

@section('content')
    <div class="content">
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-category="raw_material">
                    <i class="ti ti-package"></i> Add Raw Product
                </button>
                <button class="btn btn-secondary" onclick="exportProducts('raw')" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </button>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-raw-total" style="font-size: 2rem; font-weight: bold; color: #000;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Raw material products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Ingredients</span></h3>
                    <div id="stat-raw-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Premium ingredients</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-raw-avg" style="font-size: 2rem; font-weight: bold; color: #000;">DZD 0</div>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-category="final_product">
                    <i class="ti ti-package"></i> Add Final Product
                </button>
                <button class="btn btn-secondary" onclick="exportProducts('final')" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </button>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-final-total" style="font-size: 2rem; font-weight: bold; color: #000;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Final products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Products</span></h3>
                    <div id="stat-final-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Active products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-final-avg" style="font-size: 2rem; font-weight: bold; color: #000;">DZD 0</div>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-category="packaging">
                    <i class="ti ti-package"></i> Add Packaging Product
                </button>
                <button class="btn btn-secondary" onclick="exportProducts('packaging')" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </button>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Products</span></h3>
                    <div id="stat-packaging-total" style="font-size: 2rem; font-weight: bold; color: #000;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Packaging products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Container Types</span></h3>
                    <div id="stat-packaging-types" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Different types</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-packaging-avg" style="font-size: 2rem; font-weight: bold; color: #000;">DZD 0</div>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" data-category="blend">
                    <i class="ti ti-package"></i> Add Blend
                </button>
                <button class="btn btn-secondary" onclick="exportProducts('blend')" style="margin-left:8px">
                    <i class="ti ti-download"></i> Export
                </button>
            </div>
            
            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="card">
                    <h3><span class="wi-highlight">Total Blends</span></h3>
                    <div id="stat-blend-total" style="font-size: 2rem; font-weight: bold; color: #000;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Blend products</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Active Blends</span></h3>
                    <div id="stat-blend-active" style="font-size: 1.2rem; font-weight: bold; color: #000; margin-top: 10px;">0</div>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Active blends</div>
                </div>
                <div class="card">
                    <h3><span class="wi-highlight">Avg Price</span></h3>
                    <div id="stat-blend-avg" style="font-size: 2rem; font-weight: bold; color: #000;">DZD 0</div>
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

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        @csrf
                        <input type="hidden" id="product_id" name="id">
                        <div class="form-group">
                            <label for="product_code">Product Code</label>
                            <input type="text" class="form-control" id="product_code" name="product_code" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="raw_material">Raw Material</option>
                                <option value="packaging">Packaging</option>
                                <option value="blend">Blend</option>
                                <option value="final_product">Final Product</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="unit_price">Unit Price (DZD)</label>
                            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price">
                        </div>
                        <div class="form-group">
                            <label for="unit_of_measure">Unit of Measure</label>
                            <input type="text" class="form-control" id="unit_of_measure" name="unit_of_measure">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProductBtn">Save Product</button>
                </div>
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
            
            // Modal category preset
            $('#productModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const category = button.data('category');
                if (category) {
                    $('#category').val(category);
                }
                $('#productModalLabel').text('Create New Product');
                $('#productForm')[0].reset();
                $('#product_id').val('');
            });
            
            // Save product
            $('#saveProductBtn').on('click', function() {
                saveProduct();
            });
            
            // Load initial stats
            loadAllProductStats();
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
                            render: function(data) {
                                const badgeClass = data === 'active' ? 'badge-success' : 'badge-warning';
                                return `<span class="${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: 'id',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <div style="display:inline-flex;gap:6px;white-space:nowrap;align-items:center;">
                                        <button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.6rem;" onclick="editProduct(${data})">Edit</button>
                                        <button class="btn btn-danger" style="padding: 3px 6px; font-size: 0.6rem;" onclick="deleteProduct(${data})">Delete</button>
                                    </div>
                                `;
                            }
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
            if (dataTables[tab]) {
                dataTables[tab].columns.adjust().responsive.recalc();
            }
            
            // Load stats for the active tab
            const category = getCategoryValue(tab);
            loadProductStats(category);
        }
        
        function saveProduct() {
            const formData = new FormData(document.getElementById('productForm'));
            const productId = $('#product_id').val();
            const url = productId ? `/products/${productId}` : '/products';
            const method = productId ? 'PUT' : 'POST';
            
            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#productModal').modal('hide');
                    showToaster('success', 'Product', 'Product saved successfully');
                    
                    // Refresh all DataTables
                    Object.values(dataTables).forEach(dt => dt.ajax.reload());
                    
                    // Refresh stats
                    loadAllProductStats();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save product';
                    showToaster('error', 'Product', error);
                }
            });
        }
        
        function editProduct(id) {
            $.ajax({
                url: `/products/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    $('#productModalLabel').text('Edit Product');
                    $('#product_id').val(response.id);
                    $('#product_code').val(response.product_code);
                    $('#name').val(response.name);
                    $('#category').val(response.category);
                    $('#unit_price').val(response.unit_price);
                    $('#unit_of_measure').val(response.unit_of_measure);
                    $('#description').val(response.description);
                    $('#status').val(response.status);
                    $('#productModal').modal('show');
                },
                error: function(xhr) {
                    showToaster('error', 'Product', 'Failed to load product data');
                }
            });
        }
        
        function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }
            
            $.ajax({
                url: `/products/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToaster('success', 'Product', 'Product deleted successfully');
                    
                    // Refresh all DataTables
                    Object.values(dataTables).forEach(dt => dt.ajax.reload());
                    
                    // Refresh stats
                    loadAllProductStats();
                },
                error: function(xhr) {
                    showToaster('error', 'Product', 'Failed to delete product');
                }
            });
        }
        
        async function exportProducts(tabType) {
            try {
                const category = getCategoryValue(tabType);
                const response = await fetch(`/products/export?category=${category}`);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `products-${tabType}-${new Date().toISOString().slice(0,10).replace(/-/g,'')}.csv`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                showToaster('success', 'Exported', `Exported products successfully`);
            } catch (err) {
                console.error('Export failed', err);
                showToaster('error', 'Export failed', 'Failed to export products');
            }
        }
        
        function loadProductStats(category) {
            $.ajax({
                url: '/products/statistics',
                method: 'GET',
                data: { category: category },
                success: function(response) {
                    updateStatsUI(category, response);
                },
                error: function(xhr) {
                    console.error('Failed to load stats for category:', category);
                }
            });
        }
        
        function loadAllProductStats() {
            $.ajax({
                url: '/products/statistics',
                method: 'GET',
                success: function(response) {
                    // Update stats for all categories
                    Object.keys(response).forEach(category => {
                        updateStatsUI(category, response[category]);
                    });
                },
                error: function(xhr) {
                    console.error('Failed to load stats');
                }
            });
        }
        
        function updateStatsUI(category, data) {
            const catMap = {
                'raw_material': 'raw',
                'packaging': 'packaging',
                'blend': 'blend',
                'final_product': 'final'
            };
            
            const uiCat = catMap[category] || category;
            
            $(`#stat-${uiCat}-total`).text(data.total || 0);
            
            if (data.avg_price) {
                $(`#stat-${uiCat}-avg`).text('DZD ' + Number(data.avg_price).toLocaleString());
            }
            
            // Update specific stats for each category
            switch(category) {
                case 'raw_material':
                    $('#stat-raw-active').text(data.active || 0);
                    break;
                case 'packaging':
                    $('#stat-packaging-types').text(data.types || 0);
                    break;
                case 'blend':
                    $('#stat-blend-active').text(data.active || 0);
                    break;
                case 'final_product':
                    $('#stat-final-active').text(data.active || 0);
                    break;
            }
        }
        
        
    </script>
@endpush