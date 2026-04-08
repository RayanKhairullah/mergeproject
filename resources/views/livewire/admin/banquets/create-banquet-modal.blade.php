@volt
<?php
use function Livewire\Volt\{state, computed, mount};

state([
    'title' => '',
    'description' => '',
    'venue_id' => null,
    'new_venue_name' => '',
    'guest_type' => null,
    'new_guest_type' => '',
    'estimated_guests' => 1,
    'cost' => 0,
    'scheduled_at' => '',
    'showCreateVenue' => false,
    'showCreateGuestType' => false,
]);

mount(function () {
    $this->authorize('create banquets');
    $this->scheduled_at = now()->addHour()->format('Y-m-d\TH:i');
});

$venues = computed(fn() => \App\Models\DiningVenue::orderBy('name')->get());
$guestTypes = computed(fn() => \App\Models\GuestType::orderBy('label')->get());

$createGuestType = function () {
    $this->validate([
        'new_guest_type' => 'required|string|max:255',
    ], [
        'new_guest_type.required' => 'Nama tipe tamu harus diisi.',
    ]);

    // Check if guest type already exists
    $existingGuestType = \App\Models\GuestType::where('value', $this->new_guest_type)
        ->orWhere('label', $this->new_guest_type)
        ->first();
    if ($existingGuestType) {
        $this->addError('new_guest_type', 'Tipe tamu sudah ada.');
        return;
    }

    $guestType = \App\Models\GuestType::create([
        'value' => $this->new_guest_type,
        'label' => $this->new_guest_type,
    ]);

    $this->guest_type = $guestType->value;
    $this->new_guest_type = '';
    $this->showCreateGuestType = false;
    
    $this->dispatch('guest-type-created');
};

$createVenue = function () {
    $this->validate([
        'new_venue_name' => 'required|string|max:255|unique:dining_venues,name',
    ], [
        'new_venue_name.unique' => 'Nama venue sudah ada.',
    ]);

    $venue = \App\Models\DiningVenue::create([
        'name' => $this->new_venue_name,
    ]);

    $this->venue_id = $venue->id;
    $this->new_venue_name = '';
    $this->showCreateVenue = false;
    
    $this->dispatch('venue-created');
};

$create = function () {
    $this->authorize('create banquets');
    $this->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'venue_id' => 'required|exists:dining_venues,id',
        'guest_type' => 'required|exists:guest_types,value',
        'estimated_guests' => 'required|integer|min:1',
        'cost' => 'required|numeric|min:0',
        'scheduled_at' => 'required|date|after:now',
    ], [
        'scheduled_at.after' => 'Waktu jadwal harus di masa depan.',
        'estimated_guests.min' => 'Minimal 1 tamu diperlukan.',
        'cost.min' => 'Biaya tidak boleh negatif.',
    ]);

    \App\Models\Banquet::create([
        'title' => $this->title,
        'description' => $this->description,
        'venue_id' => $this->venue_id,
        'guest_type' => $this->guest_type,
        'estimated_guests' => $this->estimated_guests,
        'cost' => $this->cost,
        'scheduled_at' => $this->scheduled_at,
        'status' => \App\Enums\BanquetStatus::DRAFT,
        'created_by' => auth()->id(),
    ]);

    $this->dispatch('banquet-created');
    session()->flash('success', __('banquets.success_created'));
    $this->dispatch('close-modal');
    
    // Reset form
    $this->title = '';
    $this->description = '';
    $this->venue_id = null;
    $this->guest_type = null;
    $this->estimated_guests = 1;
    $this->cost = 0;
    $this->scheduled_at = now()->addHour()->format('Y-m-d\TH:i');
};

$toggleCreateGuestType = fn() => $this->showCreateGuestType = !$this->showCreateGuestType;
$toggleCreateVenue = fn() => $this->showCreateVenue = !$this->showCreateVenue;
?>

<flux:modal name="create-banquet" class="w-full max-w-4xl" x-on:banquet-created.window="$flux.modal('create-banquet').close()">
    <form wire:submit="create" class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('banquets.create') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('banquets.fields.title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('banquets.fields.title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('banquets.fields.venue') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:select wire:model.live="venue_id" placeholder="{{ __('banquets.select_venue') }}" class="flex-1">
                            @foreach($this->venues as $venue)
                                <flux:select.option value="{{ $venue->id }}">
                                    {{ $venue->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button type="button" wire:click="toggleCreateVenue" variant="ghost" size="sm" icon="plus" />
                    </div>
                    <flux:error name="venue_id" />
                </flux:field>

                @if($showCreateVenue)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg space-y-3">
                        <flux:field>
                            <flux:label>{{ __('banquets.add_new_venue') }}</flux:label>
                            <flux:input wire:model="new_venue_name" placeholder="{{ __('banquets.venue_name') }}" />
                            <flux:error name="new_venue_name" />
                        </flux:field>
                        <div class="flex gap-2">
                            <flux:button type="button" wire:click="createVenue" size="sm" variant="primary">
                                {{ __('banquets.add_venue') }}
                            </flux:button>
                            <flux:button type="button" wire:click="toggleCreateVenue" size="sm" variant="ghost">
                                {{ __('global.cancel') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <flux:field>
                    <flux:label>{{ __('banquets.fields.guest_type') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:select wire:model="guest_type" placeholder="{{ __('banquets.select_guest_type') }}" class="flex-1">
                            @foreach($this->guestTypes as $type)
                                <flux:select.option value="{{ $type->value }}">
                                    {{ $type->label }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button type="button" wire:click="toggleCreateGuestType" variant="ghost" size="sm" icon="plus" />
                    </div>
                    <flux:error name="guest_type" />
                </flux:field>

                @if($showCreateGuestType)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg space-y-3">
                        <flux:heading size="sm">{{ __('banquets.add_new_guest_type') }}</flux:heading>
                        <flux:field>
                            <flux:label>{{ __('banquets.guest_type_name') }}</flux:label>
                            <flux:input wire:model="new_guest_type" placeholder="{{ __('banquets.guest_type_name') }}" />
                            <flux:error name="new_guest_type" />
                        </flux:field>
                        <div class="flex gap-2">
                            <flux:button type="button" wire:click="createGuestType" size="sm" variant="primary">
                                {{ __('banquets.add_guest_type') }}
                            </flux:button>
                            <flux:button type="button" wire:click="toggleCreateGuestType" size="sm" variant="ghost">
                                {{ __('global.cancel') }}
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('banquets.fields.scheduled_at') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="scheduled_at" />
                    <flux:error name="scheduled_at" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('banquets.fields.estimated_guests') }}</flux:label>
                    <flux:input type="number" wire:model="estimated_guests" min="1" />
                    <flux:error name="estimated_guests" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('banquets.cost') }} (Rp)</flux:label>
                    <flux:input type="number" wire:model="cost" min="0" step="0.01" />
                    <flux:error name="cost" />
                </flux:field>
            </div>
        </div>

        <flux:field>
            <flux:label>{{ __('banquets.fields.description') }}</flux:label>
            <x-rich-text wire:model="description" placeholder="{{ __('banquets.fields.description') }}" />
            <flux:error name="description" />
        </flux:field>

        <div class="flex gap-3 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('global.cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('banquets.publish') }}</span>
                <span wire:loading>{{ __('banquets.creating') }}</span>
            </flux:button>
        </div>
    </form>
</flux:modal>
@endvolt