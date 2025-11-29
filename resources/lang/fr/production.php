<?php

return [
    'title' => 'Production',
    'page_title' => 'Gestion de la Production',
    
    'stats' => [
        'total' => 'Production Totale',
        'pending' => 'En Attente',
        'in_progress' => 'En Cours',
        'quality_check' => 'Contrôle Qualité',
        'completed' => 'Terminé',
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
        'completed' => 'Terminé',
        'not_started' => 'Non commencé',
    ],
    
    'buttons' => [
        'create' => 'Créer Production',
        'view' => 'Voir Production',
    ],
    
    'messages' => [
        'no_records' => 'Aucun enregistrement de production trouvé.',
    ],
    
    'datatable' => [
        'search' => 'Rechercher travaux production:',
        'show_entries' => 'Afficher _MENU_ entrées',
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
    
    'invoice_status' => [
        'paid' => 'Payé',
        'pending' => 'En Attente',
        'overdue' => 'En Retard',
    ],
    'inventory_history' => [
        'title' => 'Historique du Stock de Production',
        'header' => 'Historique des Transactions de Stock - :number',
        'summary_title' => 'Résumé des Transactions',
        
        'summary' => [
            'production_number' => 'Numéro de Production',
            'order_number' => 'Numéro de Commande',
            'customer' => 'Client',
            'total_transactions' => 'Total des Transactions',
            'total_added' => 'Total Ajouté au Stock',
            'unique_products' => 'Produits Uniques',
            'completed' => 'Terminé',
        ],
        
        'table' => [
            'date' => 'Date',
            'product' => 'Produit',
            'production_item' => 'Article de Production',
            'transaction_type' => 'Type de Transaction',
            'quantity_change' => 'Changement de Quantité',
            'stock_after' => 'Stock Après',
            'created_by' => 'Créé Par',
            'notes' => 'Notes',
            'status' => 'Statut',
            'product_code' => 'Code',
            'not_available' => 'N/D',
            'planned' => 'Planifié',
            'produced' => 'Produit',
            'system' => 'Système',
            'notes_title' => 'Notes de Transaction',
            'view_notes' => 'Voir Notes',
            'no_notes' => 'Aucune note',
        ],
        
        'transaction_types' => [
            'production_output' => 'Sortie de Production',
            'material_consumption' => 'Consommation Matière',
            'quality_check' => 'Contrôle Qualité',
            'waste' => 'Déchet',
            'adjustment' => 'Ajustement',
        ],
        
        'status' => [
            'completed' => 'Terminé',
            'pending' => 'En Attente',
            'cancelled' => 'Annulé',
        ],
        
        'buttons' => [
            'back_to_production' => 'Retour à la Production',
        ],
        
        'messages' => [
            'no_transactions' => 'Aucune transaction de stock trouvée pour cette production.',
        ],
    ],
];