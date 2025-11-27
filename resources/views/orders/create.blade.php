@extends('layouts.app')
@section('title', 'Create Order')

@section('content')

<style>
    .order-wrapper {
        max-width: 1900px;
        margin: 0 auto;
        padding: 25px;
    }

    .order-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        overflow: hidden;
    }

    .order-header {
        padding: 20px 25px;
        background: #f7f9fc;
        border-bottom: 1px solid #e5e7eb;
    }

    .order-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    .order-body {
        padding: 25px;
    }

    .section {
        margin-bottom: 30px;
    }

    .section label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: #444;
    }

    .section h4 {
        font-size: 18px;
        margin-bottom: 12px;
        font-weight: 600;
        color: #222;
    }

    .product-box {
        border: 1px solid #e0e0e0;
        background: #fafafa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 12px;
    }

    .btn-success,
    .btn-secondary {
        padding: 10px 18px;
        font-size: 15px;
        border-radius: 6px !important;
        font-weight: 600;
    }

    .action-row {
        display: flex;
        gap: 12px;
    }

    @media(max-width: 768px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .action-row {
            flex-direction: column;
        }
    }
</style>


<div class="order-wrapper">
    <div class="order-card">

        <!-- Header -->
        <div class="order-header">
            <h2>Create New Order</h2>
        </div>

        <!-- Body -->
        <div class="order-body">

            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <!-- Select Quotation -->
                <div class="section">
                    <label>Select QA Approved Quotation</label>
                    <select name="qa_quotes_id" class="form-control" required onchange="loadQuotationProducts(this.value)">
                        <option value="">-- Select Quotation --</option>
                        @foreach($qaApproved as $qa)
                            <option value="{{ $qa->id }}" data-quote-id="{{ $qa->quote_id }}">
                                {{ $qa->quote->quotation_number }} – {{ $qa->quote->customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Date -->
                <div class="section">
                    <label>Order Date</label>
                    <input type="date" name="order_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Delivery Date -->
                <div class="section">
                    <label>Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-control" required>
                </div>

                <!-- Order Items -->
                <div class="section">
                    <h4>Order Items</h4>
                    <div id="items-container">
                        <p style="color:#777;">Select a quotation to load items...</p>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="section">
                    <label>Order Notes</label>
                    <textarea name="order_notes" class="form-control" rows="3" placeholder="Add any instructions or notes..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="action-row">
                    <button type="submit" class="btn btn-success">Create Order</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </form>

        </div> <!-- body -->
    </div>
</div>


<script>
function loadQuotationProducts(qaId) {
    if (!qaId) return;

    fetch(`/api/qa/${qaId}/products`)
        .then(res => res.json())
        .then(data => {
            let html = '';

            if (data.products.length === 0) {
                html = '<p>No products found for this quotation.</p>';
                document.getElementById('items-container').innerHTML = html;
                return;
            }

            data.products.forEach((product, index) => {
                html += `
                    <div class="product-box">
                        <div class="product-grid">

                            <div>
                                <strong style="font-size:15px;">${product.product_name}</strong>
                                <input type="hidden" name="items[${index}][quote_product_id]" value="${product.id}">
                            </div>

                            <div>
                                <label>Unit Price</label>
                                <div style="font-weight:bold; font-size:15px;">$${product.total_amount}</div>
                            </div>

                            <div>
                                <label>Quantity</label>
                                <input type="number" name="items[${index}][quantity]" class="form-control" value="1" min="1" required>
                            </div>

                        </div>
                    </div>
                `;
            });

            document.getElementById('items-container').innerHTML = html;
        });
}
</script>

@endsection
