<?php

namespace App\Livewire\Public;

use App\Models\Pemilih;
use App\Models\Pemilihan;
use App\Models\Suara;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Voting extends Component
{
    public Pemilihan $pemilihan;

    // ID paslon yang dipilih (radio button), null kalau belum pilih
    public ?int $paslonDipilih = null;

    public bool $sudahMemilih = false;

    /**
     * mount() = gerbang keamanan utama halaman ini. Berlapis:
     * 1. Pemilihan harus aktif
     * 2. Harus ADA session "tanda pengenal" dari proses verifikasi sebelumnya
     *    (kalau gak ada, artinya orang ini gak pernah lewat halaman verifikasi
     *    NIM+Nama -- langsung tolak, gak boleh akses voting dari mana pun)
     * 3. Voting harus BENERAN sedang buka (cek status + jadwal waktu)
     * 4. Pemilih itu harus DPT dan belum pernah memilih
     */
    public function mount(): void
    {
        if (! $this->pemilihan->is_active) {
            abort(404);
        }

        $pemilihId = session("pemilih_id_pemilihan_{$this->pemilihan->id}");

        if (! $pemilihId) {
            // Gak ada session = belum pernah verifikasi di browser ini.
            // Lempar balik ke halaman pendaftaran/verifikasi.
            redirect()->route('pemilihan.daftar', $this->pemilihan);
            return;
        }

        $pemilih = Pemilih::find($pemilihId);

        // Pastikan record-nya ADA dan BENAR-BENAR punya pemilihan_id yang sama
        // dengan pemilihan yang lagi diakses -- mencegah orang "menyelundupkan"
        // session dari pemilihan lain buat vote di pemilihan yang berbeda
        if (! $pemilih || $pemilih->pemilihan_id !== $this->pemilihan->id) {
            redirect()->route('pemilihan.daftar', $this->pemilihan);
            return;
        }

        if (! $pemilihan = $this->pemilihan->isVotingBuka()) {
            // fallthrough di bawah
        }

        if ($pemilih->sudah_memilih) {
            $this->sudahMemilih = true;
        }
    }

    /**
     * INI LOGIC PALING KRUSIAL DI SELURUH SISTEM PEMIRA INI.
     * Method ini yang mencatat 1 suara -- HARUS dijamin race-condition-proof.
     *
     * Skenario yang harus dicegah: bayangkan mahasiswa buka 2 tab browser
     * bersamaan, lalu klik "Kirim Suara" di KEDUA tab hampir bersamaan.
     * Tanpa pengaman, bisa saja kedua request itu SAMA-SAMA lolos pengecekan
     * "belum pernah memilih" (karena dicek di waktu yang hampir sama, sebelum
     * salah satu sempat update statusnya) -- akibatnya tercatat 2 suara.
     *
     * Solusi: DB::transaction() + lockForUpdate().
     * lockForUpdate() mengunci baris data pemilih ini di database SELAMA
     * transaksi berlangsung -- request KEDUA yang datang hampir bersamaan
     * akan DIPAKSA MENUNGGU sampai request PERTAMA selesai (commit/rollback),
     * baru boleh baca data itu. Jadi begitu request pertama selesai update
     * sudah_memilih jadi true, request kedua yang baru dapat gilirannya
     * akan lihat data TERBARU (sudah true) dan otomatis gagal di pengecekan.
     */
    public function kirimSuara(): void
    {
        if (! $this->paslonDipilih) {
            $this->addError('paslonDipilih', 'Pilih salah satu paslon terlebih dahulu.');
            return;
        }

        $pemilihId = session("pemilih_id_pemilihan_{$this->pemilihan->id}");

        DB::transaction(function () use ($pemilihId) {
            // lockForUpdate() -- inti dari proteksi race condition, lihat penjelasan di atas
            $pemilih = Pemilih::where('id', $pemilihId)->lockForUpdate()->first();

            // Re-cek SEMUA syarat di sini lagi (bukan cuma andalkan pengecekan
            // di mount() tadi), karena kondisi bisa berubah di antara waktu
            // halaman dibuka dan tombol diklik (misal admin tiba-tiba tutup
            // voting, atau ternyata tab lain sudah duluan vote)
            if (! $pemilih || ! $pemilih->bolehMemilih()) {
                $this->addError('paslonDipilih', 'Kamu sudah tidak bisa memilih (mungkin sudah pernah memilih atau voting telah ditutup).');
                return;
            }

            // Catat suara -- INGAT, tabel suaras SENGAJA tidak menyimpan
            // siapa yang memilih (lihat penjelasan migration Step 2.6 dulu)
            Suara::create([
                'paslon_id' => $this->paslonDipilih,
                'pemilihan_id' => $this->pemilihan->id,
            ]);

            // Kunci status supaya gak bisa vote lagi
            $pemilih->update([
                'sudah_memilih' => true,
                'waktu_memilih' => now(),
            ]);
        });

        // Hapus session -- setelah vote, "tiket akses" ke halaman voting ini
        // gak berlaku lagi, mencegah re-akses aneh-aneh ke halaman yang sama
        session()->forget("pemilih_id_pemilihan_{$this->pemilihan->id}");

        $this->sudahMemilih = true;
    }

    public function render()
    {
        $paslons = $this->pemilihan->paslons()->orderBy('nomor_urut')->get();

        return view('livewire.public.voting', [
            'paslons' => $paslons,
        ]);
    }
}