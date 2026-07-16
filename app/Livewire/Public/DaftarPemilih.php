<?php

namespace App\Livewire\Public;

use App\Models\Mahasiswa;
use App\Models\Pemilih;
use App\Models\Pemilihan;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class DaftarPemilih extends Component
{
    // Route model binding otomatis: Laravel cari Pemilihan berdasarkan {pemilihan}
    // di URL, dan inject langsung sebagai object di sini
    public Pemilihan $pemilihan;

    public string $nim = '';
    public string $nama = '';

    // Menyimpan record Pemilih milik mahasiswa yang barusan submit/sudah pernah
    // daftar sebelumnya -- dipakai buat nampilin status terkini (DPS/DPT/ditolak)
    public ?Pemilih $pemilihSaya = null;

    public ?string $pesanError = null;

    public function mount(): void
    {
        if (! $this->pemilihan->is_active) {
            abort(404);
        }
    }

    /**
     * INI LOGIC PALING KRUSIAL DI FITUR VERIFIKASI PEMILIH.
     *
     * Alur pengecekannya berlapis:
     * 1. Cari mahasiswa berdasarkan NIM persis (NIM harus exact match)
     * 2. Kalau ketemu, cocokkan NAMA -- tapi TANPA peduli huruf besar/kecil
     *    dan spasi berlebih, karena manusia sering typo kapitalisasi
     *    (misal ketik "ahmad" padahal di data "AHMAD")
     * 3. Kalau NIM+Nama cocok, cek apakah mahasiswa ini SUDAH pernah
     *    daftar di pemilihan INI sebelumnya (updateOrCreate mencegah duplikat)
     */
    public function daftar(): void
    {
        $this->pesanError = null;

        $this->validate([
            'nim' => 'required|string',
            'nama' => 'required|string',
        ]);

        // Cari mahasiswa berdasarkan NIM (exact match, karena NIM harus presisi)
        $mahasiswa = Mahasiswa::where('nim', trim($this->nim))->first();

        if (! $mahasiswa) {
            $this->pesanError = 'NIM tidak ditemukan dalam data mahasiswa. Pastikan NIM yang kamu masukkan benar.';
            return;
        }

        // Normalisasi nama SEBELUM dibandingkan: ubah ke huruf kecil semua,
        // dan rapikan spasi ganda/spasi di awal-akhir jadi 1 spasi biasa.
        // Ini supaya "ahmad   nur" bisa cocok dengan "Ahmad Nur" di database,
        // walau beda kapitalisasi atau ada spasi berlebih akibat typo.
        $namaInputNormal = $this->normalisasiNama($this->nama);
        $namaDbNormal = $this->normalisasiNama($mahasiswa->nama);

        if ($namaInputNormal !== $namaDbNormal) {
            $this->pesanError = 'NIM ditemukan, tapi nama yang kamu masukkan tidak cocok dengan data kami. Periksa kembali ejaan nama.';
            return;
        }

        // PENGECEKAN WHITELIST -- kalau pemilihan ini pakai mode 'manual',
        // NIM+Nama yang cocok di data mahasiswa SAJA belum cukup, harus juga
        // ada di whitelist khusus yang disiapkan admin untuk pemilihan ini.
        if (! $this->pemilihan->bolehDaftar($mahasiswa)) {
            $this->pesanError = 'Kamu tidak termasuk dalam daftar pemilih yang diizinkan untuk pemilihan ini. Hubungi panitia jika ini keliru.';
            return;
        }

        // NIM + Nama valid! Sekarang cek/buat record pendaftaran di pemilihan ini.
        // firstOrCreate: kalau mahasiswa ini SUDAH pernah daftar di pemilihan yang
        // SAMA, kita ambil record yang sudah ada (gak bikin duplikat, gak reset
        // statusnya kalau sudah DPT). Kalau belum pernah, baru dibuatkan status 'dps'.
        $this->pemilihSaya = Pemilih::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'pemilihan_id' => $this->pemilihan->id,
            ],
            [
                'status' => 'dps', // status awal: Daftar Pemilih Sementara
            ]
        );
        // Simpan "tanda pengenal" di session browser mereka, supaya nanti pas
        // akses halaman voting, sistem tau siapa yang lagi login TANPA perlu
        // password apapun. Session ini terikat ke BROWSER mereka saat ini,
        // jadi orang lain gak bisa asal buka halaman voting pakai URL yang sama.
        session(["pemilih_id_pemilihan_{$this->pemilihan->id}" => $this->pemilihSaya->id]);
    }

    /**
     * Helper normalisasi nama: lowercase + rapikan spasi.
     * Dipisah jadi method sendiri supaya konsisten dipakai untuk INPUT
     * maupun data DATABASE -- kalau logic ini berubah nanti, cukup ubah di 1 tempat.
     */
    private function normalisasiNama(string $nama): string
    {
        // preg_replace('/\s+/', ' ', ...) mengubah spasi ganda/tab/newline
        // jadi 1 spasi tunggal, lalu trim() buang spasi di awal-akhir,
        // baru Str::lower() buat case-insensitive
        return Str::lower(trim(preg_replace('/\s+/', ' ', $nama)));
    }

    public function render()
    {
        return view('livewire.public.daftar-pemilih');
    }
}
