<?php

return [
    'title' => 'QA Department',
    'page_title' => 'QA Review',
    
    'stats' => [
        'total_quotes' => 'Total QA Quotes',
        'all_quotes' => 'All QA quotes',
        'pending' => 'Pending',
        'awaiting_review' => 'Awaiting QA review',
        'in_review' => 'In Review',
        'currently_reviewing' => 'Currently in QA',
        'approved' => 'Approved',
        'qa_approved' => 'QA approved',
        'rejected' => 'Rejected',
        'qa_rejected' => 'QA rejected',
        'with_documents' => 'With Documents',
        'quotes_with_files' => 'Quotes with QA files',
    ],
    
    'table' => [
        'title' => 'QA Quotations Pending Review',
        'quotation_number' => 'Quotation #',
        'customer' => 'Customer',
        'sent_date' => 'Sent Date',
        'status' => 'Status',
        'documents' => 'Documents',
        'actions' => 'Actions',
        'search_placeholder' => 'Search QA quotes:',
        'show_entries' => 'Show _MENU_ entries',
        'no_files' => 'No files',
        'files_count' => ':count files',
    ],
    
    'status' => [
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_review' => 'In Review',
        'pending' => 'Pending',
    ],
    
    'actions' => [
        'view_order' => 'View Order',
        'qa_review' => 'QA Review',
    ],
    
    'alerts' => [
        'success' => 'Operation completed successfully',
    ],
    
    'review_page' => [
        'title' => 'QA Review — :order_number',
        'quotation_details' => 'Quotation Details',
        'customer' => 'Customer',
        'total_amount' => 'Total Amount',
        'rnd_status' => 'R&D Status',
        'rnd_documents_review' => 'R&D Documents Review',
        'no_rnd_documents' => 'No R&D documents found.',
        'upload_documents' => 'Upload QA Documents',
        'select_files' => 'Select QA Files',
        'upload_button' => 'Upload Documents',
        'documents_uploaded' => 'QA Documents Uploaded (:count)',
        'no_documents' => 'No QA documents uploaded yet.',
        'approve_reject' => 'Approve or Reject QA',
        'qa_notes_optional' => 'QA Notes (Optional)',
        'rejection_reason' => 'Rejection Reason',
        'approve_button' => 'Approve',
        'reject_button' => 'Reject',
        'download' => 'Download',
        'delete' => 'Delete',
    ],
    
    'confirmations' => [
        'delete_document' => 'Are you sure you want to delete this document?',
        'approve_qa' => 'Approve QA?',
        'reject_qa' => 'Reject QA?',
    ],
    
    'document_info' => [
        'file_size' => ':size KB',
    ],
];