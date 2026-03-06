<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <flux:button href="{{ route('home') }}" variant="ghost" icon="arrow-left" class="mb-4">
                Kembali ke Home
            </flux:button>
            
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.inspection_form') }}
            </h1>
            <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                Inspeksi kesiapan kendaraan operasional
            </p>
        </div>

        {{-- Form and Info Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Card --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="p-4 sm:p-6 lg:p-8">
                        <x-form wire:submit="submitInspection" class="space-y-4 sm:space-y-6">
                            
                            {{-- Kendaraan --}}
                            <flux:select 
                                wire:model.live="vehicle_id" 
                                label="{{ __('vehicles.select_vehicle') }}" 
                                placeholder="Pilih kendaraan..."
                            >
                                @foreach($vehicles as $vehicle)
                                    <flux:select.option value="{{ $vehicle->id }}">
                                        {{ $vehicle->license_plate }} - {{ number_format($vehicle->current_mileage) }} km
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            {{-- Waktu Inspeksi --}}
                            <div>
                                <flux:label>{{ __('vehicles.inspection_time') }} *</flux:label>
                                <div class="mt-3 flex gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" wire:model="inspection_time" value="morning" class="mr-2" />
                                        <span class="text-sm">{{ __('vehicles.morning') }}</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" wire:model="inspection_time" value="afternoon" class="mr-2" />
                                        <span class="text-sm">{{ __('vehicles.afternoon') }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Kondisi Kendaraan --}}
                            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800 space-y-4 sm:space-y-6">
                                <p class="text-sm font-semibold text-purple-900 dark:text-purple-200">
                                    Kondisi Kendaraan
                                </p>

                                {{-- Tire Condition --}}
                                <div>
                                    <flux:label>{{ __('vehicles.tire_condition') }} *</flux:label>
                                    <div class="mt-3 space-y-3">
                                        <div class="flex gap-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="tire_condition_type" value="good" class="mr-2" />
                                                <span class="text-sm">Good</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="tire_condition_type" value="other" class="mr-2" />
                                                <span class="text-sm">Lainnya</span>
                                            </label>
                                        </div>
                                        @if($tire_condition_type === 'other')
                                            <flux:textarea 
                                                wire:model="tire_condition_notes" 
                                                placeholder="Jelaskan kondisi ban..."
                                                rows="2"
                                            />
                                            @error('tire_condition_notes')
                                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                {{-- Body Condition --}}
                                <div>
                                    <flux:label>{{ __('vehicles.body_condition') }} *</flux:label>
                                    <div class="mt-3 space-y-3">
                                        <div class="flex gap-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="body_condition_type" value="good" class="mr-2" />
                                                <span class="text-sm">Good</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="body_condition_type" value="other" class="mr-2" />
                                                <span class="text-sm">Lainnya</span>
                                            </label>
                                        </div>
                                        @if($body_condition_type === 'other')
                                            <flux:textarea 
                                                wire:model="body_condition_notes" 
                                                placeholder="Jelaskan kondisi body..."
                                                rows="2"
                                            />
                                            @error('body_condition_notes')
                                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                {{-- Glass Condition --}}
                                <div>
                                    <flux:label>{{ __('vehicles.glass_condition') }} *</flux:label>
                                    <div class="mt-3 space-y-3">
                                        <div class="flex gap-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="glass_condition_type" value="good" class="mr-2" />
                                                <span class="text-sm">Good</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="glass_condition_type" value="other" class="mr-2" />
                                                <span class="text-sm">Lainnya</span>
                                            </label>
                                        </div>
                                        @if($glass_condition_type === 'other')
                                            <flux:textarea 
                                                wire:model="glass_condition_notes" 
                                                placeholder="Jelaskan kondisi kaca..."
                                                rows="2"
                                            />
                                            @error('glass_condition_notes')
                                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Foto Masalah (Opsional) --}}
                            <div>
                                <flux:label>{{ __('vehicles.issue_photos') }} (Opsional)</flux:label>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3">
                                    Upload foto jika ditemukan masalah (maksimal 3 foto, max 2MB per foto)
                                </p>
                                
                                @for($i = 0; $i < 3; $i++)
                                    <div class="mb-4">
                                        @if(!isset($issue_photos[$i]))
                                            <input 
                                                type="file" 
                                                wire:model="issue_photos.{{ $i }}" 
                                                accept="image/*"
                                                class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                                       file:mr-4 file:py-2 file:px-4
                                                       file:rounded-lg file:border-0
                                                       file:text-sm file:font-semibold
                                                       file:bg-purple-50 file:text-purple-700
                                                       hover:file:bg-purple-100
                                                       dark:file:bg-purple-900/20 dark:file:text-purple-400"
                                            />
                                        @endif

                                        <div wire:loading wire:target="issue_photos.{{ $i }}" class="mt-2 text-sm text-purple-600 dark:text-purple-400">
                                            Mengupload...
                                        </div>

                                        @if(isset($issue_photos[$i]))
                                            <div class="mt-3 relative inline-block">
                                                <img 
                                                    src="{{ $issue_photos[$i]->temporaryUrl() }}" 
                                                    class="h-32 sm:h-40 w-auto rounded-lg shadow-md border border-zinc-200 dark:border-zinc-700" 
                                                    alt="Preview"
                                                >
                                                <button
                                                    type="button"
                                                    wire:click="clearIssuePhoto({{ $i }})"
                                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg transition-colors duration-200"
                                                    title="Hapus foto"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>

                            {{-- Posisi Kilometer --}}
                            <div>
                                <flux:input 
                                    wire:model.live="mileage_check" 
                                    type="number" 
                                    label="{{ __('vehicles.mileage_check') }}" 
                                    placeholder="Masukkan posisi kilometer saat ini"
                                    min="0"
                                />

                                @if($vehicle_id && $mileage_check)
                                    @php
                                        $selectedVehicle = $vehicles->firstWhere('id', $vehicle_id);
                                    @endphp
                                    @if($selectedVehicle && $mileage_check != $selectedVehicle->current_mileage)
                                        <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                                                Perubahan Kilometer
                                            </p>
                                            <p class="text-sm text-amber-800 dark:text-amber-300">
                                                Dari: {{ number_format($selectedVehicle->current_mileage) }} km → 
                                                Ke: {{ number_format($mileage_check) }} km
                                                ({{ $mileage_check > $selectedVehicle->current_mileage ? '+' : '' }}{{ number_format($mileage_check - $selectedVehicle->current_mileage) }} km)
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Foto Speedometer --}}
                            <div>
                                <flux:label>{{ __('vehicles.speedometer_photo') }} *</flux:label>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Maksimal ukuran file: 2MB</p>
                                
                                @if(!$speedometer_photo)
                                    <input 
                                        type="file" 
                                        wire:model="speedometer_photo" 
                                        accept="image/*"
                                        class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                               file:mr-4 file:py-2 file:px-4
                                               file:rounded-lg file:border-0
                                               file:text-sm file:font-semibold
                                               file:bg-blue-50 file:text-blue-700
                                               hover:file:bg-blue-100
                                               dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                    />
                                @endif

                                <div wire:loading wire:target="speedometer_photo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Mengupload...
                                </div>

                                @error('speedometer_photo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                @if($speedometer_photo)
                                    <div class="mt-3 relative inline-block">
                                        <img 
                                            src="{{ $speedometer_photo->temporaryUrl() }}" 
                                            class="h-32 sm:h-40 w-auto rounded-lg shadow-md border border-zinc-200 dark:border-zinc-700" 
                                            alt="Preview"
                                        >
                                        <button
                                            type="button"
                                            wire:click="clearPhoto"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg transition-colors duration-200"
                                            title="Hapus foto"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Catatan Tambahan --}}
                            <flux:textarea 
                                wire:model="additional_notes" 
                                label="{{ __('vehicles.additional_notes') }}" 
                                placeholder="Catatan tambahan (opsional)"
                                rows="3"
                            />

                            {{-- Submit Button --}}
                            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                                <flux:button 
                                    href="{{ route('home') }}" 
                                    variant="ghost"
                                    class="w-full sm:flex-1"
                                >
                                    Batal
                                </flux:button>
                                <flux:button 
                                    type="submit" 
                                    variant="primary" 
                                    icon="check" 
                                    class="w-full sm:flex-1"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="submitInspection">{{ __('vehicles.submit_inspection') }}</span>
                                    <span wire:loading wire:target="submitInspection">Menyimpan...</span>
                                </flux:button>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>

            {{-- Info Box (Right side on desktop, below on mobile) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 p-4 sm:p-6 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800 shadow-lg">
                    <div class="flex">
                        <flux:icon.information-circle class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-3 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-purple-800 dark:text-purple-300">
                            <p class="font-semibold mb-2">Catatan Penting:</p>
                            <ul class="list-disc list-inside space-y-1.5">
                                <li>Posisi kilometer akan otomatis tersinkron dengan data kendaraan</li>
                                <li>Upload foto masalah hanya jika ditemukan kondisi yang tidak baik</li>
                                <li>Foto speedometer wajib untuk validasi kilometer</li>
                                <li>Pilih "Good" jika kondisi normal, "Lainnya" jika ada masalah</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
