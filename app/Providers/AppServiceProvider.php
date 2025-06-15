<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Import semua Repositories dan Services Anda di sini
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ChatRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\RatingRepository;
use App\Repositories\AdRepository;

use App\Services\ProductService;
use App\Services\ChatService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Services\AdService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repositories
        $this->app->singleton(ProductRepository::class, function ($app) {
            return new ProductRepository();
        });
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepository();
        });
        $this->app->singleton(CategoryRepository::class, function ($app) {
            return new CategoryRepository();
        });
        $this->app->singleton(ChatRepository::class, function ($app) {
            return new ChatRepository();
        });
        $this->app->singleton(TransactionRepository::class, function ($app) {
            return new TransactionRepository();
        });
        $this->app->singleton(RatingRepository::class, function ($app) {
            return new RatingRepository();
        });
        $this->app->singleton(AdRepository::class, function ($app) {
            return new AdRepository();
        });


        // Bind Services (mereka akan menerima Repositories yang sudah di-bind)
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(TransactionRepository::class) // ProductService butuh TransactionRepository
            );
        });
        $this->app->singleton(ChatService::class, function ($app) {
            return new ChatService(
                $app->make(ChatRepository::class),
                $app->make(ProductRepository::class) // ChatService butuh ProductRepository
            );
        });
        $this->app->singleton(TransactionService::class, function ($app) {
            return new TransactionService(
                $app->make(TransactionRepository::class),
                $app->make(RatingRepository::class) // TransactionService butuh RatingRepository
            );
        });
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });
        $this->app->singleton(AdService::class, function ($app) {
            return new AdService(
                $app->make(AdRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}