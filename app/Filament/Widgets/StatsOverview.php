<?php

namespace App\Filament\Widgets;

use App\Models\Mahasiswa;
use App\Models\Pemilihan;
use App\Models\Pemilih;
use App\Models\Suara;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pemilihan Aktif', Pemilihan::where('is_active', true)->count())
                ->description('Sedang berjalan/dibuka untuk publik')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),

            Stat::make('Total Mahasiswa', Mahasiswa::count())
                ->description('Data hasil import Excel')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            // count where status dpt: dihitung dari relasi pivot pemilihs,
            // bukan dari mahasiswas -- karena DPT itu status PER pemilihan,
            // bukan status permanen si mahasiswa (inget desain Step 2.5 dulu)
            Stat::make('Total DPT', Pemilih::where('status', 'dpt')->count())
                ->description('Terverifikasi di semua pemilihan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),

            Stat::make('Suara Masuk', Suara::count())
                ->description('Total suara tercatat')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}