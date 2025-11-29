<?php

return [
    'title' => 'Département Contrôle Qualité',
    'page_title' => 'Revue QA',
    
    'stats' => [
        'total_quotes' => 'Total des devis QA',
        'all_quotes' => 'Tous les devis QA',
        'pending' => 'En attente',
        'awaiting_review' => 'En attente de revue QA',
        'in_review' => 'En revue',
        'currently_reviewing' => 'Actuellement en QA',
        'approved' => 'Approuvé',
        'qa_approved' => 'QA approuvé',
        'rejected' => 'Rejeté',
        'qa_rejected' => 'QA rejeté',
        'with_documents' => 'Avec documents',
        'quotes_with_files' => 'Devis avec fichiers QA',
    ],
    
    'table' => [
        'title' => 'Devis QA en attente de revue',
        'quotation_number' => 'Devis #',
        'customer' => 'Client',
        'sent_date' => 'Date d\'envoi',
        'status' => 'Statut',
        'documents' => 'Documents',
        'actions' => 'Actions',
        'search_placeholder' => 'Rechercher des devis QA :',
        'show_entries' => 'Afficher _MENU_ entrées',
        'no_files' => 'Aucun fichier',
        'files_count' => ':count fichiers',
    ],
    
    'status' => [
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'in_review' => 'En revue',
        'pending' => 'En attente',
    ],
    
    'actions' => [
        'view_order' => 'Voir la commande',
        'qa_review' => 'Revue QA',
    ],
    
    'alerts' => [
        'success' => 'Opération réalisée avec succès',
    ],
    
    'review_page' => [
        'title' => 'Revue QA — :order_number',
        'quotation_details' => 'Détails du devis',
        'customer' => 'Client',
        'total_amount' => 'Montant total',
        'rnd_status' => 'Statut R&D',
        'rnd_documents_review' => 'Revue des documents R&D',
        'no_rnd_documents' => 'Aucun document R&D trouvé.',
        'upload_documents' => 'Télécharger des documents QA',
        'select_files' => 'Sélectionner des fichiers QA',
        'upload_button' => 'Télécharger les documents',
        'documents_uploaded' => 'Documents QA téléchargés (:count)',
        'no_documents' => 'Aucun document QA téléchargé pour le moment.',
        'approve_reject' => 'Approuver ou Rejeter QA',
        'qa_notes_optional' => 'Notes QA (Optionnel)',
        'rejection_reason' => 'Raison du rejet',
        'approve_button' => 'Approuver',
        'reject_button' => 'Rejeter',
        'download' => 'Télécharger',
        'delete' => 'Supprimer',
    ],
    
    'confirmations' => [
        'delete_document' => 'Êtes-vous sûr de vouloir supprimer ce document ?',
        'approve_qa' => 'Approuver QA ?',
        'reject_qa' => 'Rejeter QA ?',
    ],
    
    'document_info' => [
        'file_size' => ':size KB',
    ],
];