<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    // Same values as the brand-* tokens in resources/css/app.css (600 only exists here). Also used by the portal.
    public const BRAND_COLORS = [
        50 => '#f5fbf3', 100 => '#eaf6e6', 200 => '#ddf0d5', 300 => '#bbe2ab',
        400 => '#76c457', 500 => '#589f3c', 600 => '#437d2c', 700 => '#366722',
        800 => '#2a501b', 900 => '#203c15', 950 => '#15250e',
    ];

    public const MIDDLEWARE = [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        DisableBladeIconComponents::class,
        DispatchServingFilamentEvent::class,
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('BASolution')
            ->brandLogo(fn () => Setting::logoUrl())
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico').'?v=ba')
            ->font('Plus Jakarta Sans')
            ->colors(['primary' => self::BRAND_COLORS])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware(self::MIDDLEWARE)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
