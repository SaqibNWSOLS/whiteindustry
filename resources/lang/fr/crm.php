<?php

return [
    'title' => 'CRM',
    'page_title' => 'Gestion de la Relation Client',
    
    'tabs' => [
        'customers' => 'Clients',
        'quotes' => 'Devis',
    ],
    
    'actions' => [
        'add_customer' => 'Ajouter un Client',
        'export' => 'Exporter',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'confirm_delete' => 'Êtes-vous sûr?',
    ],
    
    'table' => [
        'customer_id' => 'ID Client',
        'type' => 'Type',
        'company_name' => 'Nom de l\'Entreprise',
        'contact_person' => 'Personne de Contact',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'city' => 'Ville',
        'status' => 'Statut',
        'actions' => 'Actions',
    ],
    
    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
    ],
    
    'messages' => [
        'no_customers' => 'Aucun client trouvé',
        'success' => 'Succès',
        'info' => 'Information',
    ],
    
    'types' => [
        'customer' => 'Client',
        'lead' => 'Prospect',
        'prospect' => 'Prospect Avancé',
    ],
    // Add these to your existing fr/crm.php
'create' => [
    'title' => 'Créer :type',
    'page_title' => 'Créer :type',
],
'edit' => [
    'title' => 'Modifier :type',
    'page_title' => 'Modifier :type',
],
'form' => [
    'company_name' => 'Nom de l\'Entreprise',
    'contact_person' => 'Personne de Contact',
    'email' => 'Email',
    'phone' => 'Téléphone',
    'address' => 'Adresse',
    'city' => 'Ville',
    'postal_code' => 'Code Postal',
    'industry_type' => 'Type d\'Industrie',
    'tax_id' => 'Numéro de TVA',
    'status' => 'Statut',
    'source' => 'Source',
    'estimated_value' => 'Valeur Estimée',
    'notes' => 'Notes',
    'select_industry' => 'Sélectionner une industrie',
    'select_source' => 'Sélectionner une source',
],
'industry_types' => [
    'Cosmetics & Beauty' => 'Cosmétiques & Beauté',
    'Pharmaceuticals' => 'Pharmaceutique',
    'Dietary Supplements' => 'Compléments Alimentaires',
    'Other' => 'Autre',
],
'sources' => [
    'website' => 'Site Web',
    'referral' => 'Référence',
    'trade_show' => 'Salon Professionnel',
    'cold_call' => 'Appel Froid',
    'social_media' => 'Réseaux Sociaux',
],
'lead_status' => [
    'new' => 'Nouveau',
    'contacted' => 'Contacté',
    'qualified' => 'Qualifié',
    'proposal' => 'Proposition',
    'lost' => 'Perdu',
],
'buttons' => [
    'create' => 'Créer',
    'update' => 'Mettre à jour',
    'cancel' => 'Annuler',
],
'messages' => [
    'convert_to_customer' => 'Convertir ce prospect en client',
],
];