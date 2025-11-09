<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div id="modal-body">
        <div class="form-group">
            <label>Product Code</label>
            <input id="p_code" name="product_code" class="form-input" value="">
        </div>

        <div class="form-group">
            <label>Name</label>
            <input id="p_name" name="name" class="form-input" value="">
        </div>

        <div class="form-group">
            <label>Category</label>
            <select id="p_category" name="category" class="form-select">
                <option value="">Select category</option>
                <option value="raw_material">Raw Material</option>
                <option value="packaging">Packaging</option>
                <option value="blend">Blend</option>
                <option value="final_product">Final Product</option>
            </select>
        </div>

        {{-- Hidden Volume Field (Shown only if category = packaging) --}}
        <div class="form-group" id="volume_field" style="display: none;">
            <label>Volume</label>
            <input type="text" name="volume" id="p_volume" class="form-input" placeholder="Enter volume (e.g., 500ml)">
        </div>

        @php
            $units = \App\Models\Product::getUnitsByType('raw_material');
        @endphp

        <div class="form-group">
            <label>Type</label>
            <select id="p_type" name="product_type" class="form-select">
                <option value="">Select Type</option>
                <option value="raw">Raw</option>
                <option value="final">Final</option>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <div class="form-group">
                <label>Unit Price</label>
                <input id="p_price" type="number" name="unit_price" step="0.01" class="form-input" value="">
            </div>

            <div class="form-group">
                <label>Unit</label>
                <select name="unit_of_measure" class="form-input">
                    <option value="">Select Unit</option>
                    @foreach($units as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea id="p_description" name="description" class="form-input"></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select id="p_status" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
            <button type="submit" class="btn btn-primary">Create Product</button>
            <button class="btn" type="button" onclick="hideModal()">Cancel</button>
        </div>
    </div>
</form>

{{-- JS to Show/Hide Volume based on Category --}}
<script>
    document.getElementById('p_category').addEventListener('change', function() {
        const volumeField = document.getElementById('volume_field');
        if (this.value === 'packaging') {
            volumeField.style.display = 'block';
        } else {
            volumeField.style.display = 'none';
            document.getElementById('p_volume').value = ''; // clear if hidden
        }
    });
</script>
