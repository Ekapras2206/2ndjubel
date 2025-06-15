<?php

namespace App\Repositories;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Collection;

class AdRepository
{
    /**
     * Get all active ads.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveAds(): Collection
    {
        return Ad::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }

    /**
     * Find an ad by its ID.
     *
     * @param int $id
     * @return \App\Models\Ad|null
     */
    public function findById(int $id): ?Ad
    {
        return Ad::find($id);
    }

    /**
     * Create a new ad.
     *
     * @param array $data
     * @return \App\Models\Ad
     */
    public function create(array $data): Ad
    {
        return Ad::create($data);
    }

    /**
     * Update an existing ad.
     *
     * @param \App\Models\Ad $ad
     * @param array $data
     * @return bool
     */
    public function update(Ad $ad, array $data): bool
    {
        return $ad->update($data);
    }

    /**
     * Delete an ad.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $ad = $this->findById($id);
        if ($ad) {
            return $ad->delete();
        }
        return false;
    }
}
