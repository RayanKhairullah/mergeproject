<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <flux:button href="{{ route('admin.vehicles.index') }}" variant="ghost" icon="arrow-left" class="mb-4">
            Kembali
        </flux:button>
        
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Edit Kendaraan
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Update informasi kendaraan {{ $vehicle->license_plate }}
        </p>
    </div>

    {{-- Form Card --}}
    <div class="max-w-2xl bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6">
            <x-form wire:submit="update" class="space-y-6">
                
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

                {{-- Existing Image --}}
                @if($existing_image)
                    <div>
                        <flux:label>Foto Saat Ini</flux:label>
                        <div class="mt-2 relative inline-block">
                            <img src="{{ Storage::url($existing_image) }}" class="rounded-lg max-w-xs shadow-md" alt="Current">
                            <button 
                                type="button"
                                wire:click="deleteImage"
                                wire:confirm="Apakah Anda yakin ingin menghapus foto ini?"
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors duration-200"
                            >
                                <span class="text-sm font-bold">×</span>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Image Upload --}}
                <div x-data="{ photoPreview: null }">
                    <flux:label>{{ $existing_image ? 'Ganti Foto' : 'Foto Kendaraan' }} (Opsional)</flux:label>
                    <input 
                        type="file" 
                        wire:model="image" 
                        accept="image/*"
                        x-on:change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            }
                        "
                        class="mt-2 block w-full text-sm text-zinc-900 dark:text-zinc-100
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100
                               dark:file:bg-blue-900/20 dark:file:text-blue-400"
                    />
                    
                    <div wire:loading wire:target="image" class="mt-2 flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400">
                        <flux:icon.loader-circle class="animate-spin w-4 h-4"/>
                        <span>Mengupload ke server...</span>
                    </div>

                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Instant Alpine Preview --}}
                    <template x-if="photoPreview">
                        <div class="mt-4">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Preview Foto Baru:</p>
                            <img :src="photoPreview" class="rounded-xl max-w-xs shadow-lg border border-zinc-200 dark:border-zinc-700" alt="Preview">
                        </div>
                    </template>
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
                        <span wire:loading.remove>Update</span>
                        <span wire:loading>Mengupdate...</span>
                    </flux:button>
                </div>
            </x-form>
        </div>
    </div>
</div>
