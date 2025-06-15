<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     */
    public function show()
    {
        // Ini akan menampilkan view di 'resources/views/profile/show.blade.php'
        return view('profile.show');
    }

    /**
     * Tampilkan form untuk mengedit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        // Ini akan menampilkan view di 'resources/views/profile/edit.blade.php'
        return view('profile.edit', compact('user'));
    }

    /**
     * Update nama dan email pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi data yang masuk dari form
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Rule::unique('users')->ignore($user->id) memastikan email unik
            // kecuali untuk pengguna ini sendiri.
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        // 2. Update data nama dan email pada objek user
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Simpan perubahan ke database
        $user->save();

        // 4. Arahkan kembali ke halaman profil dengan pesan sukses
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
