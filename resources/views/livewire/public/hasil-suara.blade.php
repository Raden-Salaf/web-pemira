<div class="min-h-screen">
    <x-public-header />

    <div class="max-w-md mx-auto px-6 py-14">
        <p class="font-mono text-xs text-ink tracking-widest uppercase mb-3 text-center">
            — Hasil Sementara
        </p>
        <h1 class="font-display font-semibold text-2xl text-navy text-center mb-1">{{ $pemilihan->nama }}</h1>
        <p class="text-sm text-slate text-center mb-10">
            Total suara masuk: <span class="font-mono font-semibold text-navy">{{ $totalSuara }}</span>
        </p>

        @if ($totalSuara === 0)
            <div class="border-2 border-dashed border-slate/30 rounded-2xl py-16 text-center">
                <p class="text-slate">Belum ada suara yang masuk.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($paslons as $index => $paslon)
                    <div class="bg-white rounded-2xl border border-navy/15 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                @if ($index === 0 && $paslon->jumlah > 0)
                                    <span class="text-xs font-mono font-semibold bg-ink/10 text-ink px-2 py-0.5 rounded-full">
                                        UNGGUL
                                    </span>
                                @endif
                                <span class="font-display font-semibold text-navy text-sm">
                                    No. {{ $paslon->nomor_urut }} — {{ $paslon->nama_ketua }}
                                </span>
                            </div>
                            <span class="font-mono font-bold text-blue">{{ $paslon->persentase }}%</span>
                        </div>

                        <div class="w-full bg-navy/5 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-blue h-2.5 rounded-full transition-all duration-700"
                                 style="width: {{ $paslon->persentase }}%"></div>
                        </div>

                        <p class="font-mono text-xs text-slate mt-1.5">{{ $paslon->jumlah }} suara</p>
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-center text-slate mt-8 font-mono">
                Hasil real-time, berubah selama pemungutan suara berlangsung.
            </p>
        @endif
    </div>
</div>