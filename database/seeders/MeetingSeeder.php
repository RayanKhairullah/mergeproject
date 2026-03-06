<?php

namespace Database\Seeders;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::all();
        $users = User::all();
        $sdmUser = User::whereHas('roles', fn ($q) => $q->where('name', 'sdm'))->first();

        if ($rooms->isEmpty() || $users->isEmpty()) {
            return;
        }

        $meetings = [
            [
                'title' => 'Rapat Koordinasi Bulanan',
                'notes' => 'Pembahasan progress proyek bulan ini',
                'show_notes_on_monitor' => true,
                'room_id' => $rooms->random()->id,
                'started_at' => now()->addDays(2)->setTime(9, 0),
                'ended_at' => now()->addDays(2)->setTime(11, 0),
                'duration' => 120,
                'estimated_participants' => 15,
                'status' => MeetingStatus::PUBLISHED,
                'created_by' => $users->random()->id,
                'approved_by' => $sdmUser?->id,
                'approved_at' => now(),
            ],
            [
                'title' => 'Evaluasi Kinerja Q1',
                'notes' => 'Review pencapaian target kuartal pertama',
                'show_notes_on_monitor' => false,
                'room_id' => $rooms->random()->id,
                'started_at' => now()->addDays(5)->setTime(13, 0),
                'ended_at' => now()->addDays(5)->setTime(15, 0),
                'duration' => 120,
                'estimated_participants' => 10,
                'status' => MeetingStatus::PENDING_APPROVAL,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Briefing Keselamatan Kerja',
                'notes' => 'Sosialisasi prosedur K3 terbaru',
                'show_notes_on_monitor' => true,
                'room_id' => $rooms->random()->id,
                'started_at' => now()->addDays(1)->setTime(8, 0),
                'ended_at' => now()->addDays(1)->setTime(9, 30),
                'duration' => 90,
                'estimated_participants' => 25,
                'status' => MeetingStatus::PUBLISHED,
                'created_by' => $users->random()->id,
                'approved_by' => $sdmUser?->id,
                'approved_at' => now(),
            ],
            [
                'title' => 'Workshop Digital Transformation',
                'notes' => 'Pelatihan sistem digital baru',
                'show_notes_on_monitor' => true,
                'room_id' => $rooms->random()->id,
                'started_at' => now()->addDays(7)->setTime(9, 0),
                'ended_at' => now()->addDays(7)->setTime(16, 0),
                'duration' => 420,
                'estimated_participants' => 30,
                'status' => MeetingStatus::DRAFT,
                'created_by' => $users->random()->id,
            ],
        ];

        foreach ($meetings as $meeting) {
            Meeting::create($meeting);
        }
    }
}
