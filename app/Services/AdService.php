<?php

namespace App\Services;

use App\Repositories\AdRepository;
use App\Models\Ad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class AdService
{
    protected $adRepository;

    public function __construct(AdRepository $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    /**
     * Get all active advertisements.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAds(): Collection
    {
        return $this->adRepository->getAllActiveAds();
    }

    /**
     * Create a new advertisement.
     *
     * @param array $data
     * @return \App\Models\Ad
     */
    public function createAd(array $data): Ad
    {
        if (isset($data['image'])) {
            $data['image_path'] = $data['image']->store('ads', 'public');
            unset($data['image']); // Remove file object from data array
        }
        return $this->adRepository->create($data);
    }

    /**
     * Update an existing advertisement.
     *
     * @param int $adId
     * @param array $data
     * @return bool
     */
    public function updateAd(int $adId, array $data): bool
    {
        $ad = $this->adRepository->findById($adId);
        if (!$ad) {
            return false;
        }

        if (isset($data['image'])) {
            // Delete old image if exists
            if ($ad->image_path) {
                Storage::disk('public')->delete($ad->image_path);
            }
            $data['image_path'] = $data['image']->store('ads', 'public');
            unset($data['image']);
        }

        return $this->adRepository->update($ad, $data);
    }

    /**
     * Delete an advertisement.
     *
     * @param int $adId
     * @return bool
     */
    public function deleteAd(int $adId): bool
    {
        return $this->adRepository->delete($adId);
    }
}