<div class="text-center mb-8 pt-4">
    {{--
        Logo: cincin gradient biru-indigo yang BERPUTAR pelan terus-menerus
        (bukan statis), dengan titik tengah yang berdenyut -- kombinasi
        2 animasi beda kecepatan bikin logo terasa "hidup" tanpa berlebihan.
    --}}
    <div class="relative inline-flex items-center justify-center w-16 h-15 mb-4">
        <div class="absolute inset-0 rounded-full animate-spin"
             style="background: conic-gradient(from 0deg, #3B82F6, #6366F1, #06B6D4, #3B82F6); animation-duration: 6s; padding: 2px;">
            <div class="w-full h-full rounded-full bg-white"></div>
        </div>
        <div class="relative w-3.5 h-3.5 rounded-full animate-pulse bg-blue-700"></div>
    </div>

    {{-- Judul pakai gradient text -- teksnya sendiri yang berwarna gradasi,
         detail kecil yang langsung bikin kesan "modern SaaS product" --}}
    <h1 class="font-display font-bold text-2xl tracking-tight"
        style="background: linear-gradient(135deg, #1E40AF, #6366F1); -webkit-background-clip: text; background-clip: text; color: transparent;">
        Pemira Online
    </h1>
    <p class="font-mono text-[10px] text-blue-600/70 mt-1 tracking-widest uppercase">
        Panel Administrator
    </p>

    <p class="text-sm text-slate-500 mt-4 italic leading-relaxed px-4">
        "REVOLUSI TEKNIK"
    </p>
</div>