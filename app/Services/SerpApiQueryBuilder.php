<?php

namespace App\Services;

class SerpApiQueryBuilder
{
    public static function forProduct(string $subject, array $filters): array
    {
        $q = $subject;
        if (!empty($filters['keyword']) && stripos($subject, $filters['keyword']) === false) {
            $q .= ' ' . $filters['keyword'];
        }

        $location = self::buildLocation($filters['location'] ?? []);

        return array_filter([
            'engine'        => 'google_local',
            'q'             => $q,
            'location'      => $location,
            'google_domain' => 'google.com',
            'hl'            => 'en',
            'gl'            => self::countryToGl($filters['location']['country'] ?? null),
        ]);
    }

    private static function buildLocation(array $loc): ?string
    {
        $parts = array_filter([
            $loc['district']    ?? null,
        ]);

        if (!$parts) {
            return null;
        }

        $joined = implode(', ', $parts);

        return $joined;
    }

    private static function countryToGl(?string $country): string
    {
        return match (strtolower($country ?? '')) {
            'india' => 'in',
            'united states', 'usa' => 'us',
            default => 'us',
        };
    }
}
