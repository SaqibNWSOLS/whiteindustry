<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 6px; }
        .summary { display: flex; gap: 20px; margin-bottom: 12px; }
        .card { padding: 8px; border: 1px solid #e5e5e5; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Sales Report ({{ ucfirst($period ?? 'year') }})</h1>
    <div style="font-size: 11px; color: #555; margin-bottom: 8px;">Period: {{ $start }} — {{ $end }}</div>

    <div class="summary">
        <div class="card" style="flex:1">
            <div style="font-size: 11px; color: #666;">Total Sales</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $total_sales }}</div>
        </div>
        <div class="card" style="flex:1">
            <div style="font-size: 11px; color: #666;">Total Orders</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $total_orders }}</div>
        </div>
        <div class="card" style="flex:1">
            <div style="font-size: 11px; color: #666;">Average Order Value</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $average_order_value }}</div>
        </div>
    </div>

    <h2 style="font-size:14px; margin-top:12px;">Top Customers</h2>
    <table>
        <thead>
            <tr><th>Customer</th><th>Orders</th><th>Revenue</th><th>% of Total</th></tr>
        </thead>
        <tbody>
        @foreach($top_customers as $c)
            <tr>
                <td>{{ $c['name'] }}</td>
                <td style="text-align:right">{{ $c['orders'] }}</td>
                <td style="text-align:right">{{ $c['revenue'] }}</td>
                <td style="text-align:right">{{ $c['percent'] }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2 style="font-size:14px; margin-top:12px;">Top Products</h2>
    <table>
        <thead>
            <tr><th>Product</th><th>Units</th><th>Revenue</th></tr>
        </thead>
        <tbody>
        @foreach($top_products as $p)
            <tr>
                <td>{{ $p['name'] }}</td>
                <td style="text-align:right">{{ number_format($p['units']) }}</td>
                <td style="text-align:right">{{ $p['revenue'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2 style="font-size:14px; margin-top:12px;">Category Breakdown</h2>
    <table>
        <thead>
            <tr><th>Category</th><th>Units</th><th>Revenue</th><th>% of Total</th><th>Avg Price</th><th>YoY</th></tr>
        </thead>
        <tbody>
        @foreach($category_breakdown as $cat)
            <tr>
                <td>{{ $cat['category'] }}</td>
                <td style="text-align:right">{{ $cat['units'] }}</td>
                <td style="text-align:right">{{ $cat['revenue'] }}</td>
                <td style="text-align:right">{{ $cat['percent_of_total'] }}%</td>
                <td style="text-align:right">{{ $cat['avg_price'] }}</td>
                <td style="text-align:right">{{ $cat['yoy'] !== null ? $cat['yoy'] . '%' : '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>
