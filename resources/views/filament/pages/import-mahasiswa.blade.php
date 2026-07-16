<x-filament-panels::page>

    {{-- STEP 1: UPLOAD FILE --}}
    @if ($step === 'upload')
        <div class="space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Upload 1 atau lebih file Excel data mahasiswa. Setiap file idealnya punya
                struktur kolom yang SAMA, karena mapping kolom yang kamu atur nanti akan
                diterapkan ke SEMUA file yang diupload sekaligus.
            </p>

            {{--
                DROPZONE -- ukuran ikon sudah dirapikan (w-5 h-5, sebelumnya w-10/w-8
                yang terlalu dominan), dan warna border/hover disamakan dengan tema
                biru admin panel yang baru (#1E40AF), bukan warna primary Filament default lagi.
            --}}
            <div x-data="{ isDragging: false }" x-on:click="$refs.fileInput.click()" x-on:dragover.prevent="isDragging = true"
                x-on:dragleave.prevent="isDragging = false" x-on:drop.prevent="isDragging = false"
                :class="isDragging ? 'border-[#1E40AF] bg-[#1E40AF]/5' : 'border-gray-300 dark:border-gray-600'"
                class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-6 cursor-pointer transition-colors">

                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Klik di sini untuk memilih file Excel, atau seret file ke area ini
                </p>

                @if (count($files))
                    <p class="text-sm font-medium text-[#1E40AF]">
                        {{ count($files) }} file terpilih
                    </p>
                @endif

                <input type="file" multiple wire:model="files" x-ref="fileInput" class="hidden" />
            </div>

            <div wire:loading wire:target="files" class="text-sm text-[#1E40AF]">
                Mengupload file, mohon tunggu...
            </div>

            @error('files')
                <p class="text-danger-600 text-sm">{{ $message }}</p>
            @enderror
            @error('files.*')
                <p class="text-danger-600 text-sm">{{ $message }}</p>
            @enderror

            <x-filament::button wire:click="lanjutKeMapping" wire:loading.attr="disabled">
                Lanjut ke Mapping Kolom
            </x-filament::button>

            {{-- Tombol reset data, dipisah jauh dari alur utama supaya gak salah pencet --}}
            <div class="pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 mb-2">
                    Ada {{ \App\Models\Mahasiswa::count() }} data mahasiswa tersimpan saat ini.
                </p>
                <x-filament::button color="danger" outlined wire:click="hapusSemuaData"
                    wire:confirm="Yakin mau hapus SEMUA data mahasiswa? Aksi ini tidak bisa dibatalkan.">
                    Hapus Semua Data Mahasiswa
                </x-filament::button>
            </div>
        </div>
    @endif

    {{-- STEP 2: PILIH BARIS HEADER + MAPPING KOLOM --}}
    @if ($step === 'mapping')
        <div class="space-y-6">

            {{-- Preview isi file mentah, biar admin bisa lihat sendiri
                 di baris ke berapa nama kolom yang SEBENARNYA berada --}}
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Baris keberapa yang berisi NAMA KOLOM (NIM, Nama, dst)?
                </label>
                <select wire:model.live="headerRowIndex"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                    @foreach ($previewRows as $index => $row)
                        <option value="{{ $index }}">
                            Baris {{ $index + 1 }}: {{ Str::limit(implode(' | ', array_filter($row)), 80) }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Lihat pratinjau di bawah, pilih baris yang isinya benar-benar nama kolom
                    (misalnya "NIM", "Nama", "Jurusan"), bukan judul/alamat institusi.
                </p>
            </div>

            {{-- Tabel preview 15 baris pertama, biar admin gak "buta" --}}
            <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                <table class="min-w-full text-xs">
                    <tbody>
                        @foreach ($previewRows as $index => $row)
                            <tr
                                class="{{ $index === $headerRowIndex ? 'bg-blue-50 dark:bg-blue-950 font-semibold' : '' }} border-b dark:border-gray-700">
                                <td class="px-2 py-1 text-gray-400">{{ $index + 1 }}</td>
                                @foreach (array_slice($row, 0, 6) as $cell)
                                    <td class="px-2 py-1 whitespace-nowrap">{{ Str::limit((string) $cell, 30) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <hr class="dark:border-gray-700">

            {{-- Kotak input angkatan manual, dipakai untuk file yang tidak
                 punya kolom "Angkatan" sama sekali (angkatan ditentukan oleh
                 FILE itu sendiri, bukan per-baris) --}}
            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Angkatan untuk file ini (isi manual, akan berlaku untuk SEMUA baris)
                </label>
                <input type="text" wire:model="angkatanManual" placeholder="Contoh: 2022"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" />
                <p class="text-xs text-gray-500 mt-1">
                    Kosongkan kalau file kamu memang punya kolom "Angkatan" sendiri (bisa dipetakan di bawah).
                </p>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Sekarang cocokkan kolom Excel dengan field sistem. Contoh data di bawah
                setiap dropdown diambil dari baris tepat setelah header, biar kamu yakin
                pilih kolom yang benar.
            </p>

            @foreach (['nim' => 'NIM (wajib)', 'nama' => 'Nama (wajib)', 'jurusan' => 'Jurusan', 'fakultas' => 'Fakultas', 'angkatan' => 'Angkatan'] as $field => $labelField)
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $labelField }}</label>
                    <select wire:model="mapping.{{ $field }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        <option value="">-- Tidak digunakan --</option>
                        @foreach ($detectedColumns as $index => $kolom)
                            <option value="{{ $index }}">
                                Kolom {{ $index + 1 }}: {{ Str::limit((string) $kolom, 25) }}
                                @if (isset($sampleValues[$index]))
                                    (contoh: "{{ Str::limit((string) $sampleValues[$index], 20) }}")
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('mapping.' . $field)
                        <p class="text-danger-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <x-filament::button wire:click="prosesImport" wire:loading.attr="disabled">
                Import Sekarang
            </x-filament::button>

            <x-filament::button color="gray" wire:click="importLagi">
                Batal, Upload Ulang
            </x-filament::button>
        </div>
    @endif

    {{-- STEP 3: HASIL --}}
    @if ($step === 'selesai')
        <div class="space-y-4">
            <div class="p-4 rounded-lg bg-success-50 dark:bg-success-950 text-success-700 dark:text-success-300">
                Import selesai! <strong>{{ $hasilImport['berhasil'] }}</strong> data mahasiswa berhasil diproses,
                <strong>{{ $hasilImport['dilewati'] }}</strong> baris dilewati (NIM/Nama kosong).
            </div>

            {{-- Preview hasil, biar admin gak perlu buka Tinker buat verifikasi --}}
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Contoh 5 data yang tersimpan:
                </p>
                <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-3 py-2 text-left">NIM</th>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Jurusan</th>
                                <th class="px-3 py-2 text-left">Fakultas</th>
                                <th class="px-3 py-2 text-left">Angkatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previewHasil as $mhs)
                                <tr class="border-t dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $mhs['nim'] }}</td>
                                    <td class="px-3 py-2">{{ $mhs['nama'] }}</td>
                                    <td class="px-3 py-2">{{ $mhs['jurusan'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $mhs['fakultas'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $mhs['angkatan'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <x-filament::button wire:click="importLagi">
                Import File Lain
            </x-filament::button>
        </div>
    @endif

</x-filament-panels::page>
