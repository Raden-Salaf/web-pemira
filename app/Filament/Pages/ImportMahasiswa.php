<?php

namespace App\Filament\Pages;

use App\Imports\ExcelRawImport;
use App\Models\Mahasiswa;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportMahasiswa extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Import Data Mahasiswa';
    protected static ?string $title = 'Import Data Mahasiswa dari Excel';

    protected string $view = 'filament.pages.import-mahasiswa';

    public array $files = [];
    public string $step = 'upload';

    // Menyimpan 10 baris PERTAMA file (mentah, apa adanya) supaya admin
    // bisa lihat isinya dan menentukan sendiri baris mana yang benar-benar
    // berisi nama kolom (NIM, Nama, dst) -- karena file ekspor dari sistem
    // akademik SERINGKALI punya baris judul/alamat/logo di atas baris header asli.
    public array $previewRows = [];

    // Baris ke berapa (index mulai dari 0) yang dipilih admin sebagai HEADER
    public int $headerRowIndex = 0;

    public array $detectedColumns = [];

    // Contoh data di baris SETELAH header, ditampilkan di sebelah dropdown mapping
    // supaya admin gak perlu nebak-nebak "Kolom 3 ini isinya apa sih sebenarnya"
    public array $sampleValues = [];

    public array $mapping = [
        'nim' => null,
        'nama' => null,
        'jurusan' => null,
        'fakultas' => null,
        'angkatan' => null,
    ];

    // Untuk kasus file yang gak punya kolom "Angkatan" sama sekali (angkatan
    // ditentukan dari FILE-nya, bukan dari isi baris) -- admin isi manual di sini,
    // dan nilai ini akan diterapkan ke SEMUA baris pada file yang sedang diproses.
    public string $angkatanManual = '';
    public ?array $hasilImport = null;
    public array $storedPaths = [];

    // Preview 5 data mahasiswa TERAKHIR yang berhasil diimport, ditampilkan
    // di step akhir supaya admin bisa langsung verifikasi hasilnya tanpa buka Tinker
    public array $previewHasil = [];

    public function lanjutKeMapping(): void
    {
        $this->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|extensions:xlsx,xls,csv|max:20480',
        ]);

        $this->storedPaths = collect($this->files)
            ->map(fn($file) => $file->store('imports-mahasiswa', 'local'))
            ->toArray();

        $pathAbsolut = Storage::disk('local')->path($this->storedPaths[0]);
        $data = Excel::toArray(new ExcelRawImport, $pathAbsolut);
        $rows = $data[0] ?? [];

        if (empty($rows)) {
            Notification::make()->title('File Excel kosong atau tidak terbaca')->danger()->send();
            return;
        }

        // Ambil maksimal 10 baris pertama untuk PREVIEW saja (bukan proses import),
        // supaya admin bisa "melihat" isi file dan menentukan baris header yang benar
        $this->previewRows = array_slice($rows, 0, 15);

        // Tebakan awal: anggap baris pertama (index 0) sebagai header.
        // Admin BISA ganti ini manual di step berikutnya kalau ternyata salah.
        $this->headerRowIndex = 0;
        $this->perbaruiHeaderDanSample($rows);

        $this->step = 'mapping';
    }

    /**
     * Dipanggil otomatis setiap admin GANTI pilihan baris header di dropdown.
     * Livewire otomatis memanggil method bernama "updated{NamaProperty}"
     * setiap kali property publik itu berubah nilainya dari sisi frontend.
     */
    public function updatedHeaderRowIndex(): void
    {
        $pathAbsolut = Storage::disk('local')->path($this->storedPaths[0]);
        $data = Excel::toArray(new ExcelRawImport, $pathAbsolut);
        $rows = $data[0] ?? [];

        $this->perbaruiHeaderDanSample($rows);
    }

    /**
     * Helper: set ulang $detectedColumns (nama-nama kolom untuk dropdown mapping)
     * dan $sampleValues (contoh data di baris setelah header) berdasarkan
     * $headerRowIndex yang sedang aktif.
     */
    private function perbaruiHeaderDanSample(array $rows): void
    {
        $this->detectedColumns = $rows[$this->headerRowIndex] ?? [];
        $this->sampleValues = $rows[$this->headerRowIndex + 1] ?? [];
    }

    public function prosesImport(): void
    {
        $this->validate([
            'mapping.nim' => 'required',
            'mapping.nama' => 'required',
        ]);

        $berhasil = 0;
        $dilewati = 0;

        foreach ($this->storedPaths as $path) {
            $pathAbsolut = Storage::disk('local')->path($path);
            $data = Excel::toArray(new ExcelRawImport, $pathAbsolut);
            $rows = $data[0] ?? [];

            // Data dimulai SETELAH baris header yang admin pilih,
            // bukan selalu dari index 1 seperti versi sebelumnya
            foreach (array_slice($rows, $this->headerRowIndex + 1) as $row) {
                // ltrim($string, "'") membuang tanda kutip satu (') HANYA kalau itu ada
                // di paling depan string -- ini artifact umum dari file Excel/HTML yang
                // sengaja menambahkan tanda kutip di depan angka supaya diperlakukan
                // sebagai teks (misal biar angka 0 di depan NIM gak hilang).
                $nim = ltrim(trim((string) ($row[$this->mapping['nim']] ?? '')), "'");
                $nama = trim((string) ($row[$this->mapping['nama']] ?? ''));

                if ($nim === '' || $nama === '') {
                    $dilewati++;
                    continue;
                }

                Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'nama' => $nama,
                        'jurusan' => $this->ambilKolom($row, 'jurusan'),
                        'fakultas' => $this->ambilKolom($row, 'fakultas'),
                        'angkatan' => $this->ambilKolom($row, 'angkatan'),
                    ]
                );
                $berhasil++;
            }
        }

        $this->hasilImport = ['berhasil' => $berhasil, 'dilewati' => $dilewati];

        // Ambil 5 data TERBARU untuk preview visual di step "selesai",
        // supaya admin gak perlu buka Tinker cuma buat ngecek hasil import
        $this->previewHasil = Mahasiswa::latest('updated_at')->limit(5)->get()->toArray();

        $this->step = 'selesai';

        Notification::make()->title("Import selesai: {$berhasil} data diproses")->success()->send();
    }

    private function ambilKolom(array $row, string $field): ?string
    {
        // KHUSUS field 'angkatan': kalau admin isi nilai manual, PRIORITASKAN itu
        // daripada coba baca dari kolom Excel (karena banyak file gak punya
        // kolom angkatan sama sekali -- angkatan ditentukan oleh file itu sendiri)
        if ($field === 'angkatan' && trim($this->angkatanManual) !== '') {
            return trim($this->angkatanManual);
        }

        $index = $this->mapping[$field] ?? null;

        if ($index === null || $index === '') {
            return null;
        }

        $value = trim((string) ($row[$index] ?? ''));

        return $value !== '' ? $value : null;
    }

    /**
     * FITUR BARU: hapus SEMUA data mahasiswa, biasanya dipakai kalau
     * hasil import sebelumnya salah/berantakan dan admin mau mulai ulang dari nol.
     *
     * Pakai query()->delete() (bukan truncate()), karena truncate() akan
     * DITOLAK MySQL selama tabel "mahasiswas" masih dirujuk oleh foreign key
     * di tabel "pemilihs" -- walau tabel pemilihs itu sendiri kosong.
     * delete() biasa tetap aman dijalankan karena dia cek baris satu-satu,
     * bukan "membekukan" cek foreign key sekaligus seperti truncate().
     */
    public function hapusSemuaData(): void
    {
        $jumlah = Mahasiswa::count();
        Mahasiswa::query()->delete();

        Notification::make()
            ->title("Berhasil menghapus {$jumlah} data mahasiswa")
            ->warning()
            ->send();

        $this->importLagi();
    }

    public function importLagi(): void
    {
        $this->step = 'upload';
        $this->files = [];
        $this->storedPaths = [];
        $this->previewRows = [];
        $this->headerRowIndex = 0;
        $this->detectedColumns = [];
        $this->sampleValues = [];
        $this->mapping = ['nim' => null, 'nama' => null, 'jurusan' => null, 'fakultas' => null, 'angkatan' => null];
        $this->hasilImport = null;
        $this->previewHasil = [];
    }
}
