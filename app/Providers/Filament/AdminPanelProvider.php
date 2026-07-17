<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->sidebarWidth('16rem')
            ->colors([
                // Color::hex() generate seluruh gradasi warna (50-950) otomatis
                // dari 1 kode warna dasar -- kita pakai biru yang sama persis
                // dengan halaman publik (--color-blue: #1E40AF)
                'primary' => Color::hex('#1E40AF'),
            ])
            // ->font() otomatis nge-load font dari Google Fonts dan pasang ke SELURUH
            // panel admin, tanpa perlu setup manual kayak yang kita lakukan di app.blade.php
            ->font('Plus Jakarta Sans')
            // logo title
            ->favicon(asset('favicon.png'))
            // Ganti nama brand yang muncul di sidebar & tab browser
            ->brandName('Pemira Online')

            // Render hook ini nyuntikkan tag <link> font TAMBAHAN ke bagian <head>
            // halaman admin -- Space Grotesk (judul) dan JetBrains Mono (data/angka)
            // belum otomatis ke-load walau sudah kita daftarkan di theme.css,
            // karena ->font() bawaan Filament cuma handle 1 font body doang.
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn() => view('filament.partials.fonts')
            )

            // renderHook nge-inject konten TAMBAHAN ke posisi tertentu di halaman
            // Filament, TANPA perlu override/timpa total halaman login bawaannya.
            // AUTH_LOGIN_FORM_BEFORE = posisi tepat SEBELUM form login muncul.
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn() => view('filament.auth.slogan')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
