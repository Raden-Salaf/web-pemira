<?php

namespace App\Livewire\Public;

use App\Models\Pemilihan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class HasilSuara extends Component
{
    public Pemilihan $pemilihan;

    public function mount(): void
    {
        if (! $this->pemilihan->is_active) {
            abort(404);
        }
    }

    public function render()
    {
        // Ambil semua paslon beserta jumlah suaranya, urutkan dari yang
        // suaranya PALING BANYAK ke paling sedikit -- biar publik langsung
        // lihat "siapa unggul sementara" di urutan teratas
        $paslons = $this->pemilihan->paslons()
            ->orderBy('nomor_urut')
            ->get()
            ->map(function ($paslon) {
                // Kita hitung jumlah & persentase di sini (pakai method yang
                // sudah kita buat di Model Paslon waktu Step 3.4), lalu
                // "tempelkan" sebagai property tambahan supaya gampang dipakai di Blade
                $paslon->jumlah = $paslon->jumlahSuara();
                $paslon->persentase = $paslon->persentaseSuara();
                return $paslon;
            })
            ->sortByDesc('jumlah')
            ->values();

        return view('livewire.public.hasil-suara', [
            'paslons' => $paslons,
            'totalSuara' => $this->pemilihan->totalSuaraMasuk(),
        ]);
    }
}