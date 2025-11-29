<?php

return [
    'title' => 'R&D Department',
    'page_title' => 'R&D Review',
    
    'stats' => [
        'total_quotes' => 'Total R&D Quotes',
        'all_quotes' => 'All R&D quotes',
        'pending' => 'Pending',
        'awaiting_review' => 'Awaiting review',
        'in_review' => 'In Review',
        'currently_reviewing' => 'Currently reviewing',
        'approved' => 'Approved',
        'rnd_approved' => 'R&D approved',
        'rejected' => 'Rejected',
        'rnd_rejected' => 'R&D rejected',
        'with_documents' => 'With Documents',
        'quotes_with_files' => 'Quotes with files',
    ],
    
    'table' => [
        'title' => 'R&D Quotations Pending Review',
        'quotation_number' => 'Quotation #',
        'customer' => 'Customer',
        'sent_date' => 'Sent Date',
        'status' => 'Status',
        'documents' => 'Documents',
        'actions' => 'Actions',
        'search_placeholder' => 'Search R&D quotes:',
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
        'view_quote' => 'View Quote',
        'rnd_details' => 'R&D Details',
    ],
    
    'alerts' => [
        'success' => 'Operation completed successfully',
    ],
    
    'review_page' => [
        'title' => 'R&D Review — :quotation_number',
        'quotation_details' => 'Quotation Details',
        'customer' => 'Customer',
        'total_amount' => 'Total Amount',
        'products_count' => 'Products',
        'upload_documents' => 'Upload R&D Documents',
        'select_files' => 'Select Files (PDF, DOC, Excel)',
        'max_size' => 'Max 5MB per file',
        'upload_button' => 'Upload Documents',
        'documents_uploaded' => 'Documents Uploaded (:count)',
        'no_documents' => 'No documents uploaded yet.',
        'approve_reject' => 'Approve or Reject',
        'rnd_notes_optional' => 'R&D Notes (Optional)',
        'rejection_reason' => 'Rejection Reason',
        'add_notes_placeholder' => 'Add notes...',
        'reason_placeholder' => 'Reason...',
        'approve_button' => 'Approve',
        'reject_button' => 'Reject',
        'rnd_notes' => 'R&D Notes',
        'download' => 'Download',
        'delete' => 'Delete',
    ],
    
    'confirmations' => [
        'delete_document' => 'Are you sure you want to delete this document?',
        'approve_review' => 'Approve this R&D review?',
        'reject_review' => 'Reject this R&D review?',
    ],
    
    'document_info' => [
        'uploaded_by' => 'Uploaded by :name',
        'file_size' => ':size KB',
    ],
];