<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User; // Pastikan path ke model User Anda benar
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator; // ++ Tambahkan ini
use Illuminate\Database\Eloquent\Collection; // Ini bisa dihapus dari return type getAllUsers jika sudah tidak mengembalikan Collection

class UserService
{
    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination.
     *
     * @param int $perPage Jumlah item per halaman (default 15).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator // ++ Ubah return type
     */
    public function getAllUsers(int $perPage = 15): LengthAwarePaginator // ++ Ubah return type dan tambahkan parameter $perPage
    {
        // Langsung menggunakan Model User untuk paginasi
        // Anda bisa menambahkan orderBy jika diperlukan, misalnya User::orderBy('name', 'asc')->paginate($perPage)
        return User::paginate($perPage);

        // Jika Anda ingin tetap menggunakan UserRepository, pastikan metode
        // di UserRepository (misalnya getAllPaginated) juga mengembalikan hasil dari ->paginate().
        // Contoh: return $this->userRepository->getAllPaginated($perPage);
    }

    /**
     * Update a user's profile.
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return bool
     */
    public function updateProfile(User $user, array $data): bool
    {
        // Handle profile picture upload
        if (isset($data['profile_picture'])) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $data['profile_picture']->store('profile_pictures', 'public');
        }

        // Handle password update if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Don't update password if not provided
        }

        // Daripada memanggil $this->userRepository->update($user, $data);
        // lebih umum jika service langsung mengupdate modelnya, atau repository
        // memiliki metode update yang menerima ID dan data.
        // Namun, jika $this->userRepository->update() sudah ada dan bekerja, Anda bisa biarkan.
        // Untuk konsistensi, seringkali service akan melakukan:
        // $user->fill($data);
        // return $user->save();
        // Atau jika UserRepository->update() menerima ID:
        // return $this->userRepository->update($user->id, $data);
        return $user->update($data); // Menggunakan metode update Eloquent langsung pada model User
                                     // Pastikan semua field di $data ada di $fillable model User
    }

    /**
     * Toggle a user's active status.
     *
     * @param int $userId
     * @return bool
     */
    public function toggleUserStatus(int $userId): bool
    {
        $user = $this->userRepository->findById($userId); // Asumsi findById ada di UserRepository
        if (!$user) {
            return false;
        }
        // return $this->userRepository->toggleActiveStatus($user); // Asumsi metode ini ada di UserRepository

        // Jika Anda ingin logika toggle langsung di service:
        // Asumsi User model punya kolom 'is_active' (boolean)
        // $user->is_active = !$user->is_active;
        // return $user->save();

        // Untuk sekarang, karena saya tidak tahu implementasi toggleActiveStatus di repo Anda,
        // saya akan biarkan seperti ini. Pastikan metode tersebut melakukan penyimpanan.
        // Jika tidak ada, Anda perlu mengimplementasikan logika penyimpanan di sini atau di repository.
        // Contoh sederhana jika ingin mengubah is_admin (hati-hati):
        // $user->is_admin = !$user->is_admin;
        // return $user->save();
        // Ini hanya placeholder, sesuaikan dengan logika status Anda
        if (property_exists($user, 'is_active')) { // Cek jika properti is_active ada
            $user->is_active = !$user->is_active;
            return $user->save();
        } elseif (property_exists($user, 'status')) { // Atau mungkin kolom status
            // Logika untuk toggle kolom status (misal: 'active' -> 'inactive')
            // $user->status = ($user->status === 'active' ? 'inactive' : 'active');
            // return $user->save();
            return false; // Implementasikan logika ini
        }
        return false; // Gagal jika tidak ada kolom status yang jelas
    }
}
