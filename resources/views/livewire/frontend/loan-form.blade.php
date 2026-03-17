{{-- Loan Form - Screenshot Matched Style --}}
<div class="w-full min-h-screen py-8 px-4 font-roboto">
    <div class="max-w-2xl mx-auto">

        {{-- HEADER BANNER --}}
        <div class="rounded-2xl overflow-hidden mb-4 sm:mb-6 bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg">
            <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 sm:py-8">
                <div class="flex items-start gap-3 sm:gap-5 min-w-0">
                    <div class="p-2 sm:p-3 bg-white/20 rounded-xl shrink-0 mt-0.5 hidden sm:block">
                        <flux:icon.key class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white uppercase leading-tight tracking-wide break-words">
                            {{ __('vehicles.loan_form') }}
                        </h1>
                        <p class="text-blue-100 mt-1 sm:mt-2 text-xs sm:text-sm font-medium">{{ __('vehicles.loan_description') }}</p>
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

            <x-form wire:submit="submitLoan" class="px-8 pb-8 space-y-8">

                {{-- Kendaraan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.select_vehicle') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:select 
                        wire:model.live="vehicle_id" 
                        placeholder="{{ __('vehicles.select_vehicle_placeholder') }}"
                        :disabled="$vehicle !== null"
                    >
                        @if(!$vehicle_id)
                            <flux:select.option value="" disabled selected>{{ __('vehicles.select_vehicle_placeholder') }}</flux:select.option>
                        @endif
                        @foreach($vehicles as $vehicleOption)
                            <flux:select.option 
                                value="{{ $vehicleOption->id }}"
                                :selected="$vehicle_id === $vehicleOption->id"
                                :disabled="$vehicleOption->status !== 'available'"
                            >
                                {{ $vehicleOption->license_plate }} — {{ number_format($vehicleOption->current_mileage) }} km
                                @if($vehicleOption->status !== 'available')
                                    ({{ $vehicleOption->status === 'in_use' ? __('vehicles.in_use') : __('vehicles.maintenance') }})
                                @endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @if($vehicle_id)
                        @php $selectedVehicle = $vehicles->firstWhere('id', $vehicle_id); @endphp
                        @if($selectedVehicle)
                            <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <flux:icon.chart-bar class="w-4 h-4 text-blue-500 shrink-0" />
                                <span class="text-sm text-blue-700 dark:text-blue-300">{{ __('vehicles.start_mileage') }}: <b>{{ number_format($selectedVehicle->current_mileage) }} km</b></span>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Peminjam --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.borrower_name') }} <span class="text-red-500">*</span>
                    </label>

                    @php $selectedUser = isset($user_id) && $user_id ? $users->firstWhere('id', $user_id) : null; @endphp

                    @if($selectedUser)
                        <div class="flex items-center justify-between px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                    {{ substr($selectedUser->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-semibold text-blue-900 dark:text-blue-200">{{ $selectedUser->name }}</span>
                            </div>
                            <button type="button" wire:click="clearEmployee" class="text-xs font-bold text-blue-500 hover:text-blue-700 underline">{{ __('global.edit') }}</button>
                        </div>
                    @else
                        <flux:input 
                            wire:model.live="userSearch"
                            type="text"
                            placeholder="{{ __('vehicles.borrower_search_placeholder') }}"
                            icon="magnifying-glass"
                        />
                        @if($userSearch && strlen($userSearch) > 0)
                            <div class="mt-1 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
                                @php $filteredUsers = $users->filter(fn($u) => stripos($u->name, $this->userSearch) !== false); @endphp
                                @forelse($filteredUsers as $user)
                                    <button 
                                        type="button"
                                        wire:click="selectEmployee({{ $user->id }}, '{{ $user->name }}')"
                                        class="w-full text-left px-4 py-3 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors border-b border-zinc-100 dark:border-zinc-800 last:border-0 text-zinc-800 dark:text-zinc-200"
                                    >
                                        {{ $user->name }}
                                    </button>
                                @empty
                                    <div class="px-4 py-3 text-sm text-zinc-400 text-center">{{ __('global.no_results') ?? 'No results' }}</div>
                                @endforelse
                            </div>
                        @endif
                    @endif
                    @error('user_id')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Destinasi --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.destination') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:input 
                        wire:model="destination" 
                        placeholder="{{ __('vehicles.destination_placeholder') }}"
                    />
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                {{-- Keperluan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                        {{ __('vehicles.purpose') }} <span class="text-red-500">*</span>
                    </label>
                    <flux:textarea 
                        wire:model="purpose" 
                        placeholder="{{ __('vehicles.purpose_placeholder') }}"
                        rows="3"
                    />
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button href="{{ route('vehicles.monitor') }}" variant="ghost" icon="arrow-left">{{ __('vehicles.back') }}</flux:button>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        class="px-10 rounded-full! bg-blue-600 hover:bg-blue-700 border-0!"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ __('vehicles.submit_loan') }}</span>
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

@script
<script>
    $wire.on('save-loan-to-cache', (event) => {
        const loanData = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        loans.push(loanData);
        localStorage.setItem('activeLoans', JSON.stringify(loans));
    });
</script>
@endscript
