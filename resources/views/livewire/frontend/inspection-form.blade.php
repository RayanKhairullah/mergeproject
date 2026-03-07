{{-- Inspection Form - Matched to Loan/Expense Style --}}
<div class="w-full min-h-screen py-8 px-4 font-roboto">
    <div class="max-w-2xl mx-auto">

        {{-- HEADER BANNER --}}
        <div class="rounded-2xl overflow-hidden mb-4 sm:mb-6 bg-gradient-to-r from-violet-600 to-purple-500 shadow-lg">
            <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 sm:py-8">
                <div class="flex items-start gap-3 sm:gap-5 min-w-0">
                    <div class="p-2 sm:p-3 bg-white/20 rounded-xl shrink-0 mt-0.5 hidden sm:block">
                        <flux:icon.clipboard-document-check class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white uppercase leading-tight tracking-wide break-words">
                            {{ __('vehicles.inspection_form') }}
                        </h1>
                        <p class="text-purple-100 mt-1 sm:mt-2 text-xs sm:text-sm font-medium">{{ __('vehicles.inspection_description') }}</p>
                    </div>
                </div>

                {{-- Logo Pelindo --}}
                <div class="shrink-0 flex items-start justify-end pt-1">
                    <img src="{{ asset('images/logo_pelindo.png') }}" alt="Pelindo" class="h-6 sm:h-8 md:h-10 w-auto object-contain brightness-0 invert opacity-90">
                </div>
            </div>
        </div>

        {{-- FORM CONTAINER --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="px-8 pt-5 pb-2">
                <p class="text-xs text-red-500 font-medium">{{ __('vehicles.expense_required_mark') }}</p>
            </div>

            <x-form wire:submit="submitInspection" class="px-8 pb-8 space-y-8">

                {{-- Kendaraan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.select_vehicle') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:select
                        wire:model.live="vehicle_id"
                        placeholder="{{ __('vehicles.select_vehicle_placeholder') }}"
                    >
                        @foreach($vehicles as $vehicle)
                            <flux:select.option value="{{ $vehicle->id }}">
                                {{ $vehicle->license_plate }} — {{ number_format($vehicle->current_mileage) }} km
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @if($vehicle_id)
                        @php $selectedVehicle = $vehicles->firstWhere('id', $vehicle_id); @endphp
                        @if($selectedVehicle)
                            <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                <flux:icon.chart-bar class="w-4 h-4 text-purple-500 shrink-0" />
                                <span class="text-sm text-purple-700 dark:text-purple-300">{{ __('vehicles.current_mileage') }}: <b>{{ number_format($selectedVehicle->current_mileage) }} km</b></span>
                            </div>
                        @endif
                    @endif
                    @error('vehicle_id') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Waktu Inspeksi --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.inspection_time') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="inspection_time" value="morning" class="sr-only peer" />
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold border transition-all duration-150
                                border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 bg-transparent
                                peer-checked:border-violet-500 peer-checked:bg-violet-500 peer-checked:text-white
                                hover:border-violet-400 cursor-pointer">
                                <flux:icon.sun class="w-4 h-4" />
                                {{ __('vehicles.morning') }}
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="inspection_time" value="afternoon" class="sr-only peer" />
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold border transition-all duration-150
                                border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 bg-transparent
                                peer-checked:border-violet-500 peer-checked:bg-violet-500 peer-checked:text-white
                                hover:border-violet-400 cursor-pointer">
                                <flux:icon.moon class="w-4 h-4" />
                                {{ __('vehicles.afternoon') }}
                            </span>
                        </label>
                    </div>
                    @error('inspection_time') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Kondisi Kendaraan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-4">
                        {{ __('vehicles.vehicle_condition') }} <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-4 p-5 bg-violet-50 dark:bg-violet-900/20 rounded-xl border border-violet-200 dark:border-violet-800">

                        {{-- Tire Condition --}}
                        <div>
                            <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">{{ __('vehicles.tire_condition') }}</p>
                            <div class="flex flex-wrap gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="tire_condition_type" value="good" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white
                                        hover:border-emerald-400 cursor-pointer">
                                        <flux:icon.check-circle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.good') }}
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="tire_condition_type" value="other" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
                                        hover:border-amber-400 cursor-pointer">
                                        <flux:icon.exclamation-triangle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.other') }}
                                    </span>
                                </label>
                            </div>
                            @if($tire_condition_type === 'other')
                                <div class="mt-3">
                                    <flux:textarea wire:model="tire_condition_notes" placeholder="{{ __('vehicles.describe_tire_condition') }}" rows="2" />
                                </div>
                                @error('tire_condition_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div class="border-t border-violet-200 dark:border-violet-800"></div>

                        {{-- Body Condition --}}
                        <div>
                            <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">{{ __('vehicles.body_condition') }}</p>
                            <div class="flex flex-wrap gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="body_condition_type" value="good" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white
                                        hover:border-emerald-400 cursor-pointer">
                                        <flux:icon.check-circle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.good') }}
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="body_condition_type" value="other" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
                                        hover:border-amber-400 cursor-pointer">
                                        <flux:icon.exclamation-triangle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.other') }}
                                    </span>
                                </label>
                            </div>
                            @if($body_condition_type === 'other')
                                <div class="mt-3">
                                    <flux:textarea wire:model="body_condition_notes" placeholder="{{ __('vehicles.describe_body_condition') }}" rows="2" />
                                </div>
                                @error('body_condition_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div class="border-t border-violet-200 dark:border-violet-800"></div>

                        {{-- Glass Condition --}}
                        <div>
                            <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">{{ __('vehicles.glass_condition') }}</p>
                            <div class="flex flex-wrap gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="glass_condition_type" value="good" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white
                                        hover:border-emerald-400 cursor-pointer">
                                        <flux:icon.check-circle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.good') }}
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="glass_condition_type" value="other" class="sr-only peer" />
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all duration-150
                                        border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300
                                        peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
                                        hover:border-amber-400 cursor-pointer">
                                        <flux:icon.exclamation-triangle class="w-3.5 h-3.5" />
                                        {{ __('vehicles.other') }}
                                    </span>
                                </label>
                            </div>
                            @if($glass_condition_type === 'other')
                                <div class="mt-3">
                                    <flux:textarea wire:model="glass_condition_notes" placeholder="{{ __('vehicles.describe_glass_condition') }}" rows="2" />
                                </div>
                                @error('glass_condition_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @endif
                        </div>

                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Posisi Kilometer --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.mileage_check') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:input
                        wire:model.live="mileage_check"
                        type="number"
                        placeholder="{{ __('vehicles.mileage_check_placeholder') }}"
                        min="0"
                    />
                    @if($vehicle_id && $mileage_check)
                        @php $selectedVehicle = $vehicles->firstWhere('id', $vehicle_id); @endphp
                        @if($selectedVehicle && $mileage_check != $selectedVehicle->current_mileage)
                            <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <p class="text-sm text-amber-800 dark:text-amber-300">
                                    {{ number_format($selectedVehicle->current_mileage) }} km →
                                    {{ number_format($mileage_check) }} km
                                    <span class="font-bold">({{ $mileage_check > $selectedVehicle->current_mileage ? '+' : '' }}{{ number_format($mileage_check - $selectedVehicle->current_mileage) }} km)</span>
                                </p>
                            </div>
                        @endif
                    @endif
                    @error('mileage_check') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Foto Speedometer (Wajib) --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-1">
                        {{ __('vehicles.speedometer_photo') }} <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-zinc-400 mb-3">{{ __('vehicles.max_file_size_2mb') }}</p>

                    @if(!$speedometer_photo)
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-all group">
                            <flux:icon.arrow-up-tray class="w-7 h-7 text-zinc-300 group-hover:text-violet-400 mb-1.5 transition-colors" />
                            <span class="text-sm text-zinc-400 group-hover:text-violet-500 font-medium">{{ __('vehicles.click_to_upload') }}</span>
                            <input type="file" wire:model="speedometer_photo" accept="image/*" class="hidden" />
                        </label>
                    @else
                        <div class="relative inline-block">
                            <img src="{{ $speedometer_photo->temporaryUrl() }}" class="rounded-xl max-w-full sm:max-w-xs shadow-md border border-zinc-200 dark:border-zinc-700" alt="Preview">
                            <button type="button" wire:click="clearPhoto" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow">
                                <flux:icon.x-mark class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @endif
                    <div wire:loading wire:target="speedometer_photo" class="mt-2 text-xs text-violet-600 dark:text-violet-400 flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full border-2 border-violet-500 border-t-transparent animate-spin"></div>
                        {{ __('vehicles.uploading') }}
                    </div>
                    @error('speedometer_photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Foto Masalah (Opsional) --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-1">
                        {{ __('vehicles.issue_photos') }}
                        <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('vehicles.optional') }})</span>
                    </label>
                    <p class="text-xs text-zinc-400 mb-4">{{ __('vehicles.issue_photos_desc') }}</p>

                    <div class="space-y-3">
                        @for($i = 0; $i < 3; $i++)
                            <div>
                                @if(!isset($issue_photos[$i]))
                                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-violet-300 hover:bg-violet-50/50 dark:hover:bg-violet-900/10 transition-all group">
                                        <flux:icon.photo class="w-6 h-6 text-zinc-300 group-hover:text-violet-400 mb-1 transition-colors" />
                                        <span class="text-xs text-zinc-400 group-hover:text-violet-500">{{ __('vehicles.click_to_upload') }} ({{ __('vehicles.optional') }})</span>
                                        <input type="file" wire:model="issue_photos.{{ $i }}" accept="image/*" class="hidden" />
                                    </label>
                                @else
                                    <div class="relative inline-block">
                                        <img src="{{ $issue_photos[$i]->temporaryUrl() }}" class="h-24 w-auto rounded-lg shadow border border-zinc-200 dark:border-zinc-700" alt="Preview">
                                        <button type="button" wire:click="clearIssuePhoto({{ $i }})" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow">
                                            <flux:icon.x-mark class="w-3 h-3" />
                                        </button>
                                    </div>
                                @endif
                                <div wire:loading wire:target="issue_photos.{{ $i }}" class="mt-1 text-xs text-violet-600 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full border-2 border-violet-500 border-t-transparent animate-spin"></div>
                                    {{ __('vehicles.uploading') }}
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Catatan Tambahan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.additional_notes') }}
                        <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('vehicles.optional') }})</span>
                    </label>
                    <flux:textarea
                        wire:model="additional_notes"
                        placeholder="{{ __('vehicles.additional_notes_placeholder') }}"
                        rows="3"
                    />
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button href="{{ route('vehicles.monitor') }}" variant="ghost" icon="arrow-left">{{ __('vehicles.back') }}</flux:button>
                    <flux:button
                        type="submit"
                        variant="primary"
                        class="px-10 rounded-full! bg-violet-600 hover:bg-violet-700 border-0!"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="submitInspection">{{ __('vehicles.submit_inspection') }}</span>
                        <span wire:loading wire:target="submitInspection" class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full border-2 border-white border-t-transparent animate-spin"></div>
                            {{ __('vehicles.saving') }}
                        </span>
                    </flux:button>
                </div>

            </x-form>
        </div>
    </div>
</div>
