@extends('layouts.app')

@section('title', isset($quote) ? 'Edit Quotation' : 'Create Quotation')
@section('page_title', isset($quote) ? 'Edit Quotation' : 'Create Quotation')

@section('content')
<link rel="stylesheet" href="{{ asset('css/quotation.css') }}">

<div class="quotation-container">
    <div class="quotation-row">
        <!-- Progress Steps -->
        <div class="progress-card">
            <div class="steps-container">
                <a href="{{ isset($quote) ? route('quotes.edit', ['quote' => $quote->id, 'step' => 'basic']) : route('quotes.create', 'basic') }}" 
                   class="step {{ $step == 'basic' ? 'active' : '' }} {{ (isset($quote) && $quote->id) ? 'completed' : '' }}">
                    <div class="step-number">1</div>
                    <div class="step-label">Basic Info</div>
                </a>
                <a href="{{ (isset($quote) && $quote->id) ? route('quotes.edit', ['quote' => $quote->id, 'step' => 'products']) : '#' }}" 
                   class="step {{ $step == 'products' ? 'active' : '' }} {{ in_array($step, ['raw_materials', 'blend', 'packaging', 'calculation']) ? 'completed' : '' }} {{ !isset($quote) ? 'disabled' : '' }}">
                    <div class="step-number">2</div>
                    <div class="step-label">Products</div>
                </a>
                <a href="{{ (isset($quote) && $quote->id) ? route('quotes.edit', ['quote' => $quote->id, 'step' => 'raw_materials']) : '#' }}" 
                   class="step {{ $step == 'raw_materials' ? 'active' : '' }} {{ in_array($step, ['blend', 'packaging', 'calculation']) ? 'completed' : '' }} {{ !isset($quote) ? 'disabled' : '' }}">
                    <div class="step-number">3</div>
                    <div class="step-label">Raw Materials & Blend</div>
                </a>
                <a href="{{ (isset($quote) && $quote->id) ? route('quotes.edit', ['quote' => $quote->id, 'step' => 'packaging']) : '#' }}" 
                   class="step {{ $step == 'packaging' ? 'active' : '' }} {{ $step == 'calculation' ? 'completed' : '' }} {{ !isset($quote) ? 'disabled' : '' }}">
                    <div class="step-number">5</div>
                    <div class="step-label">Packaging</div>
                </a>
                <a href="{{ (isset($quote) && $quote->id) ? route('quotes.edit', ['quote' => $quote->id, 'step' => 'calculation']) : '#' }}" 
                   class="step {{ $step == 'calculation' ? 'active' : '' }} {{ !isset($quote) ? 'disabled' : '' }}">
                    <div class="step-number">6</div>
                    <div class="step-label">Calculation</div>
                </a>
            </div>
        </div>

        <!-- Content Card -->
        <div class="content-card">
            <div class="card-header">
                <h2>
                    @if($step == 'basic') Basic Information
                    @elseif($step == 'products') Add Products
                    @elseif($step == 'raw_materials') Raw Materials Selection
                    @elseif($step == 'blend') Blend Selection
                    @elseif($step == 'packaging') Packaging Selection
                    @elseif($step == 'calculation') Quotation Calculation
                    @endif
                </h2>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Step 1: Basic Information -->
                @if($step == 'basic')
                <form method="POST" action="{{ isset($quote) ? route('quotes.update-basic', $quote->id) : route('quotes.store-basic') }}">
                    @csrf
                    @if(isset($quote)) @method('PUT') @endif
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Customer *</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                        {{ (old('customer_id', isset($quote) ? $quote->customer_id : '') == $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->company_name ?: $customer->contact_person }}
                                        @if($customer->company_name && $customer->contact_person)
                                            ({{ $customer->contact_person }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Add any additional notes...">{{ old('notes', isset($quote) ? $quote->notes : '') }}</textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($quote) ? 'Update & Continue' : 'Next: Add Products' }}
                        </button>
                        <a href="{{ route('quotes.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
                @endif

                <!-- Step 2: Products -->
                @if($step == 'products' && isset($quote))
                <form method="POST" action="{{ route('quotes.update-products', $quote->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="dynamic-container" id="products-container">
                        <div class="dynamic-row header-row">
                            <div class="dynamic-fields">
                                <div class="form-group">
                                    <label class="form-label">Product Name *</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Product Type *</label>
                                </div>
                            
                            </div>
                            <div style="width: 100px;"></div>
                        </div>
                        
                        @php
                            $existingProducts = $quote->products;
                            $productIndex = 0;
                        @endphp
                        
                        @foreach($existingProducts as $product)
                        <div class="dynamic-row product-row" data-product-id="{{ $product->id }}">
                            <div class="dynamic-fields">
                                <div class="form-group">
                                    <input type="text" name="products[{{ $productIndex }}][product_name]" 
                                           class="form-control" value="{{ $product->product_name }}" 
                                           placeholder="Enter product name" required>
                                    <input type="hidden" name="products[{{ $productIndex }}][id]" value="{{ $product->id }}">
                                </div>
                                <div class="form-group">
                                    <select name="products[{{ $productIndex }}][product_type]" class="form-control" required>
                                        <option value="cosmetic" {{ $product->product_type == 'cosmetic' ? 'selected' : '' }}>Cosmetic</option>
                                        <option value="food_supplement" {{ $product->product_type == 'food_supplement' ? 'selected' : '' }}>Food Supplement</option>
                                    </select>
                                </div>
                               
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger remove-product" 
                                        data-product-id="{{ $product->id }}"
                                        {{ $existingProducts->count() <= 1 ? 'disabled' : '' }}>
                                    Remove
                                </button>
                            </div>
                        </div>
                        @php $productIndex++; @endphp
                        @endforeach
                        
                        @if($existingProducts->isEmpty())
                        <div class="dynamic-row product-row">
                            <div class="dynamic-fields">
                                <div class="form-group">
                                    <input type="text" name="products[0][product_name]" class="form-control" 
                                           placeholder="Enter product name" required>
                                </div>
                                <div class="form-group">
                                    <select name="products[0][product_type]" class="form-control" required>
                                        <option value="cosmetic">Cosmetic</option>
                                        <option value="food_supplement">Food Supplement</option>
                                    </select>
                                </div>
                                
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger remove-product" disabled>Remove</button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="button" id="add-product" class="btn btn-success">Add Another Product</button>
                        <button type="submit" class="btn btn-primary">Next: Add Raw Materials</button>
                        <a href="{{ route('quotes.edit', ['quote' => $quote->id, 'step' => 'basic']) }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let productCount = {{ $existingProducts->count() ?: 1 }};
                        
                        document.getElementById('add-product').addEventListener('click', function() {
                            const container = document.getElementById('products-container');
                            const newRow = document.createElement('div');
                            newRow.className = 'dynamic-row product-row';
                            newRow.innerHTML = `
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <input type="text" name="products[${productCount}][product_name]" 
                                               class="form-control" placeholder="Enter product name" required>
                                    </div>
                                    <div class="form-group">
                                        <select name="products[${productCount}][product_type]" class="form-control" required>
                                            <option value="cosmetic">Cosmetic</option>
                                            <option value="food_supplement">Food Supplement</option>
                                        </select>
                                    </div>
                                   
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger remove-product">Remove</button>
                                </div>
                            `;
                            container.appendChild(newRow);
                            productCount++;
                            
                            // Enable all remove buttons if we have more than one product
                            if (document.querySelectorAll('.product-row').length > 1) {
                                document.querySelectorAll('.remove-product').forEach(btn => {
                                    btn.disabled = false;
                                });
                            }
                        });
                        
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-product')) {
                                const row = e.target.closest('.product-row');
                                const productId = row.getAttribute('data-product-id');
                                
                                if (productId) {
                                    // Add hidden input to mark for deletion
                                    const deleteInput = document.createElement('input');
                                    deleteInput.type = 'hidden';
                                    deleteInput.name = 'deleted_products[]';
                                    deleteInput.value = productId;
                                    row.appendChild(deleteInput);
                                }
                                
                                if (document.querySelectorAll('.product-row').length > 1) {
                                    row.style.display = 'none';
                                }
                                
                                // Disable remove buttons if only one product remains
                                if (document.querySelectorAll('.product-row').length <= 2) {
                                    document.querySelectorAll('.remove-product').forEach(btn => {
                                        btn.disabled = true;
                                    });
                                }
                            }
                        });
                    });
                </script>
                @endif

                <!-- Step 3: Raw Materials -->
                @if($step == 'raw_materials' && isset($quote))
                
                <form method="POST" action="{{ route('quotes.update-raw-materials', $quote->id) }}">
                    @csrf
                    @method('PUT')

                    @foreach($quote->products as $product)
                    <div class="product-section">
                        <div class="product-header">
                            <h3 class="product-title">{{ $product->product_name }}</h3>
                                                    </div>

                        @php
                            $existingBlend = $product->items()->where('item_type', 'blend')->first();
                        @endphp
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Blend *</label>
                                <select name="blends[{{ $product->id }}][blend_id]" class="form-control" >
                                    <option value="">Select Blend</option>
                                    @foreach($blends as $blend)
                                        <option value="{{ $blend->id }}" 
                                            {{ $existingBlend && $existingBlend->item_id == $blend->id ? 'selected' : '' }}
                                            data-price="{{ $blend->unit_price }}"
                                            data-unit="{{ $blend->unit_of_measure }}">
                                            {{ $blend->name }} (€{{ $blend->unit_price }}/{{ $blend->unit_of_measure }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($existingBlend)
                                    <input type="hidden" name="blends[{{ $product->id }}][id]" value="{{ $existingBlend->id }}">
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    @endforeach
                    
                    @foreach($quote->products as $product)
                    <div class="product-section">
                        <div class="product-header">
                            <h3 class="product-title">{{ $product->product_name }}</h3>
                            
                        </div>

                        <div class="dynamic-container raw-materials-container" data-product-id="{{ $product->id }}">
                            <div class="dynamic-row header-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <label class="form-label">Raw Material</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Percentage (%)</label>
                                    </div>
                                </div>
                                <div style="width: 100px;"></div>
                            </div>
                            
                            @php
                                $existingMaterials = $product->rawMaterialItems;
                                $materialIndex = 0;
                                $totalPercentage = $existingMaterials->sum('percentage');
                            @endphp
                            
                            @foreach($existingMaterials as $material)
                            <div class="dynamic-row material-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <select name="raw_materials[{{ $product->id }}][materials][{{ $materialIndex }}][item_id]" 
                                                class="form-control" >
                                            <option value="">Select Material</option>
                                            @foreach($rawMaterials as $materialItem)
                                                <option value="{{ $materialItem->id }}" 
                                                    {{ $material->item_id == $materialItem->id ? 'selected' : '' }}
                                                    data-price="{{ $materialItem->unit_price }}"
                                                    data-unit="{{ $materialItem->unit_of_measure }}">
                                                    {{ $materialItem->name }} (€{{ $materialItem->unit_price }}/{{ $materialItem->unit_of_measure }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="raw_materials[{{ $product->id }}][materials][{{ $materialIndex }}][id]" 
                                               value="{{ $material->id }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="raw_materials[{{ $product->id }}][materials][{{ $materialIndex }}][percentage]" 
                                               class="form-control percentage-input" 
                                               value="{{ $material->percentage }}" 
                                               step="0.01" min="0" max="100" >
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger remove-material">Remove</button>
                                </div>
                            </div>
                            @php $materialIndex++; @endphp
                            @endforeach
                            
                            @if($existingMaterials->isEmpty())
                            <div class="dynamic-row material-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <select name="raw_materials[{{ $product->id }}][materials][0][item_id]" class="form-control" required>
                                            <option value="">Select Material</option>
                                            @foreach($rawMaterials as $material)
                                                <option value="{{ $material->id }}"
                                                        data-price="{{ $material->unit_price }}"
                                                        data-unit="{{ $material->unit_of_measure }}">
                                                    {{ $material->name }} (€{{ $material->unit_price }}/{{ $material->unit_of_measure }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="raw_materials[{{ $product->id }}][materials][0][percentage]" 
                                               class="form-control percentage-input" 
                                               value="0" step="0.01" min="0" max="100" required>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger remove-material">Remove</button>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="percentage-indicator">
                            <div>
                                <strong>Total Percentage:</strong> 
                                <span class="percentage-value total" data-product-id="{{ $product->id }}">
                                    {{ number_format($totalPercentage, 2) }}
                                </span>%
                            </div>
                            <div>
                                <strong>Remaining:</strong> 
                                <span class="percentage-value remaining" data-product-id="{{ $product->id }}">
                                    {{ number_format(100 - $totalPercentage, 2) }}
                                </span>%
                            </div>
                        </div>

                        <div class="form-actions" style="justify-content: flex-start; border-top: none; padding-top: 0;">
                            <button type="button" class="btn btn-success add-material" data-product-id="{{ $product->id }}">
                                Add Another Material
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submit-materials">
                            Next: Add Packaging
                        </button>
                        <a href="{{ route('quotes.edit', ['quote' => $quote->id, 'step' => 'products']) }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Add material row for specific product
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('add-material')) {
                                const productId = e.target.getAttribute('data-product-id');
                                const container = document.querySelector(`.raw-materials-container[data-product-id="${productId}"]`);
                                const materialCount = container.querySelectorAll('.material-row').length;
                                
                                const newRow = document.createElement('div');
                                newRow.className = 'dynamic-row material-row';
                                newRow.innerHTML = `
                                    <div class="dynamic-fields">
                                        <div class="form-group">
                                            <select name="raw_materials[${productId}][materials][${materialCount}][item_id]" class="form-control" required>
                                                <option value="">Select Material</option>
                                                @foreach($rawMaterials as $material)
                                                    <option value="{{ $material->id }}" 
                                                            data-price="{{ $material->unit_price }}"
                                                            data-unit="{{ $material->unit_of_measure }}">
                                                        {{ $material->name }} (€{{ $material->unit_price }}/{{ $material->unit_of_measure }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" name="raw_materials[${productId}][materials][${materialCount}][percentage]" 
                                                   class="form-control percentage-input" value="0" step="0.01" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger remove-material">Remove</button>
                                    </div>
                                `;
                                container.appendChild(newRow);
                                
                                // Add event listener to new percentage input
                                newRow.querySelector('.percentage-input').addEventListener('input', function() {
                                    calculateTotalPercentage(productId);
                                });
                            }
                        });
                        
                        // Remove material row
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-material')) {
                                const row = e.target.closest('.material-row');
                                const container = row.closest('.raw-materials-container');
                                const productId = container.getAttribute('data-product-id');
                                
                                if (container.querySelectorAll('.material-row').length > 1) {
                                    row.remove();
                                    calculateTotalPercentage(productId);
                                }
                            }
                        });
                        
                        // Calculate total percentage for a product
                        function calculateTotalPercentage(productId) {
                            const container = document.querySelector(`.raw-materials-container[data-product-id="${productId}"]`);
                            let total = 0;
                            
                            container.querySelectorAll('.percentage-input').forEach(input => {
                                total += parseFloat(input.value) || 0;
                            });
                            
                            const totalElement = document.querySelector(`.percentage-value.total[data-product-id="${productId}"]`);
                            const remainingElement = document.querySelector(`.percentage-value.remaining[data-product-id="${productId}"]`);
                            
                            if (totalElement && remainingElement) {
                                totalElement.textContent = total.toFixed(2);
                                remainingElement.textContent = (100 - total).toFixed(2);
                                
                                // Add warning class if total is not 100%
                                if (Math.abs(total - 100) > 0.01) {
                                    totalElement.style.color = 'var(--danger)';
                                    remainingElement.style.color = 'var(--danger)';
                                } else {
                                    totalElement.style.color = 'var(--success)';
                                    remainingElement.style.color = 'var(--success)';
                                }
                            }
                        }
                        
                        // Initialize event listeners and calculations
                        document.querySelectorAll('.percentage-input').forEach(input => {
                            input.addEventListener('input', function() {
                                const container = this.closest('.raw-materials-container');
                                const productId = container.getAttribute('data-product-id');
                                calculateTotalPercentage(productId);
                            });
                        });
                        
                        // Initialize calculations on page load
                        document.querySelectorAll('.raw-materials-container').forEach(container => {
                            const productId = container.getAttribute('data-product-id');
                            calculateTotalPercentage(productId);
                        });
                    });
                </script>
                @endif

                <!-- Step 4: Blend -->
                @if($step == 'blend' && isset($quote))
                <form method="POST" action="{{ route('quotes.update-blend', $quote->id) }}">
                    @csrf
                    @method('PUT')
                    
                    @foreach($quote->products as $product)
                    <div class="product-section">
                        <div class="product-header">
                            <h3 class="product-title">{{ $product->product_name }}</h3>
                                                    </div>

                        @php
                            $existingBlend = $product->items()->where('item_type', 'blend')->first();
                        @endphp
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Blend *</label>
                                <select name="blends[{{ $product->id }}][blend_id]" class="form-control" required>
                                    <option value="">Select Blend</option>
                                    @foreach($blends as $blend)
                                        <option value="{{ $blend->id }}" 
                                            {{ $existingBlend && $existingBlend->item_id == $blend->id ? 'selected' : '' }}
                                            data-price="{{ $blend->unit_price }}"
                                            data-unit="{{ $blend->unit_of_measure }}">
                                            {{ $blend->name }} (€{{ $blend->unit_price }}/{{ $blend->unit_of_measure }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($existingBlend)
                                    <input type="hidden" name="blends[{{ $product->id }}][id]" value="{{ $existingBlend->id }}">
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    @endforeach

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Next: Add Packaging</button>
                        <a href="{{ route('quotes.edit', ['quote' => $quote->id, 'step' => 'raw_materials']) }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
                @endif

                <!-- Step 5: Packaging -->
                @if($step == 'packaging' && isset($quote))
                <form method="POST" action="{{ route('quotes.update-packaging', $quote->id) }}">
                    @csrf
                    @method('PUT')
                    
                    @foreach($quote->products as $product)
                    <div class="product-section">
                        <div class="product-header">
                            <h3 class="product-title">{{ $product->product_name }}</h3>
                        </div>

                        @php
                            $existingPackaging = $product->packagingItems;
                            $packagingIndex = 0;
                        @endphp
                        
                        <div class="dynamic-container packaging-container" data-product-id="{{ $product->id }}">
                            <div class="dynamic-row header-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <label class="form-label">Packaging</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Quantity</label>
                                    </div>
                                </div>
                                <div style="width: 100px;"></div>
                            </div>
                            
                            @foreach($existingPackaging as $packaging)
                            <div class="dynamic-row packaging-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <select name="packaging[{{ $product->id }}][packaging][{{ $packagingIndex }}][item_id]" 
                                                class="form-control" required>
                                            <option value="">Select Packaging</option>
                                            @foreach($packagingMaterials as $packagingItem)
                                                <option value="{{ $packagingItem->id }}" 
                                                    {{ $packaging->item_id == $packagingItem->id ? 'selected' : '' }}
                                                    data-price="{{ $packagingItem->unit_price }}"
                                                    data-unit="{{ $packagingItem->unit_of_measure }}">
                                                    {{ $packagingItem->name }} (€{{ $packagingItem->unit_price }}/{{ $packagingItem->unit_of_measure }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="packaging[{{ $product->id }}][packaging][{{ $packagingIndex }}][id]" 
                                               value="{{ $packaging->id }}">
                                    </div>
                                  
                                </div>
                                <div>
{{--                                     <button type="button" class="btn btn-danger remove-packaging">Remove</button>
 --}}                                </div>
                            </div>
                            @php $packagingIndex++; @endphp
                            @endforeach
                            
                            @if($existingPackaging->isEmpty())
                            <div class="dynamic-row packaging-row">
                                <div class="dynamic-fields">
                                    <div class="form-group">
                                        <select name="packaging[{{ $product->id }}][packaging][0][item_id]" class="form-control" required>
                                            <option value="">Select Packaging</option>
                                            @foreach($packagingMaterials as $packaging)
                                                <option value="{{ $packaging->id }}"
                                                        data-price="{{ $packaging->unit_price }}"
                                                        data-unit="{{ $packaging->unit_of_measure }}">
                                                    {{ $packaging->name }} (€{{ $packaging->unit_price }}/{{ $packaging->unit_of_measure }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                               {{--  <div>
                                    <button type="button" class="btn btn-danger remove-packaging">Remove</button>
                                </div> --}}
                            </div>
                            @endif
                        </div>

                       {{--  <div class="form-actions" style="justify-content: flex-start; border-top: none; padding-top: 0;">
                            <button type="button" class="btn btn-success add-packaging" data-product-id="{{ $product->id }}">
                                Add Another Packaging
                            </button>
                        </div> --}}
                    </div>
                    @endforeach

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Next: Calculate Quotation</button>
                        <a href="{{ route('quotes.edit', ['quote' => $quote->id, 'step' => 'blend']) }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Add packaging row for specific product
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('add-packaging')) {
                                const productId = e.target.getAttribute('data-product-id');
                                const container = document.querySelector(`.packaging-container[data-product-id="${productId}"]`);
                                const packagingCount = container.querySelectorAll('.packaging-row').length;
                                
                                const newRow = document.createElement('div');
                                newRow.className = 'dynamic-row packaging-row';
                                newRow.innerHTML = `
                                    <div class="dynamic-fields">
                                        <div class="form-group">
                                            <select name="packaging[${productId}][packaging][${packagingCount}][item_id]" class="form-control" required>
                                                <option value="">Select Packaging</option>
                                                @foreach($packagingMaterials as $packaging)
                                                    <option value="{{ $packaging->id }}"
                                                            data-price="{{ $packaging->unit_price }}"
                                                            data-unit="{{ $packaging->unit_of_measure }}">
                                                        {{ $packaging->name }} (€{{ $packaging->unit_price }}/{{ $packaging->unit_of_measure }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                                                           </div>
                                    <div>
                                        <button type="button" class="btn btn-danger remove-packaging">Remove</button>
                                    </div>
                                `;
                                container.appendChild(newRow);
                            }
                        });
                        
                        // Remove packaging row
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-packaging')) {
                                const row = e.target.closest('.packaging-row');
                                const container = e.target.closest('.packaging-container');
                                if (container.querySelectorAll('.packaging-row').length > 1) {
                                    row.remove();
                                }
                            }
                        });
                    });
                </script>
                @endif

                <!-- Step 6: Calculation -->
                @if($step == 'calculation' && isset($quote))
                <form method="POST" action="{{ route('quotes.calculate', $quote->id) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <h4>Cost Parameters</h4>
                            <div class="form-group">
                                <label class="form-label">Manufacturing Cost %</label>
                                <input type="number" name="manufacturing_cost_percent" class="form-control" 
                                       value="{{ old('manufacturing_cost_percent', $quote->manufacturing_cost_percent ?? 30) }}" 
                                       min="0" max="100" step="0.1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Risk Cost %</label>
                                <input type="number" name="risk_cost_percent" class="form-control" 
                                       value="{{ old('risk_cost_percent', $quote->risk_cost_percent ?? 5) }}" 
                                       min="0" max="100" step="0.1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Profit Margin %</label>
                                <input type="number" name="profit_margin_percent" class="form-control" 
                                       value="{{ old('profit_margin_percent', $quote->profit_margin_percent ?? 30) }}" 
                                       min="0" max="100" step="0.1">
                            </div>
                            <div class="form-group">
{{--                                 <label class="form-label">Tax Rate %</label>
 --}}                                <input type="hidden" name="tax_rate" class="form-control" 
                                       value="{{ old('tax_rate', $quote->tax_rate ?? 19) }}" 
                                       min="0" max="100" step="0.1">
                            </div>
                        </div>
                        <div class="form-group">
                            <h4>Summary</h4>
                            <div class="summary-grid">
                                <div class="summary-card">
                                    <p class="summary-label">Customer</p>
                                    <p class="summary-value">{{ $quote->customer->company_name ?: $quote->customer->contact_person }}</p>
                                </div>
                                <div class="summary-card">
                                    <p class="summary-label">Number of Products</p>
                                    <p class="summary-value">{{ $quote->products->count() }}</p>
                                </div>
                                <div class="summary-card">
                                    <p class="summary-label">Total Raw Materials</p>
                                    <p class="summary-value">{{ $quote->products->sum(fn($product) => $product->rawMaterialItems->count()) }}</p>
                                </div>
                                <div class="summary-card">
                                    <p class="summary-label">Total Packaging Items</p>
                                    <p class="summary-value">{{ $quote->products->sum(fn($product) => $product->packagingItems->count()) }}</p>
                                </div>
                            </div>
                            
                            @if($quote->total_price)
                            <div class="alert alert-success mt-3">
                                <h5>Current Calculation</h5>
                                <p><strong>Total Price:</strong> €{{ number_format($quote->total_price, 2) }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($quote->status) }}</p>
                                <p><strong>Last Updated:</strong> {{ $quote->updated_at->format('M j, Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            @if($quote->total_price)
                                Recalculate & Update Quotation
                            @else
                                Calculate & Save Quotation
                            @endif
                        </button>
                        <a href="{{ route('quotes.edit', ['quote' => $quote->id, 'step' => 'packaging']) }}" class="btn btn-secondary">Back</a>
                        
                        @if($quote->total_price)
                        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-outline" target="_blank">
                            View Final Quotation
                        </a>
                        @endif
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection