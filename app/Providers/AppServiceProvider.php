<?php
namespace App\Providers;
use App\Models\Complaint;
use App\Models\doctor;
use App\Models\Patient;
use App\Models\secretary;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'Patient'=>Patient::class,
            'doctor'=>doctor::class,
            'secretary'=>secretary::class,
        ]);
    }
}
