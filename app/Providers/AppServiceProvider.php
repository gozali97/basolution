<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Input only reaches models through Filament forms or validated request data.
        Model::unguard();

        View::composer(['components.layout', 'pages.*'], fn ($view) => $view->with('site', Setting::values()));
    }
}
