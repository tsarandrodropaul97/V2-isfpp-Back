<?php

namespace App\Domain\ValueObject;

/**
 * =================================================================
 * DOMAIN - VALUE OBJECT
 * =================================================================
 * 
 * 🎯 OBJECTIF : Définir les statuts possibles d'une actualité (Brouillon, Publié).
 * 
 * 💡 POURQUOI : Encapsuler les valeurs autorisées et la logique d'affichage (labels).
 * 
 * 📚 PRINCIPE SOLID : Single Responsibility Principle (SRP) - Ce fichier ne fait que définir le statut.
 */
enum NewsStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    /**
     * Retourne le libellé lisible du statut.
     */
    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PUBLISHED => 'Publié',
        };
    }

    /**
     * Retourne toutes les valeurs possibles.
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
