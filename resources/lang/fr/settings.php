<?php

return [
    'administration' => 'Administration',
    
    // Tabs
    'tabs' => [
        'profile' => 'Mon Profil',
        'password' => 'Changer le Mot de Passe',
        'settings' => 'Paramètres',
    ],
    
    // Profile Tab
    'profile' => [
        'title' => 'Profil Administrateur',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'email' => 'Adresse Email',
        'phone' => 'Numéro de Téléphone',
        'address' => 'Adresse',
        'save_changes' => 'Enregistrer les Modifications',
    ],
    
    // Password Tab
    'password' => [
        'title' => 'Changer le Mot de Passe',
        'current_password' => 'Mot de Passe Actuel',
        'new_password' => 'Nouveau Mot de Passe',
        'confirm_password' => 'Confirmer le Nouveau Mot de Passe',
        'update_password' => 'Mettre à jour le Mot de Passe',
        'password_requirements' => 'Le mot de passe doit contenir au moins :',
        'requirements' => [
            'min_chars' => '8 caractères',
            'uppercase' => '1 lettre majuscule',
            'lowercase' => '1 lettre minuscule',
            'number' => '1 chiffre',
            'special_char' => '1 caractère spécial',
        ],
    ],
    
    // Settings Tab
    'settings' => [
        'title' => 'Paramètres du Système',
        'company_name' => 'Nom de l\'Entreprise',
        'tax_id' => 'Numéro Fiscal',
        'phone' => 'Téléphone',
        'default_currency' => 'Devise par Défaut',
        'timezone' => 'Fuseau Horaire',
        'company_address' => 'Adresse de l\'Entreprise',
        'email_signature' => 'Signature Email',
        'save_settings' => 'Enregistrer les Paramètres',
    ],
    
    // Form Placeholders
    'placeholders' => [
        'enter_address' => 'Entrez votre adresse',
        'enter_current_password' => 'Entrez le mot de passe actuel',
        'enter_new_password' => 'Entrez le nouveau mot de passe',
        'confirm_new_password' => 'Confirmez le nouveau mot de passe',
        'enter_company_address' => 'Entrez l\'adresse de l\'entreprise',
        'enter_email_signature' => 'Entrez la signature email',
    ],
    
    // Success Messages
    'success' => [
        'profile_updated' => 'Profil mis à jour avec succès',
        'password_updated' => 'Mot de passe mis à jour avec succès',
        'settings_updated' => 'Paramètres mis à jour avec succès',
    ],
    
    // Error Messages
    'error' => [
        'password_mismatch' => 'Le nouveau mot de passe et la confirmation ne correspondent pas !',
        'password_update_failed' => 'Échec de la mise à jour du mot de passe',
    ],
    
    // Currencies
    'currencies' => [
        'DZD' => 'DZD - Dinar Algérien',
        'EUR' => 'EUR - Euro',
        'USD' => 'USD - Dollar Américain',
    ],
    
    // Timezones
    'timezones' => [
        'Africa/Algiers' => 'Alger (GMT+1)',
        'UTC' => 'UTC',
        'Europe/Paris' => 'Paris (GMT+1)',
    ],
    
    // Buttons & States
    'buttons' => [
        'saving' => 'Enregistrement...',
        'updating' => 'Mise à jour...',
    ],
    
    // Validation
    'validation' => [
        'required' => 'Ce champ est obligatoire',
    ],
];