<?php

namespace App\Filament\Pages;

use App\Models\Mahasiswa;
use App\Models\Pemilihan;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class KelolaPemilihManual extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Whitelist Pemilih Manual';
    protected static ?string $title = 'Kelola Whitelist Pemilih Manual';

    protected string $view = 'filament.pages.kelola-pemilih-manual';

    // Pemilihan mana yang lagi dipilih admin buat dikelola whitelist-nya
    public ?int $pemilihanId = null;

    public string $nim = '';
    public string $nama = '';

    /**
     * Dipanggil OTOMATIS oleh Livewire setiap kali dropdown pemilihanId
     * berubah (wire:model.live). Gak perlu logic apapun di sini karena
     * daftar whitelist-nya sendiri diambil langsung di render(), tapi
     * method ini WAJIB ada supaya Livewire tau harus re-render halaman.
     */
    public function updatedPemilihanId(): void
    {
        //
    }

    /**
     * Tambahkan 1 mahasiswa ke whitelist pemilihan yang lagi dipilih.
     * firstOrCreate() di sini PENTING: kalau NIM yang diinput admin
     * BELUM ada di data mahasiswa (misal orang eksternal/belum pernah
     * di-import Excel), otomatis dibuatkan record barunya -- jadi
     * fitur ini tetap fleksibel dipakai buat siapapun, bukan cuma
     * mahasiswa yang datanya udah ada.
     */
    public function tambahKeWhitelist(): void
    {
        $this->validate([
            'pemilihanId' => 'required|exists:pemilihans,id',
            'nim' => 'required|string',
            'nama' => 'required|string',
        ]);

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => trim($this->nim)],
            ['nama' => trim($this->nama)]
        );

        $pemilihan = Pemilihan::find($this->pemilihanId);

        // syncWithoutDetaching: nambahin relasi TANPA menghapus relasi
        // lain yang udah ada sebelumnya (beda dengan sync() biasa yang
        // akan MENGHAPUS entry lama yang gak disebutkan)
        $pemilihan->whitelistManual()->syncWithoutDetaching([$mahasiswa->id]);

        Notification::make()
            ->title("{$mahasiswa->nama} ditambahkan ke whitelist")
            ->success()
            ->send();

        $this->nim = '';
        $this->nama = '';
    }

    /**
     * Keluarkan 1 mahasiswa dari whitelist (bukan hapus data mahasiswanya,
     * cuma putuskan relasinya ke pemilihan ini)
     */
    public function hapusDariWhitelist(int $mahasiswaId): void
    {
        Pemilihan::find($this->pemilihanId)->whitelistManual()->detach($mahasiswaId);

        Notification::make()->title('Dihapus dari whitelist')->success()->send();
    }

    /**
     * #[Computed] itu fitur Livewire yang bikin method ini bisa diakses
     * LANGSUNG di Blade sebagai $this->pemilihanList, TANPA perlu
     * dikirim manual lewat render(). Livewire otomatis panggil ulang
     * method ini tiap kali halaman perlu di-refresh datanya.
     */
    #[Computed]
    public function pemilihanList()
    {
        return Pemilihan::where('sumber_pemilih', 'manual')->get();
    }

    #[Computed]
    public function whitelist()
    {
        return $this->pemilihanId
            ? Pemilihan::find($this->pemilihanId)->whitelistManual()->get()
            : collect();
    }
}
