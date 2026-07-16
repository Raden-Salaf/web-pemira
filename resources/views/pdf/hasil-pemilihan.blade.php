<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* dompdf gak render Tailwind, jadi kita tulis CSS polos di sini */
        body { font-family: sans-serif; color: #1e293b; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { color: #64748b; font-size: 11px; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 12px; }
        th { background: #f1f5f9; }
        .bar-bg { background: #e2e8f0; height: 10px; border-radius: 4px; }
        .bar-fill { background: #1e40af; height: 10px; border-radius: 4px; }
        .footer { margin-top: 40px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Berita Acara Hasil Perolehan Suara</h1>
    <p class="subtitle">{{ $pemilihan->nama }} — Total suara masuk: {{ $totalSuara }}</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Paslon</th>
                <th>Jumlah Suara</th>
                <th>Persentase</th>
                <th style="width: 30%;">Grafik</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paslons as $paslon)
                <tr>
                    <td>{{ $paslon->nomor_urut }}</td>
                    <td>
                        {{ $paslon->nama_ketua }}
                        @if ($paslon->nama_wakil) & {{ $paslon->nama_wakil }} @endif
                    </td>
                    <td>{{ $paslon->jumlah }}</td>
                    <td>{{ $paslon->persentase }}%</td>
                    <td>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width: {{ $paslon->persentase }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Dokumen ini dibuat otomatis oleh Sistem Pemira Online pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
    </p>
</body>
</html>