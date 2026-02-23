<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Field;
use App\Policies\FieldPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\AiAnalysis;
use App\Policies\AiAnalysisPolicy;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
        Gate::policy(Field::class, FieldPolicy::class);
        Gate::policy(AiAnalysis::class, AiAnalysisPolicy::class);
    }

}
