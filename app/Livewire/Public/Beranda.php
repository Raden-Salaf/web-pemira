<?php

namespace App\Livewire\Public;

use App\Models\Pemilihan;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Beranda extends Component
{
    public function render()
    {
        // Ambil SEMUA pemilihan yang is_active = true, urutkan yang
        // "berlangsung" duluan di atas, baru "belum mulai", baru "selesai".
        // Kita gak nampilin yang is_active = false sama sekali -- itu
        // pemilihan draft/arsip yang admin sengaja sembunyikan dari publik.
        $pemilihans = Pemilihan::where('is_active', true)
            ->withCount('paslons')
            ->orderByRaw("
                CASE
                    WHEN status = 'berlangsung' THEN 1
                    WHEN status = 'draft' THEN 2
                    ELSE 3
                END
            ")
            ->get();

        return view('livewire.public.beranda', [
            'pemilihans' => $pemilihans,
        ]);
    }
}