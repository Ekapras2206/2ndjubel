<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;

// Controllers di dalam folder Admin
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductVerificationController;
use App\Http\Controllers\Admin\TransactionReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// =====================================================================
// Frontend Routes (Untuk pengguna umum)
// =====================================================================

// Default Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (Laravel UI default)
Auth::routes();

// Home Dashboard setelah login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Products Routes (Bisa diakses publik untuk melihat produk)
Route::resource('products', ProductController::class);

// Protected Routes (Hanya untuk pengguna yang sudah login)
Route::middleware(['auth'])->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Chat Routes
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show'); // Hanya satu definisi
    Route::post('/chats/{chat}/messages', [ChatController::class, 'storeMessage'])->name('messages.store');
    Route::post('/chat/start/seller/{seller}/product/{product}', [ChatController::class, 'startChatWithSeller'])->name('chat.startWithSeller');

    // Transactions Routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    // HANYA SATU RUTE UNTUK DETAIL TRANSAKSI, MENGGUNAKAN {order} UNTUK ROUTE MODEL BINDING
    Route::get('/transactions/{order}', [TransactionController::class, 'show'])->name('transactions.show'); // BARIS INI DIPERBAIKI

    // Rute Checkout Langsung (Buy Now) - Pindahkan ke dalam grup auth
    Route::get('/checkout/confirm-buy/{product}', [CheckoutController::class, 'showConfirmBuyNowForm'])->name('checkout.confirmBuyNow');
    Route::post('/buy-product/{product}', [TransactionController::class, 'processDirectBuy'])->name('products.processDirectBuy');
    //  Route::get('/checkout/confirm-buy/{product}', [CheckoutController::class, 'showConfirmBuyNowForm'])->name('checkout.confirmBuyNow');
    Route::post('/checkout/buy-now/{product}', [CheckoutController::class, 'processBuyNow'])->name('checkout.processBuyNow');

    // Rute Keranjang Belanja
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Rute Rating (Ditempatkan di dalam grup auth dan perbaiki sintaks)
    Route::post('/transactions/items/{orderItem}/rate', [TransactionController::class, 'rate'])->name('transactions.rate'); // BARIS INI DIPERBAIKI

    // Rute Checkout (Halaman utama checkout dan proses)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

}); // Penutup untuk grup middleware 'auth'

// =====================================================================
// Admin Routes (Hanya untuk user dengan role 'admin')
// =====================================================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin-panel'])->group(function () {

    Route::get('/', [UserManagementController::class, 'index'])->name('dashboard');

    Route::resource('users', UserManagementController::class)->except(['show']);

    Route::get('products/verification', [ProductVerificationController::class, 'index'])->name('products.verification');
    Route::post('products/{product}/approve', [ProductVerificationController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [ProductVerificationController::class, 'reject'])->name('products.reject');

    Route::get('transactions/report', [TransactionReportController::class, 'index'])->name('transactions.report');
    // ++ Ad Management ++
    Route::resource('ads', AdManagementController::class);

    Route::resource('categories', CategoryController::class);

});
