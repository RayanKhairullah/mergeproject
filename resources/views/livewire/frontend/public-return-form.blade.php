{{-- Return Form - Screenshot Matched Style --}}
<div class="w-full min-h-screen py-8 px-4 font-roboto">
    <div class="max-w-2xl mx-auto">

        {{-- HEADER BANNER --}}
        <div class="rounded-2xl overflow-hidden mb-4 sm:mb-6 bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg">
            <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 sm:py-8">
                <div class="flex items-start gap-3 sm:gap-5 min-w-0">
                    <div class="p-2 sm:p-3 bg-white/20 rounded-xl shrink-0 mt-0.5 hidden sm:block">
                        <flux:icon.arrow-uturn-left class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white uppercase leading-tight tracking-wide break-words">
                            {{ __('vehicles.return_vehicle') }}
                        </h1>
                        <p class="text-blue-100 mt-1 sm:mt-2 text-xs sm:text-sm font-medium">{{ __('vehicles.return_form') }}</p>
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

            @if(!$selectedLoan)
                {{-- STEP 1: Pilih Peminjam --}}
                <div class="px-8 pb-8 space-y-8">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                            {{ __('vehicles.select_borrower') }} <span class="text-red-500">*</span>
                        </label>

                        @if(count($activeLoans) === 0)
                            <div class="flex items-start gap-3 p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                <flux:icon.information-circle class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">{{ __('vehicles.no_active_loans') }}</p>
                                    <p class="text-sm text-blue-700 dark:text-blue-400">{{ __('vehicles.no_active_loans_desc') }}</p>
                                </div>
                            </div>
                        @else
                            <flux:select wire:model="loan_id" placeholder="{{ __('vehicles.select_borrower_placeholder') }}">
                                @foreach($activeLoans as $loan)
                                    <flux:select.option value="{{ $loan['loanId'] }}">
                                        {{ $loan['borrowerName'] }} — {{ $loan['vehicleName'] }}
                                        ({{ \Carbon\Carbon::parse($loan['loanedAt'])->format('d/m/Y H:i') }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="loan_id"/>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button href="{{ route('home') }}" variant="ghost" icon="arrow-left">{{ __('vehicles.back') }}</flux:button>
                        <flux:button 
                            wire:click="selectLoan" 
                            variant="primary"
                            class="px-10 rounded-full! bg-blue-600 hover:bg-blue-700 border-0!"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>{{ __('global.lanjutkan') }}</span>
                            <span wire:loading>
                                {{ __('vehicles.loading') }}
                            </span>
                        </flux:button>
                    </div>
                </div>

            @else
                {{-- STEP 2: Return Form --}}
                <form wire:submit="submitReturn" class="px-8 pb-8 space-y-8">

                    {{-- Ringkasan Peminjaman --}}
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <p class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4">{{ __('vehicles.loan_summary') }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-1">{{ __('vehicles.vehicle') }}</p>
                                <p class="font-bold text-zinc-800 dark:text-zinc-200">{{ $selectedLoan->vehicle->license_plate }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-1">{{ __('vehicles.borrower') }}</p>
                                <p class="font-bold text-zinc-800 dark:text-zinc-200">{{ $selectedLoan->user->name ?? 'Guest' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-1">{{ __('vehicles.start_mileage') }}</p>
                                <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($selectedLoan->start_mileage) }} km</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-1">{{ __('vehicles.purpose') }}</p>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 truncate">{{ $selectedLoan->purpose }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                    {{-- Kilometer Akhir --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">
                            {{ __('vehicles.end_mileage') }} <span class="text-red-500">*</span>
                        </label>
                        <flux:input
                            wire:model.blur="end_mileage"
                            type="number"
                            min="{{ $selectedLoan->start_mileage }}"
                            placeholder="{{ __('vehicles.end_mileage_placeholder', ['min' => number_format($selectedLoan->start_mileage)]) }}"
                        />
                        <p class="mt-2 text-xs text-zinc-400">{{ __('vehicles.end_mileage_desc') }}</p>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                    {{-- Foto Speedometer --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-1">
                            {{ __('vehicles.speedometer_photo') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-zinc-400 mb-4">{{ __('vehicles.speedometer_photo_desc') }}</p>

                        @if(!$speedometer_photo)
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all group">
                                <flux:icon.camera class="w-8 h-8 text-zinc-300 group-hover:text-blue-500 mb-1.5 transition-colors" />
                                <span class="text-sm text-zinc-400 group-hover:text-blue-600 font-medium">{{ __('vehicles.click_to_upload_speedometer') }}</span>
                                <input type="file" wire:model="speedometer_photo" accept="image/*" class="hidden" />
                            </label>
                        @else
                            <div class="relative inline-block">
                                <img 
                                    src="{{ $speedometer_photo->temporaryUrl() }}" 
                                    alt="Preview" 
                                    class="h-40 w-auto rounded-xl shadow-md border border-zinc-200 dark:border-zinc-700"
                                >
                                <button type="button" wire:click="clearPhoto" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg">
                                    <flux:icon.x-mark class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @endif

                        <div wire:loading wire:target="speedometer_photo" class="mt-3 flex items-center gap-2 text-sm text-blue-600">
                            <div class="w-4 h-4 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
                            {{ __('vehicles.uploading') }}
                        </div>
                        @error('speedometer_photo')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" wire:click="$set('selectedLoan', null)" variant="ghost" icon="arrow-left">
                            {{ __('vehicles.back') }}
                        </flux:button>
                        <flux:button 
                            type="submit" 
                            variant="primary"
                            class="px-10 rounded-full! bg-blue-600 hover:bg-blue-700 border-0!"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="submitReturn">{{ __('vehicles.submit_return') }}</span>
                            <span wire:loading wire:target="submitReturn" class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full border-2 border-white border-t-transparent animate-spin"></div>
                                {{ __('vehicles.saving') }}
                            </span>
                        </flux:button>
                    </div>

                </form>
            @endif
        </div>
    </div>
</div>

@script
<script>
    Alpine.effect(() => {
        const activeLoans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        $wire.set('activeLoans', activeLoans);
    });

    $wire.on('save-loan-to-cache', (event) => {
        const loanData = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        loans.push(loanData);
        localStorage.setItem('activeLoans', JSON.stringify(loans));
        $wire.set('activeLoans', loans);
    });

    $wire.on('remove-loan-from-cache', (event) => {
        const { loanId } = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        loans = loans.filter(loan => loan.loanId !== loanId);
        localStorage.setItem('activeLoans', JSON.stringify(loans));
        $wire.set('activeLoans', loans);
    });
</script>
@endscript
