@extends('layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@section('content')
<div class="content">
                    <div id="reports" class="module active">
                    <div class="tabs">
                        <div class="tab-nav">
                            <button class="tab-button active" onclick="showTab('reports', 'sales', this)">Sales Reports</button>
                            <button class="tab-button" onclick="showTab('reports', 'inventory', this)">Inventory Reports</button>
                            <button class="tab-button" onclick="showTab('reports', 'financial', this)">Financial Reports</button>
                            <button class="tab-button" onclick="showTab('reports', 'production', this)">Production Reports</button>
                            <button class="tab-button" onclick="showTab('reports', 'customer', this)">Customer Analytics</button>
                        </div>
                    </div>

                    <div id="reports-sales" class="tab-content active">
                        <div class="module-header">
                            <h3>Sales Performance Reports</h3>
                            <div style="display: flex; gap: 10px;">
                                <select id="sales-period-select" class="form-select" style="width: auto;" onchange="applyReportsFilters()">
                                    <option value="year" {{ (isset($selected_period) && $selected_period === 'year') ? 'selected' : (!isset($selected_period) ? 'selected' : '') }}>This Year</option>
                                    <option value="quarter" {{ (isset($selected_period) && $selected_period === 'quarter') ? 'selected' : '' }}>This Quarter</option>
                                    <option value="month" {{ (isset($selected_period) && $selected_period === 'month') ? 'selected' : '' }}>This Month</option>
                                    <option value="30" {{ (isset($selected_period) && ($selected_period === '30' || $selected_period === '30days')) ? 'selected' : '' }}>Last 30 Days</option>
                                    {{-- <option value="custom">Custom Range</option> --}}
                                </select>
                                <button id="reports-refresh-btn" class="btn btn-secondary" onclick="refreshReports()">
                                    <i class="ti ti-refresh"></i> Refresh
                                </button>
                                <a id="reports-export-link" class="btn btn-primary" href="#" onclick="(function(){ var p = (document.getElementById('sales-period-select') && document.getElementById('sales-period-select').value) || 'year'; window.open('/reports/export?period=' + encodeURIComponent(p), '_blank'); return false; })();">
                                    <i class="ti ti-download"></i> Export PDF
                                </a>
                            </div>
                        </div>

                        <div class="dashboard-grid" style="margin-bottom: 20px;">
                            <div class="card">
                                <h3><span class="wi-highlight">Total Sales (YTD)</span></h3>
                                <div id="sales-total-sales" style="font-size: 2rem; font-weight: bold; color: #000;">{{ $initial_total_sales ?? 'Loading...' }}</div>
                                    <div id="sales-total-sales-change" style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">{!! $initial_change_text ?? '&nbsp;' !!}</div>
                                <div class="progress-bar" style="margin-top: 10px;">
                                    <div id="sales-total-sales-progress" class="progress-fill" style="width: {{ $initial_progress_percent ?? 0 }}%;"></div>
                                </div>
                                <div id="sales-total-sales-target" style="font-size: 0.65rem; color: #666; margin-top: 4px;">&nbsp;</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Average Order Value</span></h3>
                                <div id="sales-avg-order-value" style="font-size: 2rem; font-weight: bold; color: #000;">{{ $initial_average_order_value ?? 'Loading...' }}</div>
                                <div id="sales-avg-order-change" style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">&nbsp;</div>
                                <div id="sales-avg-order-peak" style="font-size: 0.65rem; color: #666; margin-top: 8px;">&nbsp;</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Total Orders</span></h3>
                                <div id="sales-total-orders" style="font-size: 2rem; font-weight: bold; color: #000;">{{ $initial_total_orders ?? 'Loading...' }}</div>
                                <div id="sales-total-orders-change" style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">&nbsp;</div>
                                <div id="sales-total-orders-avg" style="font-size: 0.65rem; color: #666; margin-top: 8px;">&nbsp;</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Conversion Rate</span></h3>
                                <div id="sales-conversion-rate" style="font-size: 2rem; font-weight: bold; color: #10b981;">{{ $initial_conversion_rate_formatted ?? '—' }}</div>
                                <div id="sales-conversion-change" style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">&nbsp;</div>
                                <div id="sales-conversion-breakdown" style="font-size: 0.65rem; color: #666; margin-top: 8px;">&nbsp;</div>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 15px;">Monthly Sales Trend</h3>
                            <div style="display: grid; grid-template-columns: repeat(9, 1fr); gap: 8px; align-items: flex-end; height: 150px; border-bottom: 2px solid #e5e5e5; padding-bottom: 10px;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 65%;" title="January: DZD 245K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Jan</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 78%;" title="February: DZD 289K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Feb</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 85%;" title="March: DZD 312K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Mar</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 72%;" title="April: DZD 268K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Apr</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 88%;" title="May: DZD 325K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">May</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 81%;" title="June: DZD 298K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Jun</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 95%;" title="July: DZD 348K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Jul</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgb(20, 54, 25); border-radius: 4px 4px 0 0; height: 100%;" title="August: DZD 365K"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Aug</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                                    <div style="width: 100%; background: rgba(20, 54, 25, 0.3); border-radius: 4px 4px 0 0; height: 90%; border: 2px dashed rgb(20, 54, 25);" title="September: DZD 330K (Projected)"></div>
                                    <span style="font-size: 0.65rem; margin-top: 5px; color: #666;">Sep*</span>
                                </div>
                            </div>
                            <div style="font-size: 0.7rem; color: #666; margin-top: 10px; text-align: center;">* Projected based on current trends</div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Top 5 Customers by Revenue</h3>
                                <div id="top-customers-list">
                                    @if(!empty($initial_top_customers) && count($initial_top_customers))
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        @foreach($initial_top_customers as $cust)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;">{{ $cust['name'] }}</div>
                                                <div style="font-size: 0.7rem; color: #666;">{{ $cust['orders'] }} orders</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; color: rgb(20, 54, 25);">{{ $cust['revenue'] }}</div>
                                                <div style="font-size: 0.7rem; color: #10b981;">{{ $cust['percent'] }}%</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Top 5 Products by Revenue</h3>
                                <div id="top-products-list">
                                    @if(!empty($initial_top_products) && count($initial_top_products))
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        @foreach($initial_top_products as $prod)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                            <div style="flex: 1;">
                                                <div style="font-weight: 600; font-size: 0.9rem;">{{ $prod['name'] }}</div>
                                                <div style="font-size: 0.7rem; color: #666;">{{ number_format($prod['units']) }} units</div>
                                            </div>
                                            <div style="width: 80px;">
                                                <div class="progress-bar" style="height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                                    <div class="progress-fill" style="width: {{ $prod['pct'] }}%; height: 8px; background: rgb(20,54,25);"></div>
                                                </div>
                                            </div>
                                            <div style="text-align: right; margin-left: 10px;">
                                                <div style="font-weight: bold;">{{ $prod['revenue'] }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Sales by Product Category - Detailed Breakdown</h3>
                                <select id="category-sort-select" class="form-select" style="width: auto;" onchange="applyReportsFilters()">
                                    <option value="revenue" {{ (isset($selected_sort) && $selected_sort === 'revenue') ? 'selected' : '' }}>Sort by Revenue</option>
                                    <option value="units" {{ (isset($selected_sort) && $selected_sort === 'units') ? 'selected' : '' }}>Sort by Units</option>
                                    <option value="growth" {{ (isset($selected_sort) && $selected_sort === 'growth') ? 'selected' : '' }}>Sort by Growth</option>
                                </select>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Units Sold</th>
                                        <th>Revenue</th>
                                        <th>% of Total</th>
                                        <th>Avg Price</th>
                                        <th>YoY Growth</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($initial_category_breakdown) && count($initial_category_breakdown))
                                        @foreach($initial_category_breakdown as $cat)
                                            <tr>
                                                <td><strong>{{ $cat['category'] }}</strong></td>
                                                <td>{{ $cat['units'] }} units</td>
                                                <td><strong>{{ $cat['revenue'] }}</strong></td>
                                                <td>{{ $cat['percent_of_total'] }}%</td>
                                                <td>{{ $cat['avg_price'] }}</td>
                                                <td>
                                                    @if($cat['yoy'] !== null)
                                                        @if($cat['yoy'] >= 0)
                                                            <span class="badge badge-success">+{{ $cat['yoy'] }}%</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ $cat['yoy'] }}%</span>
                                                        @endif
                                                    @else
                                                        &mdash;
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: 2px;">
                                                        @php $bars = 6; $fill = round(($cat['percent_of_total']/100) * $bars); @endphp
                                                        @for($i = 0; $i < $bars; $i++)
                                                            <div style="width: 4px; height: 20px; border-radius: 2px; background: {{ $i < $fill ? '#10b981' : '#e5e5e5' }};"></div>
                                                        @endfor
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <!-- Fallback static rows if no data available -->
                                        <tr>
                                            <td><strong>Face Care Products</strong></td>
                                            <td>8,945 units</td>
                                            <td><strong>DZD 1.2M</strong></td>
                                            <td>43%</td>
                                            <td>DZD 134.15</td>
                                            <td><span class="badge badge-success">+18%</span></td>
                                            <td>
                                                <div style="display: flex; gap: 2px;">
                                                    <div style="width: 4px; height: 20px; background: #e5e5e5; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplements</strong></td>
                                            <td>12,450 units</td>
                                            <td><strong>DZD 890K</strong></td>
                                            <td>32%</td>
                                            <td>DZD 71.49</td>
                                            <td><span class="badge badge-success">+12%</span></td>
                                            <td>
                                                <div style="display: flex; gap: 2px;">
                                                    <div style="width: 4px; height: 20px; background: #e5e5e5; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #e5e5e5; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Body Care Products</strong></td>
                                            <td>5,230 units</td>
                                            <td><strong>DZD 710K</strong></td>
                                            <td>25%</td>
                                            <td>DZD 135.75</td>
                                            <td><span class="badge badge-warning">-3%</span></td>
                                            <td>
                                                <div style="display: flex; gap: 2px;">
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #10b981; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #f59e0b; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #ef4444; border-radius: 2px;"></div>
                                                    <div style="width: 4px; height: 20px; background: #ef4444; border-radius: 2px;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="reports-inventory" class="tab-content">
                        <div class="module-header">
                            <h3>Inventory Analysis Reports</h3>
                            <div style="display: flex; gap: 10px;">
                                <select class="form-select" style="width: auto;">
                                    <option>All Categories</option>
                                    <option>Raw Materials</option>
                                    <option>Packaging</option>
                                    <option>Final Products</option>
                                </select>
                                <button class="btn btn-secondary" onclick="showToaster('info', 'Refreshing', 'Updating inventory data...')">
                                    <i class="ti ti-refresh"></i> Refresh
                                </button>
                                <button class="btn btn-primary" onclick="showToaster('info', 'Exporting', 'Generating PDF report...')">
                                    <i class="ti ti-download"></i> Export PDF
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-grid" style="margin-bottom: 20px;">
                            <div class="card">
                                <h3><span class="wi-highlight">Total Inventory Value</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">DZD 1.2M</div>
                                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Across all categories</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Raw: 41% | Packaging: 23% | Final: 36%</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Stock Turnover Rate</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">4.2x</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ Above industry avg (3.5x)</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Target: 4.5x annually</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Low Stock Alerts</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">12</div>
                                <div style="font-size: 0.7rem; color: #ef4444; margin-top: 4px;">Below minimum levels</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">8 critical, 4 moderate</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Inventory Accuracy</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">98.5%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">Last audit: March 15</div>
                                <div class="progress-bar" style="margin-top: 10px;">
                                    <div class="progress-fill" style="width: 98.5%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 15px;">Inventory Value by Category</h3>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid rgb(20, 54, 25);">
                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Raw Materials</div>
                                    <div style="font-size: 1.8rem; font-weight: bold; color: #000;">DZD 487K</div>
                                    <div class="progress-bar" style="margin-top: 8px;">
                                        <div class="progress-fill" style="width: 41%;"></div>
                                    </div>
                                    <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">847 items • 41% of total</div>
                                </div>
                                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Packaging Materials</div>
                                    <div style="font-size: 1.8rem; font-weight: bold; color: #000;">DZD 278K</div>
                                    <div class="progress-bar" style="margin-top: 8px;">
                                        <div class="progress-fill" style="width: 23%; background: #3b82f6;"></div>
                                    </div>
                                    <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">412 items • 23% of total</div>
                                </div>
                                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #10b981;">
                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Final Products</div>
                                    <div style="font-size: 1.8rem; font-weight: bold; color: #000;">DZD 435K</div>
                                    <div class="progress-bar" style="margin-top: 8px;">
                                        <div class="progress-fill" style="width: 36%; background: #10b981;"></div>
                                    </div>
                                    <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">289 items • 36% of total</div>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Critical Low Stock Items</h3>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="padding: 10px; background: #fff5f5; border-radius: 6px; border-left: 3px solid #ef4444;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">Vitamin C (Ascorbic Acid)</div>
                                                <div style="font-size: 0.7rem; color: #666;">MP-VIT-C-003</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; color: #ef4444;">3.2 kg</div>
                                                <div style="font-size: 0.7rem; color: #666;">Min: 5 kg</div>
                                            </div>
                                        </div>
                                        <div class="progress-bar" style="margin-top: 8px; height: 4px;">
                                            <div class="progress-fill" style="width: 64%; background: #ef4444;"></div>
                                        </div>
                                    </div>
                                    <div style="padding: 10px; background: #fff5f5; border-radius: 6px; border-left: 3px solid #ef4444;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">White PP Jar 30ml</div>
                                                <div style="font-size: 0.7rem; color: #666;">PKG-JAR-30ML</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; color: #ef4444;">2,450 pcs</div>
                                                <div style="font-size: 0.7rem; color: #666;">Min: 5,000 pcs</div>
                                            </div>
                                        </div>
                                        <div class="progress-bar" style="margin-top: 8px; height: 4px;">
                                            <div class="progress-fill" style="width: 49%; background: #ef4444;"></div>
                                        </div>
                                    </div>
                                    <div style="padding: 10px; background: #fff8e1; border-radius: 6px; border-left: 3px solid #f59e0b;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">Collagen Peptides</div>
                                                <div style="font-size: 0.7rem; color: #666;">MP-COL-005</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; color: #f59e0b;">8.5 kg</div>
                                                <div style="font-size: 0.7rem; color: #666;">Min: 10 kg</div>
                                            </div>
                                        </div>
                                        <div class="progress-bar" style="margin-top: 8px; height: 4px;">
                                            <div class="progress-fill" style="width: 85%; background: #f59e0b;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Dead Stock Analysis</h3>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="padding: 10px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">Lavender Oil - Old Batch</div>
                                                <div style="font-size: 0.7rem; color: #666;">No movement: 8 months</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold;">12.5 kg</div>
                                                <div style="font-size: 0.7rem; color: #ef4444;">DZD 15,600</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 10px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">Clear Bottles 75ml</div>
                                                <div style="font-size: 0.7rem; color: #666;">No movement: 7 months</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold;">3,200 pcs</div>
                                                <div style="font-size: 0.7rem; color: #ef4444;">DZD 38,400</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 10px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.85rem;">Hair Serum Formula</div>
                                                <div style="font-size: 0.7rem; color: #666;">No movement: 6 months</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold;">145 units</div>
                                                <div style="font-size: 0.7rem; color: #ef4444;">DZD 11,000</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; padding: 10px; background: #fff5f5; border-radius: 6px; text-align: center;">
                                    <div style="font-size: 0.75rem; color: #666;">Total Dead Stock Value</div>
                                    <div style="font-size: 1.3rem; font-weight: bold; color: #ef4444;">DZD 65,000</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Inventory Aging Analysis - Detailed</h3>
                                <select class="form-select" style="width: auto;">
                                    <option>All Items</option>
                                    <option>Raw Materials Only</option>
                                    <option>Packaging Only</option>
                                    <option>Final Products Only</option>
                                </select>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Age Range</th>
                                        <th>Items Count</th>
                                        <th>Total Value</th>
                                        <th>% of Inventory</th>
                                        <th>Avg Days in Stock</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>0-30 Days</strong></td>
                                        <td>287 items</td>
                                        <td><strong>DZD 425K</strong></td>
                                        <td>35%</td>
                                        <td>15 days</td>
                                        <td><span class="badge badge-success">Fresh</span></td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>31-90 Days</strong></td>
                                        <td>367 items</td>
                                        <td><strong>DZD 465K</strong></td>
                                        <td>39%</td>
                                        <td>58 days</td>
                                        <td><span class="badge badge-success">Good</span></td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>91-180 Days</strong></td>
                                        <td>178 items</td>
                                        <td><strong>DZD 245K</strong></td>
                                        <td>20%</td>
                                        <td>132 days</td>
                                        <td><span class="badge badge-warning">Monitor</span></td>
                                        <td><button class="btn btn-secondary" style="padding: 2px 6px; font-size: 0.65rem;">Review</button></td>
                                    </tr>
                                    <tr style="background: #fff5f5;">
                                        <td><strong>181+ Days</strong></td>
                                        <td>15 items</td>
                                        <td><strong>DZD 65K</strong></td>
                                        <td>6%</td>
                                        <td>218 days</td>
                                        <td><span class="badge badge-danger">Action Needed</span></td>
                                        <td><button class="btn btn-danger" style="padding: 2px 6px; font-size: 0.65rem;">Clear Out</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="reports-financial" class="tab-content">
                        <div class="module-header">
                            <h3>Financial Performance Reports</h3>
                            <div style="display: flex; gap: 10px;">
                                <select class="form-select" style="width: auto;">
                                    <option>Fiscal Year 2024</option>
                                    <option>Q1 2024</option>
                                    <option>Q2 2024</option>
                                    <option>Q3 2024</option>
                                </select>
                                <button class="btn btn-secondary" onclick="showToaster('info', 'Refreshing', 'Updating financial data...')">
                                    <i class="ti ti-refresh"></i> Refresh
                                </button>
                                <button class="btn btn-primary" onclick="showToaster('info', 'Exporting', 'Generating PDF report...')">
                                    <i class="ti ti-download"></i> Export PDF
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-grid" style="margin-bottom: 20px;">
                            <div class="card">
                                <h3><span class="wi-highlight">Total Revenue (YTD)</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">DZD 2.8M</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 23% vs DZD 2.27M (2023)</div>
                                <div class="progress-bar" style="margin-top: 10px;">
                                    <div class="progress-fill" style="width: 70%;"></div>
                                </div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 4px;">70% of annual target (DZD 4M)</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Gross Profit Margin</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">38%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 2% vs 36% last year</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Industry avg: 35%</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Net Profit Margin</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">12%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">DZD 336K net profit</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Target: 15% by year-end</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Cash Flow</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">+DZD 185K</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">Positive this month</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Operating: +DZD 210K</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Accounts Receivable Aging</h3>
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 15px;">
                                    <div style="text-align: center; padding: 10px; background: #e8f5e8; border-radius: 6px;">
                                        <div style="font-size: 0.7rem; color: #666;">Current</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #10b981;">DZD 45K</div>
                                        <div style="font-size: 0.65rem; color: #666;">0-30 days</div>
                                    </div>
                                    <div style="text-align: center; padding: 10px; background: #fff8e1; border-radius: 6px;">
                                        <div style="font-size: 0.7rem; color: #666;">30-60 Days</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #f59e0b;">DZD 50K</div>
                                        <div style="font-size: 0.65rem; color: #666;">8 invoices</div>
                                    </div>
                                    <div style="text-align: center; padding: 10px; background: #ffebee; border-radius: 6px;">
                                        <div style="font-size: 0.7rem; color: #666;">60-90 Days</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #ef4444;">DZD 32K</div>
                                        <div style="font-size: 0.65rem; color: #666;">7 invoices</div>
                                    </div>
                                    <div style="text-align: center; padding: 10px; background: #f5f5f5; border-radius: 6px;">
                                        <div style="font-size: 0.7rem; color: #666;">90+ Days</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #666;">DZD 0</div>
                                        <div style="font-size: 0.65rem; color: #666;">0 invoices</div>
                                    </div>
                                </div>
                                <div style="padding: 12px; background: #f9f9f9; border-radius: 6px; border-left: 3px solid rgb(20, 54, 25);">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-size: 0.75rem; color: #666;">Total Outstanding</div>
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #000;">DZD 127K</div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 0.75rem; color: #666;">Avg Collection</div>
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #000;">24 days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Operating Expenses Breakdown</h3>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.85rem;">Labor & Salaries</div>
                                            <div style="font-size: 0.7rem; color: #666;">48% of OpEx</div>
                                        </div>
                                        <div style="width: 100px;">
                                            <div class="progress-bar" style="height: 6px;">
                                                <div class="progress-fill" style="width: 48%;"></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 10px; font-weight: bold;">DZD 672K</div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.85rem;">Raw Materials</div>
                                            <div style="font-size: 0.7rem; color: #666;">28% of OpEx</div>
                                        </div>
                                        <div style="width: 100px;">
                                            <div class="progress-bar" style="height: 6px;">
                                                <div class="progress-fill" style="width: 28%;"></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 10px; font-weight: bold;">DZD 392K</div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.85rem;">Utilities & Facility</div>
                                            <div style="font-size: 0.7rem; color: #666;">12% of OpEx</div>
                                        </div>
                                        <div style="width: 100px;">
                                            <div class="progress-bar" style="height: 6px;">
                                                <div class="progress-fill" style="width: 12%;"></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 10px; font-weight: bold;">DZD 168K</div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.85rem;">Marketing & Sales</div>
                                            <div style="font-size: 0.7rem; color: #666;">7% of OpEx</div>
                                        </div>
                                        <div style="width: 100px;">
                                            <div class="progress-bar" style="height: 6px;">
                                                <div class="progress-fill" style="width: 7%;"></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 10px; font-weight: bold;">DZD 98K</div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.85rem;">Other Expenses</div>
                                            <div style="font-size: 0.7rem; color: #666;">5% of OpEx</div>
                                        </div>
                                        <div style="width: 100px;">
                                            <div class="progress-bar" style="height: 6px;">
                                                <div class="progress-fill" style="width: 5%;"></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 10px; font-weight: bold;">DZD 70K</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Monthly Financial Summary</h3>
                                <button class="btn btn-secondary" onclick="showToaster('info', 'Comparing', 'Loading comparison data...')">
                                    <i class="ti ti-trending-up"></i> Compare YoY
                                </button>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Revenue</th>
                                        <th>COGS</th>
                                        <th>Gross Profit</th>
                                        <th>Operating Expenses</th>
                                        <th>Net Profit</th>
                                        <th>Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>January</strong></td>
                                        <td>DZD 245,000</td>
                                        <td>DZD 152,000</td>
                                        <td><strong>DZD 93,000</strong></td>
                                        <td>DZD 63,000</td>
                                        <td><strong>DZD 30,000</strong></td>
                                        <td>12.2%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>February</strong></td>
                                        <td>DZD 289,000</td>
                                        <td>DZD 178,000</td>
                                        <td><strong>DZD 111,000</strong></td>
                                        <td>DZD 74,500</td>
                                        <td><strong>DZD 36,500</strong></td>
                                        <td>12.6%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>March</strong></td>
                                        <td>DZD 312,000</td>
                                        <td>DZD 187,000</td>
                                        <td><strong>DZD 125,000</strong></td>
                                        <td>DZD 81,000</td>
                                        <td><strong>DZD 44,000</strong></td>
                                        <td>14.1%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>April</strong></td>
                                        <td>DZD 268,000</td>
                                        <td>DZD 165,000</td>
                                        <td><strong>DZD 103,000</strong></td>
                                        <td>DZD 70,000</td>
                                        <td><strong>DZD 33,000</strong></td>
                                        <td>12.3%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>May</strong></td>
                                        <td>DZD 325,000</td>
                                        <td>DZD 202,000</td>
                                        <td><strong>DZD 123,000</strong></td>
                                        <td>DZD 85,000</td>
                                        <td><strong>DZD 38,000</strong></td>
                                        <td>11.7%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>June</strong></td>
                                        <td>DZD 298,000</td>
                                        <td>DZD 185,000</td>
                                        <td><strong>DZD 113,000</strong></td>
                                        <td>DZD 78,000</td>
                                        <td><strong>DZD 35,000</strong></td>
                                        <td>11.7%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>July</strong></td>
                                        <td>DZD 348,000</td>
                                        <td>DZD 215,000</td>
                                        <td><strong>DZD 133,000</strong></td>
                                        <td>DZD 91,000</td>
                                        <td><strong>DZD 42,000</strong></td>
                                        <td>12.1%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>August</strong></td>
                                        <td>DZD 365,000</td>
                                        <td>DZD 228,000</td>
                                        <td><strong>DZD 137,000</strong></td>
                                        <td>DZD 96,000</td>
                                        <td><strong>DZD 41,000</strong></td>
                                        <td>11.2%</td>
                                    </tr>
                                    <tr style="background: #f9f9f9; font-weight: bold;">
                                        <td><strong>YTD Total</strong></td>
                                        <td>DZD 2,450,000</td>
                                        <td>DZD 1,512,000</td>
                                        <td><strong>DZD 938,000</strong></td>
                                        <td>DZD 638,500</td>
                                        <td><strong>DZD 299,500</strong></td>
                                        <td>12.2%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="reports-production" class="tab-content">
                        <div class="module-header">
                            <h3>Production Efficiency Reports</h3>
                            <div style="display: flex; gap: 10px;">
                                <select class="form-select" style="width: auto;">
                                    <option>All Production Lines</option>
                                    <option>Line A - Creams</option>
                                    <option>Line B - Supplements</option>
                                    <option>Line C - Lotions</option>
                                </select>
                                <button class="btn btn-secondary" onclick="showToaster('info', 'Refreshing', 'Updating production data...')">
                                    <i class="ti ti-refresh"></i> Refresh
                                </button>
                                <button class="btn btn-primary" onclick="showToaster('info', 'Exporting', 'Generating PDF report...')">
                                    <i class="ti ti-download"></i> Export PDF
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-grid" style="margin-bottom: 20px;">
                            <div class="card">
                                <h3><span class="wi-highlight">Orders Completed</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">143</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 18% vs 121 last year</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">17.8 orders/month avg</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">On-Time Delivery Rate</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">94%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 4% vs last quarter (90%)</div>
                                <div class="progress-bar" style="margin-top: 10px;">
                                    <div class="progress-fill" style="width: 94%;"></div>
                                </div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Avg Production Time</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">4.2 days</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↓ 15% vs 4.9 days last year</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Target: 4.0 days</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Equipment Utilization</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">87%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 5% vs last quarter</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Optimal range: 80-90%</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Quality Control Performance</h3>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                                    <div style="text-align: center; padding: 15px; background: #e8f5e8; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: #666; margin-bottom: 5px;">First Pass Rate</div>
                                        <div style="font-size: 2rem; font-weight: bold; color: #10b981;">98.5%</div>
                                        <div style="font-size: 0.65rem; color: #666; margin-top: 5px;">141/143 orders</div>
                                    </div>
                                    <div style="text-align: center; padding: 15px; background: #fff8e1; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: #666; margin-bottom: 5px;">Rework Rate</div>
                                        <div style="font-size: 2rem; font-weight: bold; color: #f59e0b;">1.4%</div>
                                        <div style="font-size: 0.65rem; color: #666; margin-top: 5px;">2 orders</div>
                                    </div>
                                    <div style="text-align: center; padding: 15px; background: #ffebee; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: #666; margin-bottom: 5px;">Rejection Rate</div>
                                        <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">0.1%</div>
                                        <div style="font-size: 0.65rem; color: #666; margin-top: 5px;">0 orders</div>
                                    </div>
                                </div>
                                <div style="padding: 12px; background: #f9f9f9; border-radius: 6px;">
                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 8px;">Top QC Issues (Last 30 Days)</div>
                                    <div style="display: flex; flex-direction: column; gap: 5px;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                                            <span>Viscosity variance</span>
                                            <span style="font-weight: 600;">5 batches</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                                            <span>Color inconsistency</span>
                                            <span style="font-weight: 600;">3 batches</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                                            <span>pH level off-spec</span>
                                            <span style="font-weight: 600;">2 batches</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Production Line Status</h3>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="padding: 10px; background: #e8f5e8; border-radius: 6px; border-left: 3px solid #10b981;">
                                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 3px;">Line A - Creams</div>
                                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 600;">Operating</div>
                                        <div style="font-size: 0.65rem; color: #666;">Utilization: 92%</div>
                                    </div>
                                    <div style="padding: 10px; background: #e8f5e8; border-radius: 6px; border-left: 3px solid #10b981;">
                                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 3px;">Line B - Supplements</div>
                                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 600;">Operating</div>
                                        <div style="font-size: 0.65rem; color: #666;">Utilization: 88%</div>
                                    </div>
                                    <div style="padding: 10px; background: #fff8e1; border-radius: 6px; border-left: 3px solid #f59e0b;">
                                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 3px;">Line C - Lotions</div>
                                        <div style="font-size: 0.7rem; color: #f59e0b; font-weight: 600;">Maintenance</div>
                                        <div style="font-size: 0.65rem; color: #666;">Back online: 2 hours</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Production by Product Line - Detailed Analysis</h3>
                                <select class="form-select" style="width: auto;">
                                    <option>Sort by Volume</option>
                                    <option>Sort by Efficiency</option>
                                    <option>Sort by Quality</option>
                                </select>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product Line</th>
                                        <th>Orders</th>
                                        <th>Units Produced</th>
                                        <th>Avg Lead Time</th>
                                        <th>Defect Rate</th>
                                        <th>Efficiency</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Face Cream Base</strong></td>
                                        <td>48 orders</td>
                                        <td>24,500 L</td>
                                        <td>3.5 days</td>
                                        <td><span class="badge badge-success">0.8%</span></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 96%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">96%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Excellent</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vitamin Supplements</strong></td>
                                        <td>65 orders</td>
                                        <td>325,000 units</td>
                                        <td>5.2 days</td>
                                        <td><span class="badge badge-success">1.2%</span></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 91%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">91%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Good</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Body Lotions</strong></td>
                                        <td>30 orders</td>
                                        <td>15,000 L</td>
                                        <td>4.0 days</td>
                                        <td><span class="badge badge-success">1.5%</span></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 88%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">88%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Good</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="reports-customer" class="tab-content">
                        <div class="module-header">
                            <h3>Customer Analytics & Insights</h3>
                            <div style="display: flex; gap: 10px;">
                                <select class="form-select" style="width: auto;">
                                    <option>All Customers</option>
                                    <option>Active Only</option>
                                    <option>Top 20%</option>
                                    <option>At Risk</option>
                                </select>
                                <button class="btn btn-secondary" onclick="showToaster('info', 'Refreshing', 'Updating customer data...')">
                                    <i class="ti ti-refresh"></i> Refresh
                                </button>
                                <button class="btn btn-primary" onclick="showToaster('info', 'Exporting', 'Generating PDF report...')">
                                    <i class="ti ti-download"></i> Export PDF
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-grid" style="margin-bottom: 20px;">
                            <div class="card">
                                <h3><span class="wi-highlight">Total Customers</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">47</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 7 new this year</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Active: 42 | Inactive: 5</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Customer Retention</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">89%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 4% vs last year</div>
                                <div class="progress-bar" style="margin-top: 10px;">
                                    <div class="progress-fill" style="width: 89%;"></div>
                                </div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Avg Customer Value</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #000;">DZD 59.5K</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">↑ 15% vs DZD 51.7K</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Lifetime value (YTD)</div>
                            </div>
                            <div class="card">
                                <h3><span class="wi-highlight">Repeat Purchase Rate</span></h3>
                                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">76%</div>
                                <div style="font-size: 0.7rem; color: #10b981; margin-top: 4px;">32 repeat customers</div>
                                <div style="font-size: 0.65rem; color: #666; margin-top: 8px;">Avg: 3.2 orders/customer</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Customer Segmentation</h3>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="padding: 12px; background: #e8f5e8; border-radius: 6px; border-left: 4px solid #10b981;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;">Platinum (Top 10%)</div>
                                                <div style="font-size: 0.7rem; color: #666;">5 customers • DZD 100K+ value</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; font-size: 1.2rem; color: #10b981;">DZD 1.1M</div>
                                                <div style="font-size: 0.7rem; color: #666;">39% of revenue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 12px; background: #e3f2fd; border-radius: 6px; border-left: 4px solid #3b82f6;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;">Gold (Top 20%)</div>
                                                <div style="font-size: 0.7rem; color: #666;">9 customers • DZD 50-100K</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; font-size: 1.2rem; color: #3b82f6;">DZD 680K</div>
                                                <div style="font-size: 0.7rem; color: #666;">24% of revenue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 12px; background: #fff8e1; border-radius: 6px; border-left: 4px solid #f59e0b;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;">Silver</div>
                                                <div style="font-size: 0.7rem; color: #666;">18 customers • DZD 20-50K</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; font-size: 1.2rem; color: #f59e0b;">DZD 720K</div>
                                                <div style="font-size: 0.7rem; color: #666;">26% of revenue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 12px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid #9ca3af;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;">Bronze</div>
                                                <div style="font-size: 0.7rem; color: #666;">15 customers • Under DZD 20K</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: bold; font-size: 1.2rem; color: #666;">DZD 300K</div>
                                                <div style="font-size: 0.7rem; color: #666;">11% of revenue</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <h3 style="margin-bottom: 15px;">Customer Health Score</h3>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="padding: 10px; background: #e8f5e8; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                            <span style="font-weight: 600; font-size: 0.85rem;">Healthy</span>
                                            <span style="font-weight: bold; color: #10b981;">32 customers</span>
                                        </div>
                                        <div class="progress-bar" style="height: 6px;">
                                            <div class="progress-fill" style="width: 68%;"></div>
                                        </div>
                                        <div style="font-size: 0.7rem; color: #666; margin-top: 3px;">Regular orders, good payment history</div>
                                    </div>
                                    <div style="padding: 10px; background: #fff8e1; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                            <span style="font-weight: 600; font-size: 0.85rem;">At Risk</span>
                                            <span style="font-weight: bold; color: #f59e0b;">8 customers</span>
                                        </div>
                                        <div class="progress-bar" style="height: 6px;">
                                            <div class="progress-fill" style="width: 17%; background: #f59e0b;"></div>
                                        </div>
                                        <div style="font-size: 0.7rem; color: #666; margin-top: 3px;">Declining order frequency or late payments</div>
                                    </div>
                                    <div style="padding: 10px; background: #ffebee; border-radius: 6px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                            <span style="font-weight: 600; font-size: 0.85rem;">Churned</span>
                                            <span style="font-weight: bold; color: #ef4444;">7 customers</span>
                                        </div>
                                        <div class="progress-bar" style="height: 6px;">
                                            <div class="progress-fill" style="width: 15%; background: #ef4444;"></div>
                                        </div>
                                        <div style="font-size: 0.7rem; color: #666; margin-top: 3px;">No orders in 90+ days</div>
                                    </div>
                                </div>
                                <div style="margin-top: 15px; padding: 12px; background: #f9f9f9; border-radius: 6px;">
                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Churn Risk Actions</div>
                                    <button class="btn btn-primary" style="width: 100%; padding: 8px; font-size: 0.8rem; margin-top: 5px;" onclick="showToaster('info', 'Campaigns', 'Loading re-engagement campaigns...')">
                                        <i class="ti ti-mail"></i> Send Re-engagement Campaign
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Detailed Customer Performance</h3>
                                <select class="form-select" style="width: auto;">
                                    <option>Sort by Revenue</option>
                                    <option>Sort by Orders</option>
                                    <option>Sort by Recency</option>
                                    <option>Sort by Health Score</option>
                                </select>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Total Orders</th>
                                        <th>Total Revenue</th>
                                        <th>Avg Order Value</th>
                                        <th>Last Order</th>
                                        <th>Health Score</th>
                                        <th>Segment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>PharmaTech Industries</strong></td>
                                        <td>28 orders</td>
                                        <td><strong>DZD 425K</strong></td>
                                        <td>DZD 15,178</td>
                                        <td>3 days ago</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 95%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">95</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Platinum</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cosmetic Solutions Ltd.</strong></td>
                                        <td>34 orders</td>
                                        <td><strong>DZD 380K</strong></td>
                                        <td>DZD 11,176</td>
                                        <td>1 day ago</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 92%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">92</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Platinum</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Beauty Global Corp</strong></td>
                                        <td>19 orders</td>
                                        <td><strong>DZD 295K</strong></td>
                                        <td>DZD 15,526</td>
                                        <td>28 days ago</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 72%; background: #f59e0b;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">72</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-warning">At Risk</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Wellness Labs Inc.</strong></td>
                                        <td>22 orders</td>
                                        <td><strong>DZD 268K</strong></td>
                                        <td>DZD 12,181</td>
                                        <td>5 days ago</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 88%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">88</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-info">Gold</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Natural Care Products</strong></td>
                                        <td>15 orders</td>
                                        <td><strong>DZD 215K</strong></td>
                                        <td>DZD 14,333</td>
                                        <td>7 days ago</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <div class="progress-bar" style="width: 60px; height: 6px;">
                                                    <div class="progress-fill" style="width: 85%;"></div>
                                                </div>
                                                <span style="font-size: 0.7rem; font-weight: 600;">85</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-info">Gold</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

</div>
@endsection

@section('scripts')
<script>
    // Minimal JS for the reports view: toaster, simple tab switching and modal show/hide.
    // Removed large unused helpers and heavy form templates to keep this view lightweight.

    function showToaster(type, title, message, duration = 4000) {
        const container = document.getElementById('toaster-container');
        if (!container) return;
        const toaster = document.createElement('div');
        toaster.className = `toaster ${type}`;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        toaster.innerHTML = `
            <div class="toaster-icon">${icons[type] || ''}</div>
            <div class="toaster-content">
                <div class="toaster-title">${title}</div>
                <div class="toaster-message">${message}</div>
            </div>
            <button class="toaster-close" onclick="removeToaster(this.parentElement)">×</button>
        `;

        container.appendChild(toaster);
        // allow CSS transitions if present
        setTimeout(() => toaster.classList.add('show'), 50);
        setTimeout(() => removeToaster(toaster), duration);
    }

    function removeToaster(toaster) {
        if (!toaster || !toaster.parentElement) return;
        toaster.classList.remove('show');
        setTimeout(() => {
            if (toaster.parentElement) toaster.parentElement.removeChild(toaster);
        }, 300);
    }

    function showTab(moduleId, tabId, clickedElement) {
        document.querySelectorAll(`#${moduleId} .tab-content`).forEach(content => content.classList.remove('active'));
        const targetTab = document.getElementById(`${moduleId}-${tabId}`);
        if (targetTab) targetTab.classList.add('active');

        document.querySelectorAll(`#${moduleId} .tab-button`).forEach(button => button.classList.remove('active'));
        if (clickedElement) clickedElement.classList.add('active');
    }

    function showModal() {
        document.getElementById('modal-overlay')?.classList.add('show');
    }

    function hideModal() {
        document.getElementById('modal-overlay')?.classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // close modal when clicking backdrop
        document.getElementById('modal-overlay')?.addEventListener('click', function(e) {
            if (e.target === this) hideModal();
        });

        // Load data for the first tab (Sales) on page load
        fetchSalesReport().catch(err => {
            console.error('Failed to load sales report:', err);
        });
    });

    // Fetch sales report from backend and populate the Sales cards
    async function fetchSalesReport(period = 'year') {
        // set loading placeholders
        const totalSalesEl = document.getElementById('sales-total-sales');
        const avgOrderEl = document.getElementById('sales-avg-order-value');
        const totalOrdersEl = document.getElementById('sales-total-orders');
        const convRateEl = document.getElementById('sales-conversion-rate');

        if (totalSalesEl) totalSalesEl.textContent = 'Loading...';
        if (avgOrderEl) avgOrderEl.textContent = 'Loading...';
        if (totalOrdersEl) totalOrdersEl.textContent = 'Loading...';
        if (convRateEl) convRateEl.textContent = '—';

        try {
            const res = await fetch(`/api/reporting?period=${encodeURIComponent(period)}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                const text = await res.text();
                throw new Error(`HTTP ${res.status}: ${text}`);
            }

            const data = await res.json();

            // expected fields: total_sales, total_orders, average_order_value
            if (totalSalesEl) totalSalesEl.textContent = data.total_sales ? data.total_sales : 'N/A';
            if (avgOrderEl) avgOrderEl.textContent = data.average_order_value ? data.average_order_value : 'N/A';
            if (totalOrdersEl) totalOrdersEl.textContent = (data.total_orders !== undefined) ? String(data.total_orders) : 'N/A';

            // optional: conversion_rate if provided by endpoint
            if (convRateEl) {
                convRateEl.textContent = data.conversion_rate_formatted ? data.conversion_rate_formatted : (data.conversion_rate ? `${data.conversion_rate}%` : '—');
            }

            // populate small extras when available
            if (data.change_text && document.getElementById('sales-total-sales-change')) {
                document.getElementById('sales-total-sales-change').textContent = data.change_text;
            }

            if (data.progress_percent && document.getElementById('sales-total-sales-progress')) {
                document.getElementById('sales-total-sales-progress').style.width = `${data.progress_percent}%`;
            }

            // render top customers, products and category breakdown if present
            if (data.top_customers && document.getElementById('top-customers-list')) {
                renderTopCustomers(data.top_customers);
            }

            if (data.top_products && document.getElementById('top-products-list')) {
                renderTopProducts(data.top_products);
            }

            if (data.category_breakdown && document.getElementById('category-breakdown-tbody')) {
                // keep a copy of the last received category breakdown so the client-side
                // sorter can operate without refetching. Stored on window for simplicity.
                try {
                    window.__lastCategoryData = Array.isArray(data.category_breakdown) ? data.category_breakdown : [];
                } catch (e) {
                    window.__lastCategoryData = [];
                }
                renderCategoryTable(data.category_breakdown);
            }

        } catch (err) {
            console.error(err);
            showToaster('error', 'Load failed', 'Could not load sales report.');
            if (totalSalesEl) totalSalesEl.textContent = 'Error';
            if (avgOrderEl) avgOrderEl.textContent = 'Error';
            if (totalOrdersEl) totalOrdersEl.textContent = 'Error';
        }
    }

    function renderTopCustomers(customers) {
        const container = document.getElementById('top-customers-list');
        if (!container) return;
        if (!customers || customers.length === 0) return;
        let html = '<div style="display: flex; flex-direction: column; gap: 10px;">';
        customers.forEach(c => {
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">${escapeHtml(c.name)}</div>
                        <div style="font-size: 0.7rem; color: #666;">${c.orders} orders</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: rgb(20, 54, 25);">${escapeHtml(c.revenue)}</div>
                        <div style="font-size: 0.7rem; color: #10b981;">${c.percent ?? '0'}%</div>
                    </div>
                </div>`;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function renderTopProducts(products) {
        const container = document.getElementById('top-products-list');
        if (!container) return;
        if (!products || products.length === 0) return;
        let html = '<div style="display: flex; flex-direction: column; gap: 10px;">';
        products.forEach(p => {
            const pct = p.pct ?? Math.round((p.revenue_numeric || 0) / (products[0]?.revenue_numeric || 1) * 100);
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 0.9rem;">${escapeHtml(p.name)}</div>
                        <div style="font-size: 0.7rem; color: #666;">${Intl.NumberFormat().format(p.units)} units</div>
                    </div>
                    <div style="width: 80px;">
                        <div class="progress-bar" style="height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div class="progress-fill" style="width: ${pct}%; height: 8px; background: rgb(20,54,25);"></div>
                        </div>
                    </div>
                    <div style="text-align: right; margin-left: 10px;">
                        <div style="font-weight: bold;">${escapeHtml(p.revenue)}</div>
                    </div>
                </div>`;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function renderCategoryTable(rows) {
        const tbody = document.getElementById('category-breakdown-tbody');
        if (!tbody) return;
        if (!rows || rows.length === 0) return;
        let html = '';
        rows.forEach(cat => {
            const yoy = cat.yoy !== null && cat.yoy !== undefined ? `${cat.yoy}%` : '—';
            const badge = (cat.yoy !== null && cat.yoy >= 0) ? `<span class="badge badge-success">+${cat.yoy}%</span>` : (cat.yoy !== null ? `<span class="badge badge-warning">${cat.yoy}%</span>` : '—');
            const bars = 6;
            const fill = Math.round((cat.percent_of_total / 100) * bars);
            let barHtml = '';
            for (let i = 0; i < bars; i++) {
                barHtml += `<div style="width: 4px; height: 20px; border-radius: 2px; background: ${i < fill ? '#10b981' : '#e5e5e5'};"></div>`;
            }

            html += `
                <tr>
                    <td><strong>${escapeHtml(cat.category)}</strong></td>
                    <td>${escapeHtml(cat.units)} units</td>
                    <td><strong>${escapeHtml(cat.revenue)}</strong></td>
                    <td>${escapeHtml(String(cat.percent_of_total))}%</td>
                    <td>${escapeHtml(cat.avg_price)}</td>
                    <td>${badge}</td>
                    <td><div style="display:flex; gap:2px">${barHtml}</div></td>
                </tr>`;
        });
        tbody.innerHTML = html;
    }

    function sortCategoryTable(mode) {
        // if the API provided category_breakdown in last fetch, use it; otherwise read table rows and sort
        // We'll store lastCategoryData on window when we fetch
        const data = window.__lastCategoryData || null;
        if (!data) return;
        let copy = Array.from(data);
        if (mode === 'revenue') {
            copy.sort((a,b) => (b.revenue_numeric || 0) - (a.revenue_numeric || 0));
        } else if (mode === 'units') {
            copy.sort((a,b) => (b.units_numeric || 0) - (a.units_numeric || 0));
        } else if (mode === 'growth') {
            copy.sort((a,b) => ( (b.yoy || 0) - (a.yoy || 0) ));
        }
        renderCategoryTable(copy);
    }

    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe).replace(/[&<>"']/g, function(m) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"})[m]; });
    }

    // Refresh the report using the selected period
    function refreshReports() {
        const btn = document.getElementById('reports-refresh-btn');
        const periodSel = document.getElementById('sales-period-select');
        const period = periodSel ? periodSel.value : 'year';
        if (btn) {
            btn.disabled = true;
            btn.classList.add('loading');
        }
        showToaster('info', 'Refreshing', 'Updating report data...');
        fetchSalesReport(period).then(() => {
            if (btn) { btn.disabled = false; btn.classList.remove('loading'); }
        }).catch(() => {
            if (btn) { btn.disabled = false; btn.classList.remove('loading'); }
        });
    }

    // Apply filters (period + sort) by navigating to the reports page with query params
    function applyReportsFilters() {
        const periodSel = document.getElementById('sales-period-select');
        const sortSel = document.getElementById('category-sort-select');
        const period = periodSel ? periodSel.value : 'year';
        const sort = sortSel ? sortSel.value : '';
        let url = `/reports?period=${encodeURIComponent(period)}`;
        if (sort) url += `&sort=${encodeURIComponent(sort)}`;
        // navigate to the server-rendered page which will inject data for the selected filters
        window.location.href = url;
    }

    // Export current report as PDF by calling server endpoint and streaming the response
    async function exportReportsPDF() {
        const btn = document.getElementById('reports-export-btn');
        const periodSel = document.getElementById('sales-period-select');
        const period = periodSel ? periodSel.value : 'year';
        if (btn) btn.disabled = true;
        showToaster('info', 'Exporting', 'Generating PDF report...');
        try {
            const res = await fetch(`/reports/export?period=${encodeURIComponent(period)}`, {
                method: 'GET',
                credentials: 'same-origin'
            });
            if (!res.ok) {
                const text = await res.text();
                throw new Error(`Export failed: ${res.status} ${text}`);
            }
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            // try to extract filename from content-disposition
            let filename = `sales-report-${period}-${new Date().toISOString().slice(0,10)}.pdf`;
            const cd = res.headers.get('content-disposition');
            if (cd) {
                const m = cd.match(/filename\*=UTF-8''(.+)|filename="?([^";]+)"?/);
                if (m) filename = decodeURIComponent(m[1] || m[2]);
            }
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            showToaster('success', 'Exported', 'PDF downloaded.');
        } catch (err) {
            console.error(err);
            showToaster('error', 'Export failed', err.message || 'Could not generate PDF');
        } finally {
            if (btn) btn.disabled = false;
        }
    }
</script>

@endsection