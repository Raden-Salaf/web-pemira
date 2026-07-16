<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Pemilihan</label>
            <select wire:model.live="pemilihanId"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                <option value="">-- Pilih pemilihan --</option>
                @foreach ($this->pemilihanList as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>

        @if ($this->pemilihanTerpilih)
            @if ($this->totalSuara === 0)
                <div class="border-2 border-dashed rounded-xl py-16 text-center text-gray-400 dark:border-gray-700">
                    Belum ada suara yang masuk untuk pemilihan ini.
                </div>
            @else
                <div class="flex justify-end">
                    <x-filament::button wire:click="unduhPdf" icon="heroicon-o-document-arrow-down"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="unduhPdf">Unduh Laporan PDF</span>
                        <span wire:loading wire:target="unduhPdf">Menyiapkan...</span>
                    </x-filament::button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($this->paslons as $index => $paslon)
                        <div
                            class="p-5 rounded-xl border bg-white dark:bg-gray-800 dark:border-gray-700
                                    {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}">
                            @if ($index === 0)
                                <span
                                    class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">UNGGUL</span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">No. {{ $paslon->nomor_urut }}</p>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $paslon->nama_ketua }}
                                @if ($paslon->nama_wakil)
                                    & {{ $paslon->nama_wakil }}
                                @endif
                            </h3>
                            <div class="flex items-end justify-between mt-3">
                                <span class="text-2xl font-bold text-blue-700">{{ $paslon->jumlah }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $paslon->persentase }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{--
                    PIE CHART pakai Chart.js + plugin chartjs-plugin-datalabels
                    (buat nampilin angka persentase LANGSUNG di tiap potongan pie).
                    Container dikasih tinggi TETAP (h-80) supaya canvas gak
                    "meledak" ngikutin ukuran natural gambar.
                --}}
                <div wire:key="chart-{{ $pemilihanId }}" x-data="{
                    init() {
                        new Chart(this.$refs.canvas, {
                            type: 'pie',
                            data: {
                                labels: @js($this->chartData['labels']),
                                datasets: [{
                                    data: @js($this->chartData['values']),
                                    backgroundColor: @js($this->chartData['colors']),
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom' },
                                    datalabels: {
                                        color: 'white',
                                        font: { weight: 'bold', size: 13 },
                                        formatter: (value, context) => {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            if (total === 0) return '0%';
                                            return (value / total * 100).toFixed(1) + '%';
                                        }
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });
                    }
                }"
                    class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-6 max-w-md mx-auto">
                    <div class="h-80">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    @endpush
</x-filament-panels::page>
