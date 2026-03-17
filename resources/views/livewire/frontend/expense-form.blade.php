{{-- Expense Form - Matched to Screenshot Style --}}
<div class="w-full min-h-screen py-8 px-4 font-roboto">
    <div class="max-w-2xl mx-auto">

        {{-- HEADER BANNER --}}
        <div class="rounded-2xl overflow-hidden mb-4 sm:mb-6 bg-gradient-to-r from-teal-500 to-emerald-500 shadow-lg">
            <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 sm:py-8">
                <div class="flex items-start gap-3 sm:gap-5 min-w-0">
                    <div class="p-2 sm:p-3 bg-white/20 rounded-xl shrink-0 mt-0.5 hidden sm:block">
                        <flux:icon.receipt-percent class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white uppercase leading-tight tracking-wide break-words">
                            {{ __('vehicles.expenses_title') }}
                        </h1>
                        <p class="text-teal-100 mt-1 sm:mt-2 text-xs sm:text-sm font-medium">{{ __('vehicles.expense_input_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM CONTAINER --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="px-8 pt-5 pb-2">
                <p class="text-xs text-red-500 font-medium">{{ __('vehicles.expense_required_mark') }}</p>
            </div>

            <x-form wire:submit="submitExpense" class="px-8 pb-8 space-y-8">

                {{-- Tipe Kegiatan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.expense_type') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['BBM', 'E-Money', 'Parkir', 'Cuci Mobil', 'Lainnya'] as $type)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="expense_type" value="{{ $type }}" class="sr-only peer" />
                                <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                    border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 bg-transparent
                                    peer-checked:border-teal-500 peer-checked:bg-teal-500 peer-checked:text-white
                                    hover:border-teal-400 cursor-pointer">
                                    {{ $type }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('expense_type')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Kendaraan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.vehicle') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:select wire:model.live="vehicle_id" placeholder="{{ __('vehicles.select_vehicle_placeholder') }}">
                        @if(!$vehicle_id)
                            <flux:select.option value="" disabled selected>{{ __('vehicles.select_vehicle_placeholder') }}</flux:select.option>
                        @endif
                        @foreach($vehicles as $vehicle)
                            <flux:select.option 
                                value="{{ $vehicle->id }}"
                                :selected="$vehicle_id === $vehicle->id"
                            >
                                {{ $vehicle->license_plate }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Nama Pelapor --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('global.username') ?? 'Name' }} <span class="text-red-500">*</span>
                    </label>
                    <flux:input 
                        wire:model="reporter_name" 
                        type="text" 
                        placeholder="{{ __('vehicles.borrower_search_placeholder') }}"
                        icon="magnifying-glass"
                    />
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.current_mileage') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:input wire:model.live="current_mileage" type="number" placeholder="{{ __('vehicles.current_mileage') }}" />
                    @if($vehicle_id)
                        <p class="mt-2 text-xs text-zinc-400">{{ __('vehicles.mileage_auto_filled') }}</p>
                    @endif
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Sumber Dana --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        Sumber Dana <span class="text-red-500">*</span>
                    </label>
                    <flux:select wire:model="funding_source" placeholder="Pilih sumber dana...">
                        <flux:select.option value="UANG_MUKA">{{ __('vehicles.uang_muka') }}</flux:select.option>
                        <flux:select.option value="UANG_PRIBADI">{{ __('vehicles.uang_pribadi') }}</flux:select.option>
                        <flux:select.option value="KOPERASI_KONSUMEN_SUKA_BAHARI">{{ __('vehicles.koperasi') }}</flux:select.option>
                    </flux:select>
                </div>

                {{-- BBM Detail (Conditional) --}}
                @if($expense_type === 'BBM')
                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>
                    <div class="space-y-6 p-5 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-200 dark:border-teal-800">
                        <p class="text-xs font-black uppercase tracking-widest text-teal-600 dark:text-teal-400">Detail BBM</p>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                                Jenis BBM <span class="text-red-500">*</span>
                            </label>
                            <flux:select wire:model.live="fuel_type" placeholder="Pilih jenis BBM...">
                                <flux:select.option value="PERTALITE">Pertalite (Rp 10.000/L)</flux:select.option>
                                <flux:select.option value="PERTAMAX">Pertamax (Rp 11.800/L)</flux:select.option>
                                <flux:select.option value="PERTAMAX TURBO">Pertamax Turbo (Rp 12.700/L)</flux:select.option>
                                <flux:select.option value="DEXLITE">Dexlite (Rp 13.250/L)</flux:select.option>
                                <flux:select.option value="PERTADEX">Pertadex (Rp 13.500/L)</flux:select.option>
                            </flux:select>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                                Jumlah Liter <span class="text-red-500">*</span>
                            </label>
                            <flux:input wire:model.live="fuel_liters" type="number" step="0.01" placeholder="Contoh: 15.50" />
                        </div>
                    </div>
                @endif

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Nominal --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.nominal') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:input wire:model="nominal" type="number" step="0.01" placeholder="{{ __('vehicles.nominal') }}" />
                    @if($expense_type === 'BBM' && $fuel_type && $fuel_liters)
                        <p class="mt-2 text-xs text-teal-600 dark:text-teal-400">* {{ __('vehicles.fuel_auto_calc') ?? 'Amount automatically calculated from fuel type × liters.' }}</p>
                    @endif
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Dokumentasi Foto --}}
                <div class="space-y-6">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                        {{ __('vehicles.documentation_photos') }} <span class="text-red-500">*</span>
                        <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('vehicles.speedometer_photo_desc') }})</span>
                    </label>

                    {{-- Bukti Pembayaran --}}
                    @if($expense_type !== 'Parkir')
                        <div>
                            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-3">{{ __('vehicles.payment_proof') }}</p>
                            @if(!($payment_proof ?? null))
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/10 transition-all group">
                                    <flux:icon.arrow-up-tray class="w-7 h-7 text-zinc-300 group-hover:text-teal-400 mb-1.5 transition-colors" />
                                    <span class="text-sm text-zinc-400 group-hover:text-teal-500 font-medium">{{ __('vehicles.click_to_upload') }}</span>
                                    <input type="file" wire:model="payment_proof" accept="image/*" capture="environment" class="hidden" />
                                </label>
                            @else
                                <div class="relative inline-block">
                                    <img src="{{ $payment_proof->temporaryUrl() }}" class="rounded-xl max-w-full sm:max-w-xs shadow-md border border-zinc-200 dark:border-zinc-700" alt="Preview">
                                    <button type="button" wire:click="clearPaymentProof" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow">
                                        <flux:icon.x-mark class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @endif
                            <div wire:loading wire:target="payment_proof" class="mt-2 text-xs text-teal-600 dark:text-teal-400 flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full border-2 border-teal-500 border-t-transparent animate-spin"></div> Mengupload...
                            </div>
                            @error('payment_proof') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- BBM Photos --}}
                    @if($expense_type === 'BBM')
                        <div>
                            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-3">{{ __('vehicles.vehicle_photo') }}</p>
                            @if(!($vehicle_photo ?? null))
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/10 transition-all group">
                                    <flux:icon.arrow-up-tray class="w-7 h-7 text-zinc-300 group-hover:text-teal-400 mb-1.5 transition-colors" />
                                    <span class="text-sm text-zinc-400 group-hover:text-teal-500 font-medium">{{ __('vehicles.click_to_upload') }}</span>
                                    <input type="file" wire:model="vehicle_photo" accept="image/*" capture="environment" class="hidden" />
                                </label>
                            @else
                                <div class="relative inline-block">
                                    <img src="{{ $vehicle_photo->temporaryUrl() }}" class="rounded-xl max-w-full sm:max-w-xs shadow-md border border-zinc-200 dark:border-zinc-700" alt="Preview">
                                    <button type="button" wire:click="clearVehiclePhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow"><flux:icon.x-mark class="w-3.5 h-3.5" /></button>
                                </div>
                            @endif
                            <div wire:loading wire:target="vehicle_photo" class="mt-2 text-xs text-teal-600 flex items-center gap-2"><div class="w-3 h-3 rounded-full border-2 border-teal-500 border-t-transparent animate-spin"></div> Mengupload...</div>
                            @error('vehicle_photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-3">{{ __('vehicles.fuel_indicator') }}</p>
                            @if(!($fuel_indicator ?? null))
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/10 transition-all group">
                                    <flux:icon.arrow-up-tray class="w-7 h-7 text-zinc-300 group-hover:text-teal-400 mb-1.5 transition-colors" />
                                    <span class="text-sm text-zinc-400 group-hover:text-teal-500 font-medium">{{ __('vehicles.click_to_upload') }}</span>
                                    <input type="file" wire:model="fuel_indicator" accept="image/*" capture="environment" class="hidden" />
                                </label>
                            @else
                                <div class="relative inline-block">
                                    <img src="{{ $fuel_indicator->temporaryUrl() }}" class="rounded-xl max-w-full sm:max-w-xs shadow-md border border-zinc-200 dark:border-zinc-700" alt="Preview">
                                    <button type="button" wire:click="clearFuelIndicator" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow"><flux:icon.x-mark class="w-3.5 h-3.5" /></button>
                                </div>
                            @endif
                            <div wire:loading wire:target="fuel_indicator" class="mt-2 text-xs text-teal-600 flex items-center gap-2"><div class="w-3 h-3 rounded-full border-2 border-teal-500 border-t-transparent animate-spin"></div> Mengupload...</div>
                            @error('fuel_indicator') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-3">{{ __('vehicles.activity_photo') }}</p>
                            @if(!($activity_photo ?? null))
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/10 transition-all group">
                                    <flux:icon.arrow-up-tray class="w-7 h-7 text-zinc-300 group-hover:text-teal-400 mb-1.5 transition-colors" />
                                    <span class="text-sm text-zinc-400 group-hover:text-teal-500 font-medium">{{ __('vehicles.click_to_upload') }}</span>
                                    <input type="file" wire:model="activity_photo" accept="image/*" capture="environment" class="hidden" />
                                </label>
                            @else
                                <div class="relative inline-block">
                                    <img src="{{ $activity_photo->temporaryUrl() }}" class="rounded-xl max-w-full sm:max-w-xs shadow-md border border-zinc-200 dark:border-zinc-700" alt="Preview">
                                    <button type="button" wire:click="clearActivityPhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow"><flux:icon.x-mark class="w-3.5 h-3.5" /></button>
                                </div>
                            @endif
                            <div wire:loading wire:target="activity_photo" class="mt-2 text-xs text-teal-600 flex items-center gap-2"><div class="w-3 h-3 rounded-full border-2 border-teal-500 border-t-transparent animate-spin"></div> Mengupload...</div>
                            @error('activity_photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.notes') }}
                        <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('vehicles.optional') }})</span>
                    </label>
                    <flux:textarea wire:model="notes" placeholder="Catatan tambahan..." rows="3" />
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button href="{{ route('home') }}" variant="ghost" icon="arrow-left">{{ __('vehicles.cancel') }}</flux:button>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        class="px-10 rounded-full! bg-teal-500 hover:bg-teal-600 border-0!"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ __('vehicles.submit_expense') }}</span>
                        <span wire:loading class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full border-2 border-white border-t-transparent animate-spin"></div>
                            {{ __('vehicles.saving') }}
                        </span>
                    </flux:button>
                </div>

            </x-form>
        </div>
    </div>
</div>
