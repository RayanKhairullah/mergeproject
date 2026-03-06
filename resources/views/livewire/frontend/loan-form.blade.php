<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <flux:button href="{{ route('vehicles.monitor') }}" variant="ghost" icon="arrow-left" class="mb-4">
                Kembali ke Monitor
            </flux:button>
            
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.loan_form') }}
            </h1>
            <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                Isi form peminjaman kendaraan
            </p>
        </div>

        {{-- Form and Info Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Card --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="p-4 sm:p-6 lg:p-8">
                        <x-form wire:submit="submitLoan" class="space-y-4 sm:space-y-6">
                            
                            {{-- Vehicle Selection --}}
                            <flux:select 
                                wire:model.live="vehicle_id" 
                                label="{{ __('vehicles.select_vehicle') }}" 
                                placeholder="Pilih kendaraan..."
                                :disabled="$vehicle !== null"
                            >
                                @foreach($vehicles as $vehicleOption)
                                    <flux:select.option 
                                        value="{{ $vehicleOption->id }}"
                                        :disabled="$vehicleOption->status !== 'available'"
                                    >
                                        {{ $vehicleOption->license_plate }} - {{ number_format($vehicleOption->current_mileage) }} km
                                        @if($vehicleOption->status !== 'available')
                                            ({{ $vehicleOption->status === 'in_use' ? 'Sedang Dipinjam' : 'Maintenance' }})
                                        @endif
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            {{-- User Selection with Search --}}
                            <div>
                                <flux:label>{{ __('vehicles.borrower_name') }}</flux:label>
                                
                                @php
                                    $selectedUser = isset($user_id) && $user_id ? $users->firstWhere('id', $user_id) : null;
                                @endphp
                                
                                @if($selectedUser)
                                    <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 flex items-center justify-between">
                                        <span class="text-sm text-blue-900 dark:text-blue-200">
                                            Dipilih: {{ $selectedUser->name }}
                                        </span>
                                        <button 
                                            type="button"
                                            wire:click="clearEmployee"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <flux:input 
                                        wire:model.live="userSearch"
                                        type="text"
                                        placeholder="Ketik untuk mencari nama peminjam..."
                                        class="mt-2"
                                    />
                                    
                                    @if($userSearch && strlen($userSearch) > 0)
                                        <div class="mt-2 max-h-48 overflow-y-auto border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800">
                                            @php
                                                $filteredUsers = $users->filter(function($user) {
                                                    return stripos($user->name, $this->userSearch) !== false;
                                                });
                                            @endphp
                                            
                                            @forelse($filteredUsers as $user)
                                                <button 
                                                    type="button"
                                                    wire:click="selectEmployee({{ $user->id }}, '{{ $user->name }}')"
                                                    class="w-full text-left px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-sm text-zinc-900 dark:text-zinc-100"
                                                >
                                                    {{ $user->name }}
                                                </button>
                                            @empty
                                                <div class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">
                                                    Tidak ada hasil
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                @endif
                                
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Purpose --}}
                            <flux:textarea 
                                wire:model="purpose" 
                                label="{{ __('vehicles.purpose') }}" 
                                placeholder="Contoh: Rapat dengan klien, Pengiriman dokumen, dll"
                                rows="3"
                            />

                            {{-- Destination --}}
                            <flux:input 
                                wire:model="destination" 
                                label="{{ __('vehicles.destination') }}" 
                                placeholder="Contoh: Kantor Pusat Jakarta, Pelabuhan Tanjung Priok"
                            />

                            {{-- Start Mileage Info --}}
                            @if($vehicle_id)
                                @php
                                    $selectedVehicle = $vehicles->firstWhere('id', $vehicle_id);
                                @endphp
                                @if($selectedVehicle)
                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">
                                            Kilometer Awal
                                        </p>
                                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                            {{ number_format($selectedVehicle->current_mileage) }} km
                                        </p>
                                    </div>
                                @endif
                            @endif

                            {{-- Submit Button --}}
                            <div class="flex gap-3 pt-4">
                                <flux:button 
                                    type="submit" 
                                    variant="primary" 
                                    icon="check" 
                                    class="w-full"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove>{{ __('vehicles.submit_loan') }}</span>
                                    <span wire:loading>Menyimpan...</span>
                                </flux:button>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>

            {{-- Info Box (Right side on desktop, below on mobile) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 p-4 sm:p-6 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 shadow-lg">
                    <div class="flex">
                        <flux:icon.information-circle class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <p class="font-semibold mb-2">Catatan Penting:</p>
                            <ul class="list-disc list-inside space-y-1.5">
                                <li>Pastikan tujuan dan destinasi diisi dengan jelas</li>
                                <li>Setelah submit, Anda akan diarahkan ke form pengembalian</li>
                                <li>Kilometer awal akan otomatis tercatat dari posisi terakhir kendaraan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@script
<script>
    // Listen for save-loan-to-cache event
    $wire.on('save-loan-to-cache', (event) => {
        const loanData = event[0];
        let loans = JSON.parse(localStorage.getItem('activeLoans') || '[]');
        
        // Add new loan
        loans.push(loanData);
        
        localStorage.setItem('activeLoans', JSON.stringify(loans));
        
        console.log('Loan saved to cache:', loanData);
    });
</script>
@endscript
