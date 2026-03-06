<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <flux:button href="{{ route('admin.vehicles.index') }}" variant="ghost" icon="arrow-left" class="mb-4">
            Kembali
        </flux:button>
        
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Tambah Kendaraan Baru
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Tambahkan kendaraan operasional baru
        </p>
    </div>

    {{-- Form Card --}}
    <div class="max-w-2xl bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6">
            <x-form wire:submit="save" class="space-y-6">
                
                {{-- License Plate --}}
                <flux:input 
                    wire:model="license_plate" 
                    label="Plat Nomor" 
                    placeholder="Contoh: B 1234 XYZ"
                />

                {{-- Current Mileage --}}
                <flux:input 
                    wire:model="current_mileage" 
                    type="number" 
                    label="Kilometer Saat Ini" 
                    placeholder="Masukkan kilometer"
                />

                {{-- Status --}}
                <flux:select wire:model="status" label="Status">
                    <flux:select.option value="available">Tersedia</flux:select.option>
                    <flux:select.option value="in_use">Sedang Digunakan</flux:select.option>
                    <flux:select.option value="maintenance">Maintenance</flux:select.option>
                </flux:select>

                {{-- Last Service Date --}}
                <flux:input 
                    wire:model="last_service_date" 
                    type="date" 
                    label="Tanggal Service Terakhir (Opsional)"
                />

                {{-- Image Upload --}}
                <div>
                    <flux:label>Foto Kendaraan (Opsional)</flux:label>
                    <input 
                        type="file" 
                        wire:model="image" 
                        accept="image/*"
                        class="mt-2 block w-full text-sm text-zinc-900 dark:text-zinc-100
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100
                               dark:file:bg-blue-900/20 dark:file:text-blue-400"
                    />
                    <div wire:loading wire:target="image" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        Mengupload...
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($image)
                        <div class="mt-3">
                            <img src="{{ $image->temporaryUrl() }}" class="rounded-lg max-w-xs shadow-md" alt="Preview">
                        </div>
                    @endif
                </div>

                {{-- Submit Buttons --}}
                <div class="flex gap-3 pt-4">
                    <flux:button 
                        href="{{ route('admin.vehicles.index') }}" 
                        variant="ghost"
                        class="flex-1"
                    >
                        Batal
                    </flux:button>
                    <flux:button 
                        type="submit" 
                        variant="primary" 
                        icon="check" 
                        class="flex-1"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Simpan</span>
                        <span wire:loading>Menyimpan...</span>
                    </flux:button>
                </div>
            </x-form>
        </div>
    </div>
</div>
