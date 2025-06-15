<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService; // Anda sudah punya ini
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Validation\Rules;    // Untuk aturan validasi password

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users for admin management.
     */
    public function index()
    {
        // Asumsi userService->getAllUsers() sudah mengembalikan hasil paginasi
        $users = $this->userService->getAllUsers();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     * ++ TAMBAHKAN METODE INI ++
     */
    public function create()
    {
        return view('admin.users.create'); // Pastikan view ini ada: resources/views/admin/users/create.blade.php
    }

    /**
     * Store a newly created user in storage.
     * ++ TAMBAHKAN METODE INI ++
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['nullable', 'boolean'], // 'nullable' jika checkbox tidak dicentang
        ]);

        // Anda bisa memindahkan logika pembuatan user ini ke UserService jika mau
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'), // Mengambil nilai boolean dari checkbox
            'email_verified_at' => now(), // Opsional: langsung verifikasi jika dibuat admin
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // Metode show() di-exclude berdasarkan rute Anda: ->except(['show'])
    // public function show(User $user)
    // {
    //     // return view('admin.users.show', compact('user'));
    // }

    /**
     * Show the form for editing the specified user.
     * ++ TAMBAHKAN METODE INI ++
     */
    public function edit(User $user) // Menggunakan Route Model Binding
    {
        return view('admin.users.edit', compact('user')); // Pastikan view ini ada: resources/views/admin/users/edit.blade.php
    }

    /**
     * Update the specified user in storage.
     * ++ TAMBAHKAN METODE INI ++
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opsional saat update
            'is_admin' => ['nullable', 'boolean'],
        ]);

        // Anda bisa memindahkan logika update user ini ke UserService jika mau
        $userData = $request->only('name', 'email');
        $userData['is_admin'] = $request->boolean('is_admin');

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     * ++ TAMBAHKAN METODE INI ++
     */
    public function destroy(User $user)
    {
        // Tambahkan logika untuk mencegah admin menghapus dirinya sendiri jika perlu
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Anda bisa memindahkan logika delete user ini ke UserService jika mau
        $user->delete(); // Ini akan soft delete jika User model menggunakan SoftDeletes trait

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Toggle the active status of a user.
     * (Ini sudah ada di kode Anda)
     */
    public function toggleStatus(User $user)
    {
        // Logika untuk toggle status seharusnya ada di UserService
        // Misalnya, jika userService->toggleUserStatus mengembalikan boolean
        if ($this->userService->toggleUserStatus($user->id)) { // Menggunakan $user->id seperti kode asli Anda
            return back()->with('success', 'Status pengguna berhasil diubah.');
        }
        return back()->with('error', 'Gagal mengubah status pengguna.');
    }
}
