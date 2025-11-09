<div style="display:inline-flex;gap:6px;white-space:nowrap;align-items:center;">
    <!-- Edit Button -->
    <a href="#" data-size="lg" data-url="{{ route('products.edit', $product->id) }}"   data-ajax-popup="true"  data-title="{{__('Edit Product')}}" class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.6rem;">
        <i class="ti ti-edit"></i> Edit
    </a>



    <!-- Delete Form -->
    <form method="POST" action="{{ route('products.destroy', $product->id) }}" style="display:inline" 
          onsubmit="return confirm('Are you sure you want to delete this product?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" style="padding: 3px 6px; font-size: 0.6rem;">
            <i class="ti ti-trash"></i> Delete
        </button>
    </form>

    <!-- Status Dropdown -->
  {{--   <div class="dropdown" style="display:inline">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                data-bs-toggle="dropdown" style="padding: 3px 6px; font-size: 0.6rem;">
            <i class="ti ti-settings"></i> Status
        </button>
        <ul class="dropdown-menu">
            <li>
                <form method="POST" action="{{ route('products.status', $product->id) }}" style="display:inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="dropdown-item {{ $product->status === 'active' ? 'active' : '' }}">
                        <i class="ti ti-circle-check"></i> Active
                    </button>
                </form>
            </li>
            <li>
                <form method="POST" action="{{ route('products.status', $product->id) }}" style="display:inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="dropdown-item {{ $product->status === 'inactive' ? 'active' : '' }}">
                        <i class="ti ti-circle-x"></i> Inactive
                    </button>
                </form>
            </li>
            <li>
                <form method="POST" action="{{ route('products.status', $product->id) }}" style="display:inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="archived">
                    <button type="submit" class="dropdown-item {{ $product->status === 'archived' ? 'active' : '' }}">
                        <i class="ti ti-archive"></i> Archived
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- View Details Button -->
    <a href="{{ route('products.show', $product->id) }}" class="btn btn-info" style="padding: 3px 6px; font-size: 0.6rem;">
        <i class="ti ti-eye"></i> View
    </a> --}}
</div>