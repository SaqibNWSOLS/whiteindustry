<?php

return [
    'title' => 'CRM',
    'page_title' => 'Customer Relationship Management',
    
    'tabs' => [
        'customers' => 'Customers',
        'quotes' => 'Quotes',
    ],
    
    'actions' => [
        'add_customer' => 'Add Customer',
        'export' => 'Export',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure?',
    ],
    
    'table' => [
        'customer_id' => 'Customer ID',
        'type' => 'Type',
        'company_name' => 'Company Name',
        'contact_person' => 'Contact Person',
        'email' => 'Email',
        'phone' => 'Phone',
        'city' => 'City',
        'status' => 'Status',
        'actions' => 'Actions',
    ],
    
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    
    'messages' => [
        'no_customers' => 'No customers found',
        'success' => 'Success',
        'info' => 'Information',
    ],
    
    'types' => [
        'customer' => 'Customer',
        'lead' => 'Lead',
        'prospect' => 'Prospect',
    ],
    // Add these to your existing en/crm.php
'create' => [
    'title' => 'Create :type',
    'page_title' => 'Create :type',
],
'edit' => [
    'title' => 'Edit :type',
    'page_title' => 'Edit :type',
],
'form' => [
    'company_name' => 'Company Name',
    'contact_person' => 'Contact Person',
    'email' => 'Email',
    'phone' => 'Phone',
    'address' => 'Address',
    'city' => 'City',
    'postal_code' => 'Postal Code',
    'industry_type' => 'Industry Type',
    'tax_id' => 'Tax ID',
    'status' => 'Status',
    'source' => 'Source',
    'estimated_value' => 'Estimated Value',
    'notes' => 'Notes',
    'select_industry' => 'Select industry',
    'select_source' => 'Select source',
],
'industry_types' => [
    'Cosmetics & Beauty' => 'Cosmetics & Beauty',
    'Pharmaceuticals' => 'Pharmaceuticals',
    'Dietary Supplements' => 'Dietary Supplements',
    'Other' => 'Other',
],
'sources' => [
    'website' => 'Website',
    'referral' => 'Referral',
    'trade_show' => 'Trade Show',
    'cold_call' => 'Cold Call',
    'social_media' => 'Social Media',
],
'lead_status' => [
    'new' => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'proposal' => 'Proposal',
    'lost' => 'Lost',
],
'buttons' => [
    'create' => 'Create',
    'update' => 'Update',
    'cancel' => 'Cancel',
],
'messages' => [
    'convert_to_customer' => 'Convert this lead to a customer',
],
];