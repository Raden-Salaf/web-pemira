<div class="min-h-screen">
    <x-public-header />

    <div class="max-w-md mx-auto px-6 py-14">

        @if ($sudahMemilih)
            {{--
                INI SIGNATURE ELEMENT UTAMA: "noda tinta coblos".
                Bentuknya blob organik (bukan lingkaran sempurna) dibuat pakai
                border-radius asimetris, warna ink (ungu tinta), dengan animasi
                ink-stamp yang sudah kita definisikan di app.css -- meniru momen
                jari dicelup tinta setelah mencoblos di TPS sungguhan.
            --}}
            <div class="bg-white border border-navy/15 rounded-2xl p-10 text-center">
                <div class="relative w-24 h-24 mx-auto mb-6">
                    <div class="absolute inset-0 bg-ink animate-ink-stamp"
                         style="border-radius: 42% 58% 63% 37% / 41% 44% 56% 59%;"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white text-3xl">✓</span>
                    </div>
                </div>
                <h1 class="font-display font-semibold text-xl text-navy">Suara Kamu Berhasil Dikirim</h1>
                <p class="text-sm text-slate mt-2">Terima kasih sudah berpartisipasi dalam {{ $pemilihan->nama }}.</p>
            </div>
        @else
            <p class="font-mono text-xs text-ink tracking-widest uppercase mb-3 text-center">
                — Surat Suara
            </p>
            <h1 class="font-display font-semibold text-xl text-navy text-center mb-8">{{ $pemilihan->nama }}</h1>

            <div class="space-y-3">
                @foreach ($paslons as $paslon)
                    <div wire:click="$set('paslonDipilih', {{ $paslon->id }})"
                         class="flex items-center gap-4 p-4 bg-white rounded-2xl border-2 cursor-pointer transition-all
                            {{ $paslonDipilih === $paslon->id ? 'border-blue shadow-lg shadow-blue/10' : 'border-navy/10 hover:border-navy/25' }}">

                        <div class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 bg-navy/5">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($paslon->foto) }}"
                                 class="w-full h-full object-cover" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <span class="font-mono text-xs font-semibold text-ink">No. {{ $paslon->nomor_urut }}</span>
                            <h3 class="font-display font-semibold text-navy truncate">
                                {{ $paslon->nama_ketua }}
                                @if ($paslon->nama_wakil) &amp; {{ $paslon->nama_wakil }} @endif
                            </h3>
                        </div>

                        {{-- Radio indicator custom, jadi "lubang coblos" saat terpilih --}}
                        <div class="w-6 h-6 rounded-full border-2 flex-shrink-0 flex items-center justify-center
                            {{ $paslonDipilih === $paslon->id ? 'border-blue' : 'border-navy/20' }}">
                            @if ($paslonDipilih === $paslon->id)
                                <div class="w-3 h-3 rounded-full bg-blue"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @error('paslonDipilih')
                <p class="text-red-600 text-sm mt-3 text-center">{{ $message }}</p>
            @enderror

            <button wire:click="kirimSuara"
                    wire:confirm="Suara tidak bisa diubah setelah dikirim. Yakin dengan pilihanmu?"
                    wire:loading.attr="disabled"
                    class="w-full mt-6 bg-blue hover:bg-navy text-white font-display font-semibold py-3.5 rounded-xl transition-colors disabled:opacity-50 shadow-lg shadow-blue/20">
                <span wire:loading.remove wire:target="kirimSuara">Kirim Suara Saya</span>
                <span wire:loading wire:target="kirimSuara">Mengirim...</span>
            </button>
        @endif
    </div>
</div>