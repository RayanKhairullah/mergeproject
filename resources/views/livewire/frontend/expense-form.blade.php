<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <flux:button href="{{ route('home') }}" variant="ghost" icon="arrow-left" class="mb-4">
                Kembali ke Home
            </flux:button>
            
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.expense_form') }}
            </h1>
            <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                Input kegiatan/biaya kendaraan operasional
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-8">
                <x-form wire:submit="submitExpense" class="space-y-4 sm:space-y-6">
                    
                    {{-- Tipe Kegiatan --}}
                    <div>
                        <flux:label>{{ __('vehicles.expense_type') }} *</flux:label>
                        <div class="mt-3 grid grid-cols-2 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="BBM" class="mr-2" />
                                <span class="text-sm">BBM</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="E-Money" class="mr-2" />
                                <span class="text-sm">E-Money</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="Parkir" class="mr-2" />
                                <span class="text-sm">Parkir</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="Cuci Mobil" class="mr-2" />
                                <span class="text-sm">Cuci Mobil</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="Lainnya" class="mr-2" />
                                <span class="text-sm">Lainnya</span>
                            </label>
                        </div>
                        @error('expense_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kendaraan --}}
                    <flux:select 
                        wire:model.live="vehicle_id" 
                        label="{{ __('vehicles.select_vehicle') }}" 
                        placeholder="Pilih kendaraan..."
                    >
                        @foreach($vehicles as $vehicle)
                            <flux:select.option value="{{ $vehicle->id }}">
                                {{ $vehicle->license_plate }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    {{-- Nama Pelapor --}}
                    <flux:input 
                        wire:model="reporter_name" 
                        type="text" 
                        label="Nama Pelapor" 
                        placeholder="Masukkan nama pelapor"
                    />

                    {{-- Current Mileage --}}
                    <flux:input 
                        wire:model.live="current_mileage" 
                        type="number" 
                        label="Kilometer Saat Ini" 
                        placeholder="Masukkan kilometer saat ini"
                    />
                    @if($vehicle_id)
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 -mt-4">
                            * Otomatis terisi dengan kilometer terakhir. Anda bisa mengedit jika ada perubahan.
                        </p>
                    @endif

                    {{-- Sumber Dana --}}
                    <flux:select 
                        wire:model="funding_source" 
                        label="{{ __('vehicles.funding_source') }}" 
                        placeholder="Pilih sumber dana..."
                    >
                        <flux:select.option value="UANG_MUKA">{{ __('vehicles.uang_muka') }}</flux:select.option>
                        <flux:select.option value="UANG_PRIBADI">{{ __('vehicles.uang_pribadi') }}</flux:select.option>
                        <flux:select.option value="KOPERASI_KONSUMEN_SUKA_BAHARI">{{ __('vehicles.koperasi') }}</flux:select.option>
                    </flux:select>

                    {{-- BBM Specific Fields --}}
                    @if($expense_type === 'BBM')
                        <div class="p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 space-y-4">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                                Detail BBM
                            </p>

                            <flux:select 
                                wire:model.live="fuel_type" 
                                label="{{ __('vehicles.fuel_type') }}" 
                                placeholder="Pilih jenis BBM..."
                            >
                                <flux:select.option value="PERTALITE">Pertalite (Rp 10.000/L)</flux:select.option>
                                <flux:select.option value="PERTAMAX">Pertamax (Rp 11.800/L)</flux:select.option>
                                <flux:select.option value="PERTAMAX TURBO">Pertamax Turbo (Rp 12.700/L)</flux:select.option>
                                <flux:select.option value="DEXLITE">Dexlite (Rp 13.250/L)</flux:select.option>
                                <flux:select.option value="PERTADEX">Pertadex (Rp 13.500/L)</flux:select.option>
                            </flux:select>

                            <flux:input 
                                wire:model.live="fuel_liters" 
                                type="number" 
                                step="0.01"
                                label="{{ __('vehicles.fuel_liters') }}" 
                                placeholder="Contoh: 15.50"
                            />
                        </div>
                    @endif

                    {{-- Nominal (Auto-calculated for BBM) --}}
                    <flux:input 
                        wire:model="nominal" 
                        type="number" 
                        step="0.01"
                        label="{{ __('vehicles.nominal') }}" 
                        placeholder="Masukkan nominal dalam Rupiah"
                    />
                    @if($expense_type === 'BBM' && $fuel_type && $fuel_liters)
                        <p class="text-xs text-blue-600 dark:text-blue-400 -mt-4">
                            * Nominal otomatis dihitung. Anda masih bisa mengedit jika ada perubahan harga.
                        </p>
                    @endif

                    {{-- Dokumentasi Foto --}}
                    <div class="space-y-4">
                        <flux:label>{{ __('vehicles.documentation_photos') }} *</flux:label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Maksimal ukuran file: 2MB per foto</p>
                        
                        {{-- Bukti Pembayaran (Wajib kecuali Parkir) --}}
                        @if($expense_type !== 'Parkir')
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('vehicles.payment_proof') }} (Wajib)
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="payment_proof" 
                                    accept="image/*"
                                    class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100
                                           dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                />
                                <div wire:loading wire:target="payment_proof" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Mengupload...
                                </div>
                                @error('payment_proof')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @if($payment_proof)
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $payment_proof->temporaryUrl() }}" class="rounded-lg max-w-full sm:max-w-xs shadow-md" alt="Preview">
                                        <button 
                                            type="button"
                                            wire:click="clearPaymentProof"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors duration-200"
                                        >
                                            <span class="text-sm font-bold">×</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($expense_type === 'BBM')
                            {{-- Foto Mobil Belakang --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('vehicles.vehicle_photo') }} (Wajib)
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="vehicle_photo" 
                                    accept="image/*"
                                    class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100
                                           dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                />
                                <div wire:loading wire:target="vehicle_photo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Mengupload...
                                </div>
                                @error('vehicle_photo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @if($vehicle_photo)
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $vehicle_photo->temporaryUrl() }}" class="rounded-lg max-w-full sm:max-w-xs shadow-md" alt="Preview">
                                        <button 
                                            type="button"
                                            wire:click="clearVehiclePhoto"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors duration-200"
                                        >
                                            <span class="text-sm font-bold">×</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Indikator BBM --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('vehicles.fuel_indicator') }} (Wajib)
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="fuel_indicator" 
                                    accept="image/*"
                                    class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100
                                           dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                />
                                <div wire:loading wire:target="fuel_indicator" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Mengupload...
                                </div>
                                @error('fuel_indicator')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @if($fuel_indicator)
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $fuel_indicator->temporaryUrl() }}" class="rounded-lg max-w-full sm:max-w-xs shadow-md" alt="Preview">
                                        <button 
                                            type="button"
                                            wire:click="clearFuelIndicator"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors duration-200"
                                        >
                                            <span class="text-sm font-bold">×</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Foto Kegiatan --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('vehicles.activity_photo') }} (Wajib)
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="activity_photo" 
                                    accept="image/*"
                                    class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100
                                           dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                />
                                <div wire:loading wire:target="activity_photo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Mengupload...
                                </div>
                                @error('activity_photo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @if($activity_photo)
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $activity_photo->temporaryUrl() }}" class="rounded-lg max-w-full sm:max-w-xs shadow-md" alt="Preview">
                                        <button 
                                            type="button"
                                            wire:click="clearActivityPhoto"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors duration-200"
                                        >
                                            <span class="text-sm font-bold">×</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    <flux:textarea 
                        wire:model="notes" 
                        label="{{ __('vehicles.notes') }}" 
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
                            <span wire:loading.remove>{{ __('vehicles.submit_expense') }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </flux:button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</section>
