<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.return_form') }}
            </h1>
            <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                Pengembalian kendaraan {{ $loan->vehicle->license_plate }}
            </p>
        </div>

        {{-- Loan Info Card --}}
        <div class="mb-4 sm:mb-6 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Informasi Peminjaman</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Kendaraan</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->vehicle->license_plate }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Peminjam</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->user->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Tujuan</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->purpose }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Destinasi</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->destination }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Kilometer Awal</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($loan->start_mileage) }} km</p>
                </div>
            </div>
        </div>

        {{-- Return Form Card --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-8">
                <x-form wire:submit="submitReturn" class="space-y-4 sm:space-y-6">
                    
                    {{-- End Mileage --}}
                    <flux:input 
                        wire:model.live="end_mileage" 
                        type="number" 
                        label="{{ __('vehicles.end_mileage') }}" 
                        placeholder="Masukkan kilometer akhir"
                        min="{{ $loan->start_mileage }}"
                    />

                    @if($end_mileage > $loan->start_mileage)
                        <div class="p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-200 mb-1">
                                Jarak Tempuh
                            </p>
                            <p class="text-xl sm:text-2xl font-bold text-green-700 dark:text-green-300">
                                {{ number_format($end_mileage - $loan->start_mileage) }} km
                            </p>
                        </div>
                    @endif

                    {{-- Speedometer Photo --}}
                    <div>
                        <flux:label>{{ __('vehicles.speedometer_photo') }}</flux:label>
                        <input 
                            type="file" 
                            wire:model="speedometer_photo" 
                            accept="image/*"
                            class="mt-2 block w-full text-sm text-zinc-900 dark:text-zinc-100
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100
                                   dark:file:bg-blue-900/20 dark:file:text-blue-400
                                   dark:hover:file:bg-blue-900/30"
                        />
                        @error('speedometer_photo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        
                        @if($speedometer_photo)
                            <div class="mt-4">
                                <img src="{{ $speedometer_photo->temporaryUrl() }}" class="rounded-lg max-w-full sm:max-w-xs" alt="Preview">
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <flux:button 
                            href="{{ route('vehicles.monitor') }}" 
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
                            <span wire:loading.remove>{{ __('vehicles.submit_return') }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </flux:button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</section>
