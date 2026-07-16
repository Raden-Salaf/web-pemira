<div class="min-h-screen">
    <x-public-header />

    <div class="max-w-3xl mx-auto px-6 py-14">

        {{-- Hero pemilihan --}}
        <div class="mb-12">
            <p class="font-mono text-xs text-ink tracking-widest uppercase mb-3">
                — {{ $pemilihan->jenis }}
            </p>
            <h1 class="font-display font-semibold text-3xl text-navy leading-tight">
                {{ $pemilihan->nama }}
            </h1>
            @if ($pemilihan->deskripsi)
                <p class="text-slate mt-3 max-w-lg">{{ $pemilihan->deskripsi }}</p>
            @endif
        </div>

        @if ($paslons->isEmpty())
            <div class="border-2 border-dashed border-slate/30 rounded-2xl py-20 text-center">
                <p class="text-slate">Belum ada paslon yang terdaftar untuk pemilihan ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach ($paslons as $paslon)
                    <div class="bg-white border border-navy/15 rounded-2xl overflow-hidden">

                        {{--
                            KUNCI JAWABAN PERTANYAAN KAMU ADA DI SINI:

                            "aspect-[4/5]" -- ini utility Tailwind yang MEMAKSA kotak
                            ini selalu punya rasio lebar:tinggi 4:5 (potret), APAPUN
                            ukuran asli foto yang diupload admin (entah itu 500x500,
                            1920x1080, atau 800x2000 sekalipun).

                            "object-cover" pada <img> di dalamnya yang melakukan
                            "pemotongan otomatis" -- foto akan di-scale sampai
                            MENGISI PENUH kotak itu, dan bagian yang kelebihan
                            (entah di kiri-kanan atau atas-bawah) otomatis
                            "terpotong" (invisible, di-crop), TANPA foto jadi
                            gepeng/melar tidak proporsional.

                            Ini beda dengan "object-contain" yang malah nyisain
                            area kosong/blank kalau rasio foto beda dengan kotaknya.
                        --}}
                        <div class="relative aspect-[4/5] bg-navy/5 overflow-hidden">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($paslon->foto) }}"
                                alt="Foto {{ $paslon->nama_ketua }}" class="w-full h-full object-cover" />

                            {{-- Badge nomor urut, mengambang di pojok kayak "segel" resmi --}}
                            <div
                                class="absolute top-3 left-3 w-11 h-11 rounded-full bg-navy border-2 border-white shadow-lg flex items-center justify-center">
                                <span class="font-mono font-bold text-white text-sm">{{ $paslon->nomor_urut }}</span>
                            </div>
                        </div>

                        <div class="p-5">
                            <h2 class="font-display font-semibold text-lg text-navy">
                                {{ $paslon->nama_ketua }}
                                @if ($paslon->nama_wakil)
                                    <span class="text-slate font-normal">&amp; {{ $paslon->nama_wakil }}</span>
                                @endif
                            </h2>
                            @if ($paslon->fakultas_asal)
                                <p class="font-mono text-xs text-ink mt-1">{{ $paslon->fakultas_asal }}</p>
                            @endif

                            <div class="mt-4 pt-4 border-t border-navy/10 space-y-4">
                                <div>
                                    <h3
                                        class="font-mono text-[10px] font-semibold text-slate uppercase tracking-wider mb-1">
                                        Visi</h3>
                                    <p class="text-sm text-navy/80 leading-relaxed">{{ $paslon->visi }}</p>
                                </div>

                                <div>
                                    <h3
                                        class="font-mono text-[10px] font-semibold text-slate uppercase tracking-wider mb-1">
                                        Misi</h3>
                                    <p class="text-sm text-navy/80 leading-relaxed">{{ $paslon->misi }}</p>
                                </div>

                                @if (!empty($paslon->program_kerja))
                                    <div>
                                        <h3
                                            class="font-mono text-[10px] font-semibold text-slate uppercase tracking-wider mb-1">
                                            Program Kerja</h3>
                                        <ul class="text-sm text-navy/80 space-y-1">
                                            @foreach ($paslon->program_kerja as $item)
                                                <li class="flex gap-2">
                                                    <span class="text-ink flex-shrink-0">•</span>
                                                    <span>{{ $item['poin'] ?? '' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Status banner + tombol (logic sama seperti sebelumnya, cuma styling diperbarui) --}}
        <div class="text-center mt-12">
            @if ($pemilihan->sudahBerakhir())
                <div class="inline-block bg-slate/10 text-slate px-4 py-2 rounded-full text-sm font-mono mb-4">
                    ⏱ Pemungutan suara telah berakhir
                </div>
                <br>
                <span
                    class="inline-block bg-slate/20 text-slate font-display font-medium px-8 py-3.5 rounded-xl cursor-not-allowed">
                    Daftar untuk Memilih
                </span>
            @elseif ($pemilihan->belumMulai())
                <div class="inline-block bg-ink/10 text-ink px-4 py-2 rounded-full text-sm font-mono mb-4">
                    🕒 Dibuka {{ $pemilihan->waktu_mulai->translatedFormat('d F Y, H:i') }} WIB
                </div>
                <br>
                <span
                    class="inline-block bg-slate/20 text-slate font-display font-medium px-8 py-3.5 rounded-xl cursor-not-allowed">
                    Daftar untuk Memilih
                </span>
            @else
                <a href="{{ route('pemilihan.daftar', $pemilihan) }}"
                    class="inline-block bg-blue hover:bg-navy text-white font-display font-medium px-8 py-3.5 rounded-xl transition-colors shadow-lg shadow-blue/20">
                    Daftar untuk Memilih →
                </a>
            @endif
        </div>
    </div>
</div>
