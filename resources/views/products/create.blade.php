<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div id="modal-body">
        <div class="form-group">
            <label>@lang('products.form.product_code')</label>
            <input id="p_code" name="product_code" class="form-input" value="">
        </div>

        <div class="form-group">
            <label>@lang('products.form.name')</label>
            <input id="p_name" name="name" class="form-input" value="">
        </div>

        <div class="form-group">
            <label>@lang('products.form.category')</label>
            <select id="p_category" name="category" class="form-select">
                <option value="">@lang('products.form.select_category')</option>
                <option value="raw_material">@lang('products.categories.raw_material')</option>
                <option value="packaging">@lang('products.categories.packaging')</option>
                <option value="blend">@lang('products.categories.blend')</option>
                <option value="final_product">@lang('products.categories.final_product')</option>
            </select>
        </div>

        {{-- Hidden Volume Field (Shown only if category = packaging) --}}
        <div class="form-group" id="volume_field" style="display: none;">
            <label>@lang('products.form.volume')</label>
            <input type="text" name="volume" id="p_volume" class="form-input" placeholder="@lang('products.form.enter_volume')">
        </div>

        @php
            $units = \App\Models\Product::getUnitsByType('raw_material');
        @endphp

        <div class="form-group">
            <label>@lang('products.form.type')</label>
            <select id="p_type" name="product_type" class="form-select">
                <option value="">@lang('products.form.select_type')</option>
                <option value="raw">@lang('products.form.raw')</option>
                <option value="final">@lang('products.form.final')</option>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <div class="form-group">
                <label>@lang('products.form.unit_price')</label>
                <input id="p_price" type="number" name="unit_price" step="0.01" class="form-input" value="">
            </div>

            <div class="form-group">
                <label>@lang('products.form.unit')</label>
                <select name="unit_of_measure" class="form-input">
                    <option value="">@lang('products.form.select_unit')</option>
                    @foreach($units as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>@lang('products.form.description')</label>
            <textarea id="p_description" name="description" class="form-input"></textarea>
        </div>

        <div class="form-group">
            <label>@lang('products.form.status')</label>
            <select id="p_status" name="status" class="form-select">
                <option value="active">@lang('products.form.active')</option>
                <option value="inactive">@lang('products.form.inactive')</option>
            </select>
        </div>

        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
            <button type="submit" class="btn btn-primary">@lang('products.actions.create_product')</button>
            <button class="btn" type="button" onclick="hideModal()">@lang('products.actions.cancel')</button>
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