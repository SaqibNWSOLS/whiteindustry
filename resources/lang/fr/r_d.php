<?php

return [
    'title' => 'Département R&D',
    'page_title' => 'Revue R&D',
    
    'stats' => [
        'total_quotes' => 'Total des devis R&D',
        'all_quotes' => 'Tous les devis R&D',
        'pending' => 'En attente',
        'awaiting_review' => 'En attente de revue',
        'in_review' => 'En revue',
        'currently_reviewing' => 'Actuellement en revue',
        'approved' => 'Approuvé',
        'rnd_approved' => 'R&D approuvé',
        'rejected' => 'Rejeté',
        'rnd_rejected' => 'R&D rejeté',
        'with_documents' => 'Avec documents',
        'quotes_with_files' => 'Devis avec fichiers',
    ],
    
    'table' => [
        'title' => 'Devis R&D en attente de revue',
        'quotation_number' => 'Devis #',
        'customer' => 'Client',
        'sent_date' => 'Date d\'envoi',
        'status' => 'Statut',
        'documents' => 'Documents',
        'actions' => 'Actions',
        'search_placeholder' => 'Rechercher des devis R&D :',
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
        'view_quote' => 'Voir le devis',
        'rnd_details' => 'Détails R&D',
    ],
    
    'alerts' => [
        'success' => 'Opération réalisée avec succès',
    ],
    
    'review_page' => [
        'title' => 'Revue R&D — :quotation_number',
        'quotation_details' => 'Détails du devis',
        'customer' => 'Client',
        'total_amount' => 'Montant total',
        'products_count' => 'Produits',
        'upload_documents' => 'Télécharger des documents R&D',
        'select_files' => 'Sélectionner des fichiers (PDF, DOC, Excel)',
        'max_size' => 'Max 5MB par fichier',
        'upload_button' => 'Télécharger les documents',
        'documents_uploaded' => 'Documents téléchargés (:count)',
        'no_documents' => 'Aucun document téléchargé pour le moment.',
        'approve_reject' => 'Approuver ou Rejeter',
        'rnd_notes_optional' => 'Notes R&D (Optionnel)',
        'rejection_reason' => 'Raison du rejet',
        'add_notes_placeholder' => 'Ajouter des notes...',
        'reason_placeholder' => 'Raison...',
        'approve_button' => 'Approuver',
        'reject_button' => 'Rejeter',
        'rnd_notes' => 'Notes R&D',
        'download' => 'Télécharger',
        'delete' => 'Supprimer',
    ],
    
    'confirmations' => [
        'delete_document' => 'Êtes-vous sûr de vouloir supprimer ce document ?',
        'approve_review' => 'Approuver cette revue R&D ?',
        'reject_review' => 'Rejeter cette revue R&D ?',
    ],
    
    'document_info' => [
        'uploaded_by' => 'Téléchargé par :name',
        'file_size' => ':size KB',
    ],
];