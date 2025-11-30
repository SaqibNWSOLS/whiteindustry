<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div id="modal-body">
        <div class="form-group">
            <label>@lang('products.form.product_code')</label>
            <input id="p_code" name="product_code" class="form-input"
                   value="{{ old('product_code', $product->product_code) }}">
        </div>

        <div class="form-group">
            <label>@lang('products.form.name')</label>
            <input id="p_name" name="name" class="form-input"
                   value="{{ old('name', $product->name) }}">
        </div>

        <div class="form-group">
            <label>@lang('products.form.category')</label>
            <select id="p_category" name="category" class="form-select">
                <option value="">@lang('products.form.select_category')</option>
                <option value="raw_material" {{ old('category', $product->category) == 'raw_material' ? 'selected' : '' }}>@lang('products.categories.raw_material')</option>
                <option value="packaging" {{ old('category', $product->category) == 'packaging' ? 'selected' : '' }}>@lang('products.categories.packaging')</option>
                <option value="blend" {{ old('category', $product->category) == 'blend' ? 'selected' : '' }}>@lang('products.categories.blend')</option>
                <option value="final_product" {{ old('category', $product->category) == 'final_product' ? 'selected' : '' }}>@lang('products.categories.final_product')</option>
            </select>
        </div>

        {{-- Hidden Volume Field (visible if category = packaging) --}}
        <div class="form-group" id="volume_field"
             style="{{ old('category', $product->category) == 'packaging' ? '' : 'display:none;' }}">
            <label>@lang('products.form.volume')</label>
            <input type="text" name="volume" id="p_volume" class="form-input"
                   placeholder="@lang('products.form.enter_volume')"
                   value="{{ old('volume', $product->volume ?? '') }}">
        </div>

        <div class="form-group">
            <label>@lang('products.form.type')</label>
            <select id="p_type" name="product_type" class="form-select">
                <option value="raw" {{ old('product_type', $product->product_type) == 'raw' ? 'selected' : '' }}>@lang('products.form.raw')</option>
                <option value="final" {{ old('product_type', $product->product_type) == 'final' ? 'selected' : '' }}>@lang('products.form.final')</option>
            </select>
        </div>

        @php
            $units = \App\Models\Product::getUnitsByType('raw_material');
        @endphp

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
            <div class="form-group">
                <label>@lang('products.form.unit_price')</label>
                <input id="p_price" type="number" name="unit_price" step="0.01" class="form-input"
                       value="{{ old('unit_price', $product->unit_price) }}">
            </div>

            <div class="form-group">
                <label>@lang('products.form.unit')</label>
                <select name="unit_of_measure" class="form-input">
                    <option value="">@lang('products.form.select_unit')</option>
                    @foreach($units as $key => $label)
                        <option value="{{ $key }}" {{ old('unit_of_measure', $product->unit_of_measure) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>@lang('products.form.description')</label>
            <textarea id="p_description" name="description" class="form-input">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>@lang('products.form.status')</label>
            <select id="p_status" name="status" class="form-select">
                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>@lang('products.form.active')</option>
                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>@lang('products.form.inactive')</option>
               
            </select>
        </div>

        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
            <button type="submit" class="btn btn-primary">@lang('products.actions.update_product')</button>
            <button type="button" class="btn" onclick="hideModel()">@lang('products.actions.cancel')</button>
        </div>
    </div>
</form>

{{-- JS: toggle Volume field dynamically based on Category --}}
<script>
    document.getElementById('p_category').addEventListener('change', function() {
        const volumeField = document.getElementById('volume_field');
        if (this.value === 'packaging') {
            volumeField.style.display = 'block';
        } else {
            volumeField.style.display = 'none';
            document.getElementById('p_volume').value = '';
        }
    });
</script>