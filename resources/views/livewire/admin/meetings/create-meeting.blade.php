<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Buat Meeting Baru</flux:heading>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form wire:submit="create" class="space-y-6">
                <flux:field>
                    <flux:label>Judul Meeting</flux:label>
                    <flux:input wire:model="title" placeholder="Masukkan judul meeting" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Ruang Rapat</flux:label>
                    <flux:select wire:model.live="room_id" placeholder="Pilih ruang rapat">
                        @foreach($rooms as $room)
                            <flux:select.option value="{{ $room->id }}">
                                {{ $room->name }} (Kapasitas: {{ $room->capacity }} orang)
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="room_id" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Waktu Mulai</flux:label>
                        <flux:input type="datetime-local" wire:model="started_at" />
                        <flux:error name="started_at" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Estimasi Durasi (menit)</flux:label>
                        <flux:input type="number" wire:model="duration" min="15" max="480" />
                        <flux:error name="duration" />
                        <flux:description>Minimal 15 menit, maksimal 480 menit (8 jam)</flux:description>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Estimasi Peserta</flux:label>
                    <flux:input type="number" wire:model="estimated_participants" min="1" />
                    <flux:error name="estimated_participants" />
                </flux:field>

                <flux:field>
                    <flux:label>Catatan Meeting</flux:label>
                    <flux:textarea wire:model="notes" rows="4" placeholder="Tambahkan catatan, agenda, atau detail meeting..." />
                    <flux:error name="notes" />
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="show_notes_on_monitor">
                        Tampilkan catatan di monitor display
                    </flux:checkbox>
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Buat Meeting</span>
                        <span wire:loading>Membuat...</span>
                    </flux:button>
                    <flux:button type="button" variant="ghost" href="{{ route('admin.meetings.index') }}" wire:navigate>
                        Batal
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
