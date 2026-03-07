<div class="w-full min-h-screen flex flex-col pt-8 pb-12 px-4 sm:px-6 lg:px-8 font-roboto bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-2xl mx-auto w-full">
        {{-- HEADER BANNER --}}
        <div class="rounded-2xl overflow-hidden mb-4 sm:mb-6 bg-gradient-to-r from-emerald-500 to-green-500 shadow-lg">
            <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 sm:py-8">
                <div class="flex items-start gap-3 sm:gap-5 min-w-0">
                    <div class="p-2 sm:p-3 bg-white/20 rounded-xl shrink-0 mt-0.5 hidden sm:block">
                        <flux:icon.arrow-uturn-left class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white uppercase leading-tight tracking-wide break-words">
                            {{ __('vehicles.return_form') }}
                        </h1>
                        <p class="text-emerald-100 mt-1 sm:mt-2 text-xs sm:text-sm font-medium">{{ __('vehicles.return_vehicle_at', ['plate' => $loan->vehicle->license_plate]) }}</p>
                    </div>
                </div>
                
                {{-- Logo Pelindo --}}
                <div class="shrink-0 flex items-start justify-end pt-1">
                    <img src="{{ asset('images/logo_pelindo.png') }}" alt="Pelindo" class="h-6 sm:h-8 md:h-10 w-auto object-contain brightness-0 invert opacity-90">
                </div>
            </div>
        </div>

        {{-- Loan Info Card --}}
        <div class="mb-4 sm:mb-6 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">{{ __('vehicles.loan_info') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ __('vehicles.vehicle') }}</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->vehicle->license_plate }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ __('vehicles.borrower') }}</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->user->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ __('vehicles.purpose') }}</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->purpose }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ __('vehicles.destination') }}</p>
                    <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $loan->destination }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ __('vehicles.start_mileage') }}</p>
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
                        placeholder="{{ __('vehicles.end_mileage_placeholder_general') }}"
                        min="{{ $loan->start_mileage }}"
                    />

                    @if($end_mileage > $loan->start_mileage)
                        <div class="p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-200 mb-1">
                                {{ __('vehicles.distance_traveled') }}
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
                            {{ __('vehicles.cancel') }}
                        </flux:button>
                        <flux:button 
                            type="submit" 
                            variant="primary" 
                            icon="check" 
                            class="w-full sm:flex-1"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>{{ __('vehicles.submit_return') }}</span>
                            <span wire:loading>{{ __('vehicles.saving') }}</span>
                        </flux:button>
                    </div>
                </x-form>
            </div>
        </div>
        </div>
    </div>
</div>
