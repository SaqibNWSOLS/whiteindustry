<?php

return [
    'title' => 'Production',
    'page_title' => 'Gestion de la Production',
    
    'stats' => [
        'total' => 'Production Totale',
        'pending' => 'En Attente',
        'in_progress' => 'En Cours',
        'quality_check' => 'Contrôle Qualité',
        'completed' => 'Terminée',
        'with_invoices' => 'Avec Factures',
        
        'descriptions' => [
            'total' => 'Tous les travaux de production',
            'pending' => 'En attente de production',
            'in_progress' => 'Actuellement en production',
            'quality_check' => 'En inspection qualité',
            'completed' => 'Production terminée',
            'with_invoices' => 'Production avec factures',
        ]
    ],
    
    'headers' => [
        'production_jobs' => 'Travaux de Production',
        'create_production' => 'Créer une Production',
        'production_number' => 'N° Production',
        'order_number' => 'N° Commande',
        'customer' => 'Client',
        'start_date' => 'Date de Début',
        'status' => 'Statut',
        'invoices' => 'Factures',
        'actions' => 'Actions',
    ],
    
    'status' => [
        'pending' => 'En Attente',
        'in_progress' => 'En Cours',
        'quality_check' => 'Contrôle Qualité',
        'completed' => 'Terminée',
        'not_started' => 'Non commencée',
    ],
    
    'buttons' => [
        'create' => 'Créer Production',
        'view' => 'Voir Production',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'approve_all' => 'Tout Approuver',
        'reject_all' => 'Tout Rejeter',
        'confirm_reject' => 'Confirmer Rejet',
        'cancel' => 'Annuler',
        'inventory_history' => 'Historique Stock',
        'start_production' => 'Démarrer Production',
        'complete_production' => 'Terminer Production',
        'update' => 'Mettre à Jour',
        'add_quantity' => 'Ajouter Quantité',
        'close' => 'Fermer',
        'download' => 'Télécharger',
        'back_to_productions' => 'Retour aux Productions',
        'back_to_production' => 'Retour à la Production',
    ],
    
    'messages' => [
        'no_records' => 'Aucun enregistrement de production trouvé.',
        'pending_transactions' => 'Il y a :count transactions en attente d\'approbation',
        'confirm_approve_all' => 'Êtes-vous sûr de vouloir approuver toutes les transactions en attente ?',
        'confirm_reject_all' => 'Êtes-vous sûr de vouloir rejeter toutes les transactions en attente ?',
        'reject_reason_required' => 'Veuillez fournir un motif de rejet',
        'transaction_approved' => 'Transaction approuvée avec succès',
        'transaction_rejected' => 'Transaction rejetée avec succès',
        'all_transactions_approved' => 'Toutes les transactions en attente approuvées avec succès',
        'all_transactions_rejected' => 'Toutes les transactions en attente rejetées avec succès',
        'approve_error' => 'Erreur lors de l\'approbation de la transaction',
        'reject_error' => 'Erreur lors du rejet de la transaction',
        'approve_all_error' => 'Erreur lors de l\'approbation de toutes les transactions',
        'approved_by' => 'Approuvé par',
        'rejected_by' => 'Rejeté par',
        'no_transactions' => 'Aucune transaction d\'inventaire trouvée pour cette production.',
    ],
    
    'datatable' => [
        'search' => 'Rechercher travaux de production :',
        'show_entries' => 'Afficher _MENU_ entrées',
    ],
    
    'details' => [
        'title' => 'Détails de Production',
        'production_number' => 'Production :number',
        
        'summary' => [
            'order_number' => 'Numéro de Commande',
            'customer' => 'Client',
            'status' => 'Statut',
            'progress' => 'Progression',
        ],
        
        'dates' => [
            'start_date' => 'Date de Début',
            'end_date' => 'Date de Fin',
        ],
        
        'notes' => [
            'title' => 'Notes',
        ],
        
        'items' => [
            'title' => 'Articles en Production',
            'product_name' => 'Nom du Produit',
            'type' => 'Type',
            'planned_quantity' => 'Quantité Planifiée',
            'produced_quantity' => 'Quantité Produite',
            'progress' => 'Progression',
            'status' => 'Statut',
            'action' => 'Action',
        ],
        
        'invoices' => [
            'title' => 'Factures Associées',
            'invoice_number' => 'Numéro de Facture',
            'amount' => 'Montant',
            'status' => 'Statut',
            'date' => 'Date',
        ],
        
        'documents' => [
            'rnd_title' => 'Revue des Documents R&D',
            'qa_title' => 'Revue des Documents Qualité et Contrôle',
            'no_rnd_documents' => 'Aucun document R&D trouvé.',
            'no_qa_documents' => 'Aucun document QA trouvé.',
        ],
        
        'modal' => [
            'title' => 'Ajouter Quantité Prête - :product',
            'reject_title' => 'Rejeter Transaction',
            'reject_reason' => 'Motif de Rejet',
            'reject_placeholder' => 'Veuillez fournir un motif pour rejeter cette transaction...',
            'current_status' => 'Statut Actuel',
            'produced' => 'Produit',
            'planned' => 'Planifié',
            'remaining' => 'Restant',
            'quantity_to_add' => 'Quantité à Ajouter',
            'quantity_placeholder' => 'Entrez la quantité à ajouter',
            'max_available' => 'Maximum disponible : :quantity',
            'notes' => 'Notes (Optionnel)',
            'notes_placeholder' => 'Ajoutez des notes...',
        ],
        
        'confirmations' => [
            'start_production' => 'Démarrer la production ?',
            'complete_production' => 'Terminer la production ?',
        ],
    ],
    
    'dates' => [
        'not_started' => 'Non commencée',
        'not_completed' => 'Non terminée',
    ],
    
    'invoice_status' => [
        'paid' => 'Payée',
        'pending' => 'En Attente',
        'overdue' => 'En Retard',
    ],
    
    'inventory_history' => [
        'title' => 'Historique du Stock de Production',
        'header' => 'Historique des Transactions - :number',
        'summary_title' => 'Résumé des Transactions',
        
        'summary' => [
            'production_number' => 'Numéro de Production',
            'order_number' => 'Numéro de Commande',
            'customer' => 'Client',
            'total_transactions' => 'Total Transactions',
            'total_added' => 'Total Ajouté au Stock',
            'unique_products' => 'Produits Uniques',
            'completed' => 'Terminée',
            'pending' => 'En Attente',
            'total_removed' => 'Total Retiré',
        ],
        
        'table' => [
            'date' => 'Date',
            'product' => 'Produit',
            'production_item' => 'Article de Production',
            'transaction_type' => 'Type de Transaction',
            'quantity_change' => 'Changement Quantité',
            'stock_after' => 'Stock Après',
            'created_by' => 'Créé Par',
            'notes' => 'Notes',
            'status' => 'Statut',
            'product_code' => 'Code',
            'not_available' => 'N/A',
            'planned' => 'Planifié',
            'produced' => 'Produit',
            'system' => 'Système',
            'notes_title' => 'Notes de Transaction',
            'view_notes' => 'Voir Notes',
            'no_notes' => 'Aucune note',
            'actions' => 'Actions',
            'no_actions' => 'Aucune action disponible',
        ],
        
        'transaction_types' => [
            'production_output' => 'Sortie Production',
            'material_consumption' => 'Consommation Matière',
            'quality_check' => 'Contrôle Qualité',
            'waste' => 'Déchet',
            'adjustment' => 'Ajustement',
        ],
        
        'status' => [
            'completed' => 'Terminée',
            'pending' => 'En Attente',
            'cancelled' => 'Annulée',
        ],
        
        'messages' => [
            'pending_transactions' => 'Il y a :count transactions en attente d\'approbation',
            'confirm_approve_all' => 'Êtes-vous sûr de vouloir approuver toutes les transactions en attente ?',
            'confirm_reject_all' => 'Êtes-vous sûr de vouloir rejeter toutes les transactions en attente ?',
            'reject_reason_required' => 'Veuillez fournir un motif de rejet',
            'transaction_approved' => 'Transaction approuvée avec succès',
            'transaction_rejected' => 'Transaction rejetée avec succès',
            'all_transactions_approved' => 'Toutes les transactions en attente approuvées avec succès',
            'all_transactions_rejected' => 'Toutes les transactions en attente rejetées avec succès',
            'approve_error' => 'Erreur lors de l\'approbation de la transaction',
            'reject_error' => 'Erreur lors du rejet de la transaction',
            'approve_all_error' => 'Erreur lors de l\'approbation de toutes les transactions',
            'approved_by' => 'Approuvé par',
            'rejected_by' => 'Rejeté par',
            'no_transactions' => 'Aucune transaction d\'inventaire trouvée pour cette production.',
        ],
    ],
    'details' => [
        'title' => 'Détails de la Production',
        'production_number' => 'Production :number',
        
        'summary' => [
            'order_number' => 'Numéro de Commande',
            'customer' => 'Client',
            'status' => 'Statut',
            'progress' => 'Progression',
        ],
        
        'dates' => [
            'start_date' => 'Date de Début',
            'end_date' => 'Date de Fin',
        ],
        
        'notes' => [
            'title' => 'Notes',
        ],
        
        'items' => [
            'title' => 'Articles en Production',
            'product_name' => 'Nom du Produit',
            'type' => 'Type',
            'planned_quantity' => 'Quantité Planifiée',
            'produced_quantity' => 'Quantité Produite',
            'progress' => 'Progression',
            'status' => 'Statut',
            'action' => 'Action',
        ],
        
        'invoices' => [
            'title' => 'Factures Associées',
            'invoice_number' => 'Numéro de Facture',
            'amount' => 'Montant',
            'status' => 'Statut',
            'date' => 'Date',
        ],
        
        'documents' => [
            'rnd_title' => 'Revue des Documents R&D',
            'qa_title' => 'Revue des Documents Qualité et Contrôle',
            'no_rnd_documents' => 'Aucun document R&D trouvé.',
            'no_qa_documents' => 'Aucun document QA trouvé.',
        ],
        
        'buttons' => [
            'inventory_history' => 'Historique Stock',
            'start_production' => 'Démarrer Production',
            'complete_production' => 'Terminer Production',
            'update' => 'Mettre à Jour',
            'add_quantity' => 'Ajouter Quantité',
            'close' => 'Fermer',
            'download' => 'Télécharger',
            'back_to_productions' => 'Retour aux Productions',
        ],
        
        'modal' => [
            'title' => 'Ajouter Quantité Prête - :product',
            'current_status' => 'Statut Actuel',
            'produced' => 'Produit',
            'planned' => 'Planifié',
            'remaining' => 'Restant',
            'quantity_to_add' => 'Quantité à Ajouter',
            'quantity_placeholder' => 'Entrez la quantité à ajouter',
            'max_available' => 'Maximum disponible: :quantity',
            'notes' => 'Notes (Optionnel)',
            'notes_placeholder' => 'Ajoutez des notes...',
        ],
        
        'confirmations' => [
            'start_production' => 'Démarrer la production?',
            'complete_production' => 'Terminer la production?',
        ],
    ],
    
    'dates' => [
        'not_started' => 'Non commencé',
        'not_completed' => 'Non terminé',
    ],
];