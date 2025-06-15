<?php

namespace App\Traits; // <--- PASTIKAN NAMESPACE INI BENAR SESUAI LOKASI FOLDER app/Traits/

trait HasLocation
{
    /**
     * Get the full address attribute.
     * Accessor for displaying full address.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        // Trait ini mengasumsikan model yang menggunakannya memiliki kolom 'address' dan 'city'
        // Jika tidak ada, Anda perlu menyesuaikannya.
        return "{$this->address}, {$this->city}";
    }

    /**
     * Calculate distance to a target latitude and longitude using the Haversine formula.
     * This method can be used by any model that uses this trait and has 'latitude' and 'longitude' columns.
     *
     * @param float $targetLat Latitude of the target location.
     * @param float $targetLng Longitude of the target location.
     * @param string $unit The unit of distance ('km' for kilometers, 'miles' for miles).
     * @return float|null The calculated distance, or null if the product's location is missing.
     */
    public function distanceTo(float $targetLat, float $targetLng, string $unit = 'km'): ?float
    {
        // Pastikan model yang menggunakan trait ini memiliki kolom latitude dan longitude
        if (empty($this->latitude) || empty($this->longitude)) {
            return null; // Mengembalikan null jika data lokasi produk tidak lengkap
        }

        $earthRadius = ($unit === 'miles') ? 3959 : 6371; // Radius bumi dalam mil atau kilometer

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($targetLat);
        $lonTo = deg2rad($targetLng);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
             pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $earthRadius * $angle;
    }
}