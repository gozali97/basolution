<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;

/** Client portal: access requires the `client` role and a client_id (see User::canAccessPanel). */
class PortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('portal')
            ->login()
            ->profile()
            ->brandName('BASolution Portal')
            ->brandLogo(fn () => Setting::logoUrl())
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico').'?v=ba')
            ->font('Plus Jakarta Sans')
            ->colors(['primary' => AdminPanelProvider::BRAND_COLORS])
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\Filament\Portal\Resources')
            ->middleware(AdminPanelProvider::MIDDLEWARE)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
