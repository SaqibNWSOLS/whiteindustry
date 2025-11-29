<?php

return [
    'title' => 'Orders',
    'page_title' => 'Orders Management',
    
    'stats' => [
        'total_orders' => 'Total Orders',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'production' => 'In Production',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'all_orders' => 'All orders',
        'awaiting_confirmation' => 'Awaiting confirmation',
        'confirmed_orders' => 'Confirmed orders',
        'currently_in_production' => 'Currently in production',
        'completed_orders' => 'Completed orders',
        'cancelled_orders' => 'Cancelled orders',
    ],
    
    'list' => [
        'title' => 'Orders List',
        'create_order' => 'Create Order',
        'order_number' => 'Order #',
        'quotation_number' => 'Quotation #',
        'customer' => 'Customer',
        'order_date' => 'Order Date',
        'total_amount' => 'Total Amount',
        'status' => 'Status',
        'actions' => 'Actions',
        'not_available' => 'N/A',
    ],
    
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'production' => 'Production',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],
    
    'buttons' => [
        'create' => 'Create Order',
        'edit' => 'Edit',
        'view' => 'View',
    ],
    
    'empty' => 'No orders found.',
    'create_edit' => [
        'title' => [
            'create' => 'Create Order',
            'edit' => 'Edit Order',
            'page_create' => 'Create Order',
            'page_edit' => 'Edit Order',
        ],
        
        'steps' => [
            'basic' => 'Basic Info',
            'products' => 'Products',
            'raw_materials' => 'Raw Materials & Blend',
            'packaging' => 'Packaging',
            'calculation' => 'Calculation',
        ],
        
        'step_titles' => [
            'basic' => 'Basic Information',
            'products' => 'Add Products',
            'raw_materials' => 'Raw Materials Selection',
            'blend' => 'Blend Selection',
            'packaging' => 'Packaging Selection',
            'calculation' => 'Order Calculation',
        ],
        
        'form' => [
            'customer' => 'Customer',
            'customer_placeholder' => 'Select Customer',
            'delivery_date' => 'Delivery Date',
            'notes' => 'Notes',
            'notes_placeholder' => 'Add any additional notes...',
            'product_name' => 'Product Name',
            'product_name_placeholder' => 'Enter product name',
            'product_type' => 'Product Type',
            'quantity' => 'Quantity',
            'quantity_placeholder' => 'Enter product quantity',
            'raw_material' => 'Raw Material',
            'raw_material_placeholder' => 'Select Material',
            'percentage' => 'Percentage (%)',
            'packaging' => 'Packaging',
            'packaging_placeholder' => 'Select Packaging',
        ],
        
        'product_types' => [
            'cosmetic' => 'Cosmetic',
            'food_supplement' => 'Food Supplement',
        ],
        
        'buttons' => [
            'add_another_product' => 'Add Another Product',
            'add_another_material' => 'Add Another Material',
            'add_another_packaging' => 'Add Another Packaging',
            'remove' => 'Remove',
            'next_products' => 'Next: Add Products',
            'next_raw_materials' => 'Next: Add Raw Materials',
            'next_packaging' => 'Next: Add Packaging',
            'next_calculation' => 'Next: Calculate Order',
            'update_continue' => 'Update & Continue',
            'back' => 'Back',
            'cancel' => 'Cancel',
            'calculate_save' => 'Calculate & Save Order',
            'recalculate_update' => 'Recalculate & Update Order',
            'view_final_order' => 'View Final Order',
        ],
        
        'alerts' => [
            'total_percentage' => 'Total Percentage:',
            'remaining' => 'Remaining:',
        ],
        
        'calculation' => [
            'cost_parameters' => 'Cost Parameters',
            'manufacturing_cost' => 'Manufacturing Cost %',
            'risk_cost' => 'Risk Cost %',
            'profit_margin' => 'Profit Margin %',
            'tax_rate' => 'Tax Rate %',
            'summary' => 'Summary',
            'customer' => 'Customer',
            'number_of_products' => 'Number of Products',
            'total_raw_materials' => 'Total Raw Materials',
            'total_packaging_items' => 'Total Packaging Items',
            'current_calculation' => 'Current Calculation',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'last_updated' => 'Last Updated',
        ],
        
        'messages' => [
            'select_customer' => 'Please select a customer',
            'percentage_warning' => 'Total percentage should be 100%',
        ],
    ],
];