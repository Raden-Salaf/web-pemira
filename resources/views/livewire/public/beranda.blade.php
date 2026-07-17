<div class="min-h-screen">

    {{-- HEADER: identitas resmi, navy tegas, kesan "lembaga" --}}
    <x-public-header />

    <div class="max-w-3xl mx-auto px-6 py-14">

        {{-- HERO: tegas & langsung, bukan basa-basi --}}
        <div class="mb-12">
            <p class="font-mono text-xs text-ink tracking-widest uppercase mb-3">
                — Suaramu yang menentukan masa depan
            </p>
            <h1 class="font-display font-semibold text-3xl md:text-4xl text-navy leading-tight">
                Pilih pemilihan yang<br>ingin kamu ikuti.
            </h1>
        </div>

        @if ($pemilihans->isEmpty())
            <div class="border-2 border-dashed border-slate/30 rounded-2xl py-20 text-center">
                <p class="text-slate">Belum ada pemilihan yang sedang berlangsung saat ini.</p>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($pemilihans as $pemilihan)
                    <a href="{{ route('pemilihan.profil', $pemilihan) }}"
                       class="group relative block">

                        {{--
                            EFEK "TUMPUKAN KERTAS": 2 layer di belakang kartu utama,
                            sedikit miring & offset -- meniru tumpukan kertas suara.
                            Cuma dekorasi visual (pointer-events-none), gak ganggu klik.
                        --}}
                        <div class="absolute inset-0 bg-white border border-navy/10 rounded-2xl rotate-1 translate-x-1 translate-y-1 pointer-events-none"></div>
                        <div class="absolute inset-0 bg-white border border-navy/10 rounded-2xl -rotate-1 -translate-x-0.5 translate-y-0.5 pointer-events-none"></div>

                        <div class="relative bg-white border border-navy/15 rounded-2xl p-5 flex items-center gap-4
                                    transition-all duration-200 group-hover:border-blue group-hover:shadow-lg group-hover:-translate-y-0.5">

                            {{-- "Lubang coblos" -- nomor urut/jenis pemilihan dalam
                                 lingkaran mirip lubang di kertas suara asli --}}
                            <div class="flex-shrink-0 w-14 h-14 rounded-full bg-navy flex items-center justify-center">
                                <span class="font-mono text-white text-[10px] font-semibold uppercase tracking-wide">
                                    {{ Str::limit($pemilihan->jenis, 4, '') }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    @if ($pemilihan->sudahBerakhir())
                                        <span class="font-mono text-[10px] font-medium bg-slate/10 text-slate px-2 py-0.5 rounded-full">
                                            SELESAI
                                        </span>
                                    @elseif ($pemilihan->belumMulai())
                                        <span class="font-mono text-[10px] font-medium bg-ink/10 text-ink px-2 py-0.5 rounded-full">
                                            BELUM DIMULAI
                                        </span>
                                    @elseif ($pemilihan->isVotingBuka())
                                        <span class="font-mono text-[10px] font-medium bg-success/10 text-success px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success animate-pulse"></span>
                                            BERLANGSUNG
                                        </span>
                                    @else
                                        <span class="font-mono text-[10px] font-medium bg-slate/10 text-slate px-2 py-0.5 rounded-full">
                                            PERSIAPAN
                                        </span>
                                    @endif
                                </div>

                                <h2 class="font-display font-semibold text-navy truncate">{{ $pemilihan->nama }}</h2>
                                <p class="text-sm text-slate mt-0.5">{{ $pemilihan->paslons_count }} paslon terdaftar</p>
                            </div>

                            <span class="font-display text-blue text-xl flex-shrink-0 transition-transform group-hover:translate-x-1">→</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>