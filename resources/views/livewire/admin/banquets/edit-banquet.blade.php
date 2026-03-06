<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Edit Banquet</flux:heading>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form wire:submit="update" class="space-y-6">
                <flux:field>
                    <flux:label>Judul Banquet</flux:label>
                    <flux:input wire:model="title" placeholder="Masukkan judul banquet" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Tipe Tamu</flux:label>
                    <flux:select wire:model="guest_type" placeholder="Pilih tipe tamu">
                        <flux:select.option value="VVIP">VVIP</flux:select.option>
                        <flux:select.option value="VIP">VIP</flux:select.option>
                        <flux:select.option value="Internal">Internal</flux:select.option>
                    </flux:select>
                    <flux:error name="guest_type" />
                </flux:field>

                <flux:field>
                    <flux:label>Venue Makan</flux:label>
                    <flux:select wire:model.live="venue_id" placeholder="Pilih venue">
                        @foreach($venues as $venue)
                            <flux:select.option value="{{ $venue->id }}">
                                {{ $venue->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="venue_id" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Tanggal Terjadwal</flux:label>
                        <flux:input type="datetime-local" wire:model="scheduled_at" />
                        <flux:error name="scheduled_at" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Estimasi Tamu</flux:label>
                        <flux:input type="number" wire:model="estimated_guests" min="1" />
                        <flux:error name="estimated_guests" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Biaya Jamuan (Rp)</flux:label>
                        <flux:input type="number" wire:model="cost" min="0" step="0.01" placeholder="0" />
                        <flux:error name="cost" />
                        <flux:description>Opsional - kosongkan jika belum ditentukan</flux:description>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Deskripsi</flux:label>
                    <flux:textarea wire:model="description" rows="4" placeholder="Tambahkan deskripsi banquet..." />
                    <flux:error name="description" />
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Banquet</span>
                        <span wire:loading>Memperbarui...</span>
                    </flux:button>
                    <flux:button type="button" variant="ghost" href="{{ route('admin.banquets.index') }}" wire:navigate>
                        Batal
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
