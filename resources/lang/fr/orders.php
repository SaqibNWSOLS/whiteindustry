<?php

return [
    'title' => 'Commandes',
    'page_title' => 'Gestion des Commandes',
    
    'stats' => [
        'total_orders' => 'Total des Commandes',
        'pending' => 'En Attente',
        'confirmed' => 'Confirmées',
        'production' => 'En Production',
        'completed' => 'Terminées',
        'cancelled' => 'Annulées',
        'all_orders' => 'Toutes les commandes',
        'awaiting_confirmation' => 'En attente de confirmation',
        'confirmed_orders' => 'Commandes confirmées',
        'currently_in_production' => 'Actuellement en production',
        'completed_orders' => 'Commandes terminées',
        'cancelled_orders' => 'Commandes annulées',
    ],
    
    'list' => [
        'title' => 'Liste des Commandes',
        'create_order' => 'Créer une Commande',
        'order_number' => 'Commande N°',
        'quotation_number' => 'Devis N°',
        'customer' => 'Client',
        'order_date' => 'Date de Commande',
        'total_amount' => 'Montant Total',
        'status' => 'Statut',
        'actions' => 'Actions',
        'not_available' => 'N/D',
    ],
    
    'status' => [
        'pending' => 'En Attente',
        'confirmed' => 'Confirmée',
        'production' => 'Production',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ],
    
    'buttons' => [
        'create' => 'Créer une Commande',
        'edit' => 'Modifier',
        'view' => 'Voir',
    ],
    
    'empty' => 'Aucune commande trouvée.',
    'create_edit' => [
        'title' => [
            'create' => 'Créer une Commande',
            'edit' => 'Modifier la Commande',
            'page_create' => 'Créer une Commande',
            'page_edit' => 'Modifier la Commande',
        ],
        
        'steps' => [
            'basic' => 'Informations de Base',
            'products' => 'Produits',
            'raw_materials' => 'Matières Premières & Mélange',
            'packaging' => 'Emballage',
            'calculation' => 'Calcul',
        ],
        
        'step_titles' => [
            'basic' => 'Informations de Base',
            'products' => 'Ajouter des Produits',
            'raw_materials' => 'Sélection des Matières Premières',
            'blend' => 'Sélection du Mélange',
            'packaging' => 'Sélection de l\'Emballage',
            'calculation' => 'Calcul de la Commande',
        ],
        
        'form' => [
            'customer' => 'Client',
            'customer_placeholder' => 'Sélectionner un Client',
            'delivery_date' => 'Date de Livraison',
            'notes' => 'Notes',
            'notes_placeholder' => 'Ajouter des notes supplémentaires...',
            'product_name' => 'Nom du Produit',
            'product_name_placeholder' => 'Entrer le nom du produit',
            'product_type' => 'Type de Produit',
            'quantity' => 'Quantité',
            'quantity_placeholder' => 'Entrer la quantité du produit',
            'raw_material' => 'Matière Première',
            'raw_material_placeholder' => 'Sélectionner une Matière',
            'percentage' => 'Pourcentage (%)',
            'packaging' => 'Emballage',
            'packaging_placeholder' => 'Sélectionner un Emballage',
        ],
        
        'product_types' => [
            'cosmetic' => 'Cosmétique',
            'food_supplement' => 'Complément Alimentaire',
        ],
        
        'buttons' => [
            'add_another_product' => 'Ajouter un Autre Produit',
            'add_another_material' => 'Ajouter une Autre Matière',
            'add_another_packaging' => 'Ajouter un Autre Emballage',
            'remove' => 'Supprimer',
            'next_products' => 'Suivant: Ajouter des Produits',
            'next_raw_materials' => 'Suivant: Ajouter des Matières Premières',
            'next_packaging' => 'Suivant: Ajouter l\'Emballage',
            'next_calculation' => 'Suivant: Calculer la Commande',
            'update_continue' => 'Mettre à jour & Continuer',
            'back' => 'Retour',
            'cancel' => 'Annuler',
            'calculate_save' => 'Calculer & Sauvegarder la Commande',
            'recalculate_update' => 'Recalculer & Mettre à Jour',
            'view_final_order' => 'Voir la Commande Finale',
        ],
        
        'alerts' => [
            'total_percentage' => 'Pourcentage Total:',
            'remaining' => 'Restant:',
        ],
        
        'calculation' => [
            'cost_parameters' => 'Paramètres de Coût',
            'manufacturing_cost' => 'Coût de Fabrication %',
            'risk_cost' => 'Coût de Risque %',
            'profit_margin' => 'Marge Bénéficiaire %',
            'tax_rate' => 'Taux de Taxe %',
            'summary' => 'Résumé',
            'customer' => 'Client',
            'number_of_products' => 'Nombre de Produits',
            'total_raw_materials' => 'Total Matières Premières',
            'total_packaging_items' => 'Total Articles d\'Emballage',
            'current_calculation' => 'Calcul Actuel',
            'total_price' => 'Prix Total',
            'status' => 'Statut',
            'last_updated' => 'Dernière Mise à Jour',
        ],
        
        'messages' => [
            'select_customer' => 'Veuillez sélectionner un client',
            'percentage_warning' => 'Le pourcentage total doit être de 100%',
        ],
    ],
];