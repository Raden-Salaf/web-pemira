<div class="min-h-screen">
    <x-public-header />

    <div class="max-w-md mx-auto px-6 py-14">
        <p class="font-mono text-xs text-ink tracking-widest uppercase mb-3 text-center">
            — Verifikasi Pemilih
        </p>
        <h1 class="font-display font-semibold text-2xl text-navy text-center mb-1">{{ $pemilihan->nama }}</h1>
        <p class="text-sm text-slate text-center mb-10">Masukkan NIM dan nama untuk memeriksa status kamu</p>

        @if (!$pemilihSaya)
            <form wire:submit="daftar" class="bg-white border border-navy/15 rounded-2xl p-6 space-y-5">
                <div>
                    <label
                        class="block font-mono text-xs font-semibold text-navy uppercase tracking-wide mb-2">NIM</label>
                    <input type="text" wire:model="nim"
                        class="w-full rounded-xl border-navy/20 font-mono focus:border-blue focus:ring-blue/20" />
                    @error('nim')
                        <p class="text-red-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-mono text-xs font-semibold text-navy uppercase tracking-wide mb-2">Nama
                        Lengkap</label>
                    <input type="text" wire:model="nama"
                        class="w-full rounded-xl border-navy/20 focus:border-blue focus:ring-blue/20" />
                    @error('nama')
                        <p class="text-red-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate mt-1.5">Besar/kecil huruf tidak masalah, cukup sesuai nama yang
                        terdaftar.</p>
                </div>

                @if ($pesanError)
                    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $pesanError }}
                    </div>
                @endif

                <button type="submit"
                    class="w-full bg-blue hover:bg-navy text-white font-display font-medium py-3.5 rounded-xl transition-colors disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="daftar">Periksa Status</span>
                    <span wire:loading wire:target="daftar">Memeriksa...</span>
                </button>
            </form>
        @else
            <div class="bg-white border border-navy/15 rounded-2xl p-8 text-center">

                {{-- KASUS 1: DPS --}}
                @if ($pemilihSaya->status === 'dps')
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-ink/10 mb-4">
                        <span class="text-2xl">⏳</span>
                    </div>
                    <h2 class="font-display font-semibold text-navy text-lg">Menunggu Verifikasi</h2>
                    <p class="text-sm text-slate mt-2 leading-relaxed">
                        Pendaftaran kamu tercatat sebagai <span class="font-mono font-medium text-ink">DPS</span> —
                        Daftar Pemilih Sementara. Admin akan segera memverifikasi data kamu.
                    </p>

                    {{-- KASUS 2: DPT --}}
                @elseif ($pemilihSaya->status === 'dpt')
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/10 mb-4">
                        <span class="text-2xl">✅</span>
                    </div>
                    <h2 class="font-display font-semibold text-navy text-lg">Kamu Terdaftar sebagai DPT</h2>
                    <p class="text-sm text-slate mt-2 leading-relaxed">
                        Selamat! Kamu masuk <span class="font-mono font-medium text-success">DPT</span> —
                        Daftar Pemilih Tetap, dan berhak mencoblos.
                    </p>

                    @if ($pemilihSaya->sudah_memilih)
                        <p class="mt-5 text-sm font-medium text-ink">
                            Kamu sudah mencoblos di pemilihan ini. Terima kasih atas partisipasimu!
                        </p>
                    @elseif ($pemilihan->isVotingBuka())
                        <a href="{{ route('pemilihan.voting', $pemilihan) }}"
                            class="inline-block mt-5 bg-blue hover:bg-navy text-white font-display font-medium px-8 py-3 rounded-xl transition-colors shadow-lg shadow-blue/20">
                            Mulai Memilih →
                        </a>
                    @else
                        <p class="mt-5 text-sm font-mono text-ink">
                            Pemungutan suara belum dibuka atau sudah berakhir.
                        </p>
                    @endif

                    {{-- KASUS 3: Ditolak --}}
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                        <span class="text-2xl">❌</span>
                    </div>
                    <h2 class="font-display font-semibold text-navy text-lg">Pendaftaran Ditolak</h2>
                    <p class="text-sm text-slate mt-2 leading-relaxed">
                        Mohon maaf, pendaftaran kamu ditolak oleh admin. Silakan hubungi panitia pemira.
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>
