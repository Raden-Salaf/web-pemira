<?php

namespace App\Filament\Pages;

use App\Models\Pemilihan;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HasilPemilihan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Hasil & Laporan';
    protected static ?string $title = 'Hasil Perolehan Suara';

    protected string $view = 'filament.pages.hasil-pemilihan';

    public ?int $pemilihanId = null;

    // Daftar SEMUA pemilihan, terbaru duluan -- admin mungkin perlu cek
    // hasil sementara pemilihan yang MASIH berlangsung, bukan cuma yang udah selesai
    #[Computed]
    public function pemilihanList()
    {
        return Pemilihan::orderByDesc('created_at')->get();
    }

    #[Computed]
    public function pemilihanTerpilih(): ?Pemilihan
    {
        return $this->pemilihanId ? Pemilihan::find($this->pemilihanId) : null;
    }

    /**
     * Data paslon LENGKAP dengan jumlah & persentase suara, dihitung
     * pakai method yang udah kita buat di Model Paslon sejak Step 3.4 dulu.
     * Diurutkan dari suara TERBANYAK ke tersedikit.
     */
    #[Computed]
    public function paslons()
    {
        if (! $this->pemilihanTerpilih) {
            return collect();
        }

        return $this->pemilihanTerpilih->paslons()
            ->orderBy('nomor_urut')
            ->get()
            ->map(function ($paslon) {
                $paslon->jumlah = $paslon->jumlahSuara();
                $paslon->persentase = $paslon->persentaseSuara();
                return $paslon;
            })
            ->sortByDesc('jumlah')
            ->values();
    }

    #[Computed]
    public function totalSuara(): int
    {
        return $this->pemilihanTerpilih?->totalSuaraMasuk() ?? 0;
    }

    /**
     * Data yang khusus disiapkan buat Chart.js (JavaScript), format-nya
     * HARUS array asosiatif sederhana (label, value, warna) karena nanti
     * di-passing ke Alpine.js lewat directive @js() di Blade -- itu
     * otomatis convert array PHP ini jadi JSON yang dibaca JavaScript.
     */
    #[Computed]
    public function chartData(): array
    {
        // Palet warna berbeda per paslon, supaya potongan pie chart
        // gampang dibedain -- muter lagi dari awal kalau paslonnya lebih dari 6
        $palet = ['#1E40AF', '#4C1D95', '#059669', '#D97706', '#DC2626', '#0891B2'];

        return [
            'labels' => $this->paslons->map(fn ($p) => "No.{$p->nomor_urut} {$p->nama_ketua}")->toArray(),
            'values' => $this->paslons->pluck('jumlah')->toArray(),
            'colors' => $this->paslons->values()->map(fn ($p, $i) => $palet[$i % count($palet)])->toArray(),
        ];
    }

    /**
     * INI FITUR EXPORT PDF.
     * Livewire (dan Filament Page, yang berbasis Livewire) MENDUKUNG
     * method aksi yang me-return file response langsung -- browser bakal
     * otomatis mulai proses download begitu method ini selesai jalan,
     * TANPA perlu redirect ke route terpisah.
     */
    public function unduhPdf(): StreamedResponse
    {
        $pemilihan = $this->pemilihanTerpilih;

        $pdf = Pdf::loadView('pdf.hasil-pemilihan', [
            'pemilihan' => $pemilihan,
            'paslons' => $this->paslons,
            'totalSuara' => $this->totalSuara,
        ]);

        $namaFile = 'hasil-' . str($pemilihan->nama)->slug() . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $namaFile
        );
    }
}