<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Pemilihan</label>
            <select wire:model.live="pemilihanId"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                <option value="">-- Pilih pemilihan bermode manual --</option>
                @foreach ($this->pemilihanList as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            @if ($this->pemilihanList->isEmpty())
                <p class="text-xs text-amber-600 mt-1">
                    Belum ada pemilihan dengan sumber pemilih "Manual". Ubah dulu di halaman Pemilihans.
                </p>
            @endif
        </div>

        @if ($pemilihanId)
            <form wire:submit="tambahKeWhitelist" class="flex gap-3 items-end p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex-1">
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">NIM</label>
                    <input type="text" wire:model="nim"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" />
                </div>
                <div class="flex-1">
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Nama</label>
                    <input type="text" wire:model="nama"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" />
                </div>
                <x-filament::button type="submit">Tambah</x-filament::button>
            </form>
            @error('nim')
                <p class="text-danger-600 text-sm">{{ $message }}</p>
            @enderror
            @error('nama')
                <p class="text-danger-600 text-sm">{{ $message }}</p>
            @enderror

            <div class="border rounded-lg dark:border-gray-700 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">NIM</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->whitelist as $mhs)
                            <tr class="border-t dark:border-gray-700">
                                <td class="px-4 py-2 font-mono">{{ $mhs->nim }}</td>
                                <td class="px-4 py-2">{{ $mhs->nama }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button wire:click="hapusDariWhitelist({{ $mhs->id }})"
                                        wire:confirm="Hapus {{ $mhs->nama }} dari whitelist?"
                                        class="text-red-600 text-xs hover:underline">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-400">Whitelist masih kosong.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
