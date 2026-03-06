<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <flux:button href="{{ route('home') }}" variant="ghost" icon="arrow-left" class="mb-4">
                Kembali ke Home
            </flux:button>
            
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.return_vehicle') }}
            </h1>
            <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                Kembalikan kendaraan yang telah dipinjam
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-8">
                @if(!$selectedLoan)
                    <!-- Loan Selection -->
                    <div class="space-y-4 sm:space-y-6">
                        <flux:field>
                            <flux:label>{{ __('vehicles.select_borrower') }}</flux:label>
                            <flux:select wire:model="loan_id" placeholder="Pilih peminjam...">
                                @foreach($activeLoans as $loan)
                                    <flux:select.option value="{{ $loan['loanId'] }}">
                                        {{ $loan['borrowerName'] }} - {{ $loan['vehicleName'] }}
                                        ({{ \Carbon\Carbon::parse($loan['loanedAt'])->format('d/m/Y H:i') }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="loan_id"/>
                        </flux:field>

                        @if(count($activeLoans) === 0)
                            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex">
                                    <flux:icon.information-circle class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3 flex-shrink-0 mt-0.5" />
                                    <div class="text-sm text-amber-800 dark:text-amber-300">
                                        <p class="font-semibold mb-1">Tidak ada peminjaman aktif</p>
                                        <p>Belum ada kendaraan yang dipinjam atau semua sudah dikembalikan.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <flux:button 
                            wire:click="selectLoan" 
                            variant="primary" 
                            class="w-full" 
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="selectLoan">{{ __('vehicles.select') }}</span>
                            <span wire:loading wire:target="selectLoan">Memuat...</span>
                        </flux:button>
                    </div>
                @else
                    <!-- Return Form -->
                    <form wire:submit="submitReturn" class="space-y-4 sm:space-y-6">
                        <!-- Loan Info -->
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Peminjaman</h3>
                            <div class="grid gap-2 sm:gap-3 text-sm">
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('vehicles.vehicle') }}:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $selectedLoan->vehicle->license_plate }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('vehicles.borrower') }}:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $selectedLoan->user->name ?? 'Guest' }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('vehicles.start_mileage') }}:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ number_format($selectedLoan->start_mileage) }} km</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('vehicles.purpose') }}:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white break-words">{{ $selectedLoan->purpose }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- End Mileage -->
                        <flux:input
                            wire:model.blur="end_mileage"
                            type="number"
                            min="{{ $selectedLoan->start_mileage }}"
                            label="{{ __('vehicles.end_mileage') }}"
                            placeholder="Masukkan kilometer akhir (min: {{ number_format($selectedLoan->start_mileage) }} km)"
                        />

                        <!-- Speedometer Photo -->
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
                                        alt="Preview" 
                                        class="h-32 sm:h-40 w-auto rounded-lg shadow-md border border-zinc-200 dark:border-zinc-700"
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

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <flux:button 
                                type="button" 
                                wire:click="$set('selectedLoan', null)" 
                                variant="ghost" 
                                class="w-full sm:flex-1"
                            >
                                {{ __('global.cancel') }}
                            </flux:button>
                            <flux:button 
                                type="submit" 
                                variant="primary" 
                                class="w-full sm:flex-1" 
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="submitReturn">{{ __('vehicles.submit_return') }}</span>
                                <span wire:loading wire:target="submitReturn">Menyimpan...</span>
                            </flux:button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>

@script
<script>
    // Load active loans from localStorage on component mount
    Alpine.effect(() => {
        const activeLoans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        $wire.set('activeLoans', activeLoans);
    });

    // Listen for save-loan-to-cache event
    $wire.on('save-loan-to-cache', (event) => {
        const loanData = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        
        // Add new loan
        loans.push(loanData);
        
        localStorage.setItem('activeLoans', JSON.stringify(loans));
        $wire.set('activeLoans', loans);
    });

    // Listen for remove-loan-from-cache event
    $wire.on('remove-loan-from-cache', (event) => {
        const { loanId } = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        
        // Remove returned loan
        loans = loans.filter(loan => loan.loanId !== loanId);
        
        localStorage.setItem('activeLoans', JSON.stringify(loans));
        $wire.set('activeLoans', loans);
    });
</script>
@endscript
