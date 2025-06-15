<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdService;
use App\Http\Requests\AdRequest;
use App\Models\Ad; // Import Model Ad
use Illuminate\Http\Request;

class AdManagementController extends Controller
{
    protected $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    /**
     * Display a listing of the ads.
     */
    public function index()
    {
        // BARIS YANG DIPERBAIKI:
        $ads = $this->adService->getActiveAds(); 
        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new ad.
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created ad in storage.
     */
    public function store(AdRequest $request)
    {
        try {
            $this->adService->createAd($request->validated());
            return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan iklan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ad.
     */
    public function show(Ad $ad)
    {
        return view('admin.ads.show', compact('ad'));
    }

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified ad in storage.
     */
    public function update(AdRequest $request, Ad $ad)
    {
        try {
            $this->adService->updateAd($ad->id, $request->validated());
            return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui iklan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ad from storage.
     */
    public function destroy(Ad $ad)
    {
        if ($this->adService->deleteAd($ad->id)) {
            return back()->with('success', 'Iklan berhasil dihapus.');
        }
        return back()->with('error', 'Gagal menghapus iklan.');
    }
}
