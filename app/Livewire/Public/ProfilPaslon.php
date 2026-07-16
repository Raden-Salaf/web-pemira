<?php

namespace App\Livewire\Public;

use App\Models\Pemilihan;
use Illuminate\Http\Exceptions\HttpResponseException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProfilPaslon extends Component
{
    // Route model binding otomatis, sama seperti DaftarPemilih sebelumnya
    public Pemilihan $pemilihan;

    /**
     * mount() dijalankan SEKALI di awal, sebelum method render() dipanggil.
     * Di sinilah tempat yang tepat buat validasi akses -- kalau pemilihan
     * belum di-set aktif oleh admin, kita hentikan akses dengan error 404
     * (pura-pura halamannya gak ada, daripada kasih tau detail "ini masih draft"
     * yang bisa jadi informasi gak perlu buat orang luar).
     */
    public function mount(): void
    {
        if (! $this->pemilihan->is_active) {
            abort(404);
        }
    }

    public function render()
    {
        // Load relasi 'paslons' sekaligus di sini (eager loading), supaya
        // gak muncul N+1 query problem -- daripada Blade manggil
        // $pemilihan->paslons berkali-kali (tiap kali query baru ke database),
        // kita load semua paslonnya SEKALI di awal, urutkan berdasarkan nomor urut.
        $paslons = $this->pemilihan->paslons()->orderBy('nomor_urut')->get();

        return view('livewire.public.profil-paslon', [
            'paslons' => $paslons,
        ]);
    }
}