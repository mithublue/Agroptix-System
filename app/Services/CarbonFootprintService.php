<?php

namespace App\Services;

class CarbonFootprintService
{
    /**
     * Emission factors in kg CO2e per kg-km
     * These are simplified estimates for demonstration purposes.
     */
    public const FACTOR_ROAD = 0.0001; // ~0.1g per kg-km
    public const FACTOR_AIR = 0.001;   // ~1g per kg-km
    public const FACTOR_SEA = 0.00001; // ~0.01g per kg-km
    public const FACTOR_RAIL = 0.00005;

    public static function calculateEmission(float $distanceKm, float $weightKg, string $mode): float
    {
        $factor = match (strtolower($mode)) {
            'air', 'plane' => self::FACTOR_AIR,
            'sea', 'ship', 'ocean' => self::FACTOR_SEA,
            'rail', 'train' => self::FACTOR_RAIL,
            default => self::FACTOR_ROAD, // Default to Road
        };

        return $distanceKm * $weightKg * $factor;
    }

    public static function getModeIcon(string $mode): string
    {
        return match (strtolower($mode)) {
            'air', 'plane' => '✈️',
            'sea', 'ship' => '🚢',
            'rail', 'train' => '🚂',
            default => '🚚',
        };
    }
}
