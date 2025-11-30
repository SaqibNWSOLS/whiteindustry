<?php

return [
    'title' => 'Production',
    'page_title' => 'Production Management',
    
    'stats' => [
        'total' => 'Total Production',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'quality_check' => 'Quality Check',
        'completed' => 'Completed',
        'with_invoices' => 'With Invoices',
        
        'descriptions' => [
            'total' => 'All production jobs',
            'pending' => 'Awaiting production',
            'in_progress' => 'Currently in production',
            'quality_check' => 'Under quality inspection',
            'completed' => 'Completed production',
            'with_invoices' => 'Production with invoices',
        ]
    ],
    
    'headers' => [
        'production_jobs' => 'Production Jobs',
        'create_production' => 'Create Production',
        'production_number' => 'Production #',
        'order_number' => 'Order #',
        'customer' => 'Customer',
        'start_date' => 'Start Date',
        'status' => 'Status',
        'invoices' => 'Invoices',
        'actions' => 'Actions',
    ],
    
    'status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'quality_check' => 'Quality Check',
        'completed' => 'Completed',
        'not_started' => 'Not started',
    ],
    
    'buttons' => [
        'create' => 'Create Production',
        'view' => 'View Production',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'approve_all' => 'Approve All',
        'reject_all' => 'Reject All',
        'confirm_reject' => 'Confirm Reject',
        'cancel' => 'Cancel',
        'inventory_history' => 'Inventory History',
        'start_production' => 'Start Production',
        'complete_production' => 'Complete Production',
        'update' => 'Update',
        'add_quantity' => 'Add Quantity',
        'close' => 'Close',
        'download' => 'Download',
        'back_to_productions' => 'Back to Productions',
        'back_to_production' => 'Back to Production',
    ],
    
    'messages' => [
        'no_records' => 'No production records found.',
        'pending_transactions' => 'There are :count pending transactions awaiting approval',
        'confirm_approve_all' => 'Are you sure you want to approve all pending transactions?',
        'confirm_reject_all' => 'Are you sure you want to reject all pending transactions?',
        'reject_reason_required' => 'Please provide a rejection reason',
        'transaction_approved' => 'Transaction approved successfully',
        'transaction_rejected' => 'Transaction rejected successfully',
        'all_transactions_approved' => 'All pending transactions approved successfully',
        'all_transactions_rejected' => 'All pending transactions rejected successfully',
        'approve_error' => 'Error approving transaction',
        'reject_error' => 'Error rejecting transaction',
        'approve_all_error' => 'Error approving all transactions',
        'approved_by' => 'Approved by',
        'rejected_by' => 'Rejected by',
        'no_transactions' => 'No inventory transactions found for this production.',
    ],
    
    'datatable' => [
        'search' => 'Search production jobs:',
        'show_entries' => 'Show _MENU_ entries',
    ],
    
    'details' => [
        'title' => 'Production Details',
        'production_number' => 'Production :number',
        
        'summary' => [
            'order_number' => 'Order Number',
            'customer' => 'Customer',
            'status' => 'Status',
            'progress' => 'Progress',
        ],
        
        'dates' => [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ],
        
        'notes' => [
            'title' => 'Notes',
        ],
        
        'items' => [
            'title' => 'Production Items',
            'product_name' => 'Product Name',
            'type' => 'Type',
            'planned_quantity' => 'Planned Quantity',
            'produced_quantity' => 'Produced Quantity',
            'progress' => 'Progress',
            'status' => 'Status',
            'action' => 'Action',
        ],
        
        'invoices' => [
            'title' => 'Related Invoices',
            'invoice_number' => 'Invoice Number',
            'amount' => 'Amount',
            'status' => 'Status',
            'date' => 'Date',
        ],
        
        'documents' => [
            'rnd_title' => 'R&D Documents Review',
            'qa_title' => 'Quality And Control Documents Review',
            'no_rnd_documents' => 'No R&D documents found.',
            'no_qa_documents' => 'No QA documents found.',
        ],
        
        'modal' => [
            'title' => 'Add Ready Quantity - :product',
            'reject_title' => 'Reject Transaction',
            'reject_reason' => 'Rejection Reason',
            'reject_placeholder' => 'Please provide a reason for rejecting this transaction...',
            'current_status' => 'Current Status',
            'produced' => 'Produced',
            'planned' => 'Planned',
            'remaining' => 'Remaining',
            'quantity_to_add' => 'Quantity to Add',
            'quantity_placeholder' => 'Enter quantity to add',
            'max_available' => 'Max available: :quantity',
            'notes' => 'Notes (Optional)',
            'notes_placeholder' => 'Add any notes...',
        ],
        
        'confirmations' => [
            'start_production' => 'Start production?',
            'complete_production' => 'Complete production?',
        ],
    ],
    
    'dates' => [
        'not_started' => 'Not started',
        'not_completed' => 'Not completed',
    ],
    
    'invoice_status' => [
        'paid' => 'Paid',
        'pending' => 'Pending',
        'overdue' => 'Overdue',
    ],
    
    'inventory_history' => [
        'title' => 'Production Inventory History',
        'header' => 'Inventory Transaction History - :number',
        'summary_title' => 'Transaction Summary',
        
        'summary' => [
            'production_number' => 'Production Number',
            'order_number' => 'Order Number',
            'customer' => 'Customer',
            'total_transactions' => 'Total Transactions',
            'total_added' => 'Total Added to Stock',
            'unique_products' => 'Unique Products',
            'completed' => 'Completed',
            'pending' => 'Pending',
            'total_removed' => 'Total Removed',
        ],
        
        'table' => [
            'date' => 'Date',
            'product' => 'Product',
            'production_item' => 'Production Item',
            'transaction_type' => 'Transaction Type',
            'quantity_change' => 'Quantity Change',
            'stock_after' => 'Stock After',
            'created_by' => 'Created By',
            'notes' => 'Notes',
            'status' => 'Status',
            'product_code' => 'Code',
            'not_available' => 'N/A',
            'planned' => 'Planned',
            'produced' => 'Produced',
            'system' => 'System',
            'notes_title' => 'Transaction Notes',
            'view_notes' => 'View Notes',
            'no_notes' => 'No notes',
            'actions' => 'Actions',
            'no_actions' => 'No actions available',
        ],
        
        'transaction_types' => [
            'production_output' => 'Production Output',
            'material_consumption' => 'Material Consumption',
            'quality_check' => 'Quality Check',
            'waste' => 'Waste',
            'adjustment' => 'Adjustment',
        ],
        
        'status' => [
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
        ],
        
        'messages' => [
            'pending_transactions' => 'There are :count pending transactions awaiting approval',
            'confirm_approve_all' => 'Are you sure you want to approve all pending transactions?',
            'confirm_reject_all' => 'Are you sure you want to reject all pending transactions?',
            'reject_reason_required' => 'Please provide a rejection reason',
            'transaction_approved' => 'Transaction approved successfully',
            'transaction_rejected' => 'Transaction rejected successfully',
            'all_transactions_approved' => 'All pending transactions approved successfully',
            'all_transactions_rejected' => 'All pending transactions rejected successfully',
            'approve_error' => 'Error approving transaction',
            'reject_error' => 'Error rejecting transaction',
            'approve_all_error' => 'Error approving all transactions',
            'approved_by' => 'Approved by',
            'rejected_by' => 'Rejected by',
            'no_transactions' => 'No inventory transactions found for this production.',
        ],
    ],
    'details' => [
        'title' => 'Production Details',
        'production_number' => 'Production :number',
        
        'summary' => [
            'order_number' => 'Order Number',
            'customer' => 'Customer',
            'status' => 'Status',
            'progress' => 'Progress',
        ],
        
        'dates' => [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ],
        
        'notes' => [
            'title' => 'Notes',
        ],
        
        'items' => [
            'title' => 'Production Items',
            'product_name' => 'Product Name',
            'type' => 'Type',
            'planned_quantity' => 'Planned Quantity',
            'produced_quantity' => 'Produced Quantity',
            'progress' => 'Progress',
            'status' => 'Status',
            'action' => 'Action',
        ],
        
        'invoices' => [
            'title' => 'Related Invoices',
            'invoice_number' => 'Invoice Number',
            'amount' => 'Amount',
            'status' => 'Status',
            'date' => 'Date',
        ],
        
        'documents' => [
            'rnd_title' => 'R&D Documents Review',
            'qa_title' => 'Quality And Control Documents Review',
            'no_rnd_documents' => 'No R&D documents found.',
            'no_qa_documents' => 'No QA documents found.',
        ],
        
        'buttons' => [
            'inventory_history' => 'Inventory History',
            'start_production' => 'Start Production',
            'complete_production' => 'Complete Production',
            'update' => 'Update',
            'add_quantity' => 'Add Quantity',
            'close' => 'Close',
            'download' => 'Download',
            'back_to_productions' => 'Back to Productions',
        ],
        
        'modal' => [
            'title' => 'Add Ready Quantity - :product',
            'current_status' => 'Current Status',
            'produced' => 'Produced',
            'planned' => 'Planned',
            'remaining' => 'Remaining',
            'quantity_to_add' => 'Quantity to Add',
            'quantity_placeholder' => 'Enter quantity to add',
            'max_available' => 'Max available: :quantity',
            'notes' => 'Notes (Optional)',
            'notes_placeholder' => 'Add any notes...',
        ],
        
        'confirmations' => [
            'start_production' => 'Start production?',
            'complete_production' => 'Complete production?',
        ],
    ],
];