<?php

namespace App\Domain\ValueObject;

/**
 * =================================================================
 * DOMAIN - VALUE OBJECT
 * =================================================================
 * 
 * 🎯 OBJECTIF : Définir les catégories d'actualités.
 * 
 * 💡 POURQUOI : Garantir que seules des catégories valides sont utilisées dans le système.
 */
enum NewsCategory: string
{
    case EVENTS = 'événements';
    case SCHOOL_LIFE = 'vie';
    case SUCCESSES = 'réussites';
    case PARTNERSHIPS = 'partenariats';

    public function getLabel(): string
    {
        return match($this) {
            self::EVENTS => 'Événements',
            self::SCHOOL_LIFE => 'Vie étudiante',
            self::SUCCESSES => 'Réussites',
            self::PARTNERSHIPS => 'Partenariats',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::EVENTS => 'blue',
            self::SCHOOL_LIFE => 'green',
            self::SUCCESSES => 'orange',
            self::PARTNERSHIPS => 'purple',
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
