<?php

namespace App\Livewire\Intern;

use App\Models\Internship;
use App\Models\InternshipLog;
use App\Models\InternshipAttendance;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app.frontend')]
class Dashboard extends Component
{
    use LivewireAlert, WithFileUploads;

    public $internship;
    public $todayAttendance;
    public $activity = '';
    public $photo;

    // Survey Form
    public $isSurveyModalOpen = false;
    public $surveyFeedback = '';

    // Simulate office coordinates for MVP
    const OFFICE_LAT = -6.2088; // e.g. Jakarta
    const OFFICE_LNG = 106.8456;
    const MAX_DISTANCE_KM = 2.0;

    public function mount()
    {
        $this->internship = Auth::user()->internships()->where('status', 'active')->latest()->first();
        if ($this->internship) {
            $this->todayAttendance = InternshipAttendance::where('internship_id', $this->internship->id)
                ->where('date', now()->toDateString())
                ->first();
        }
    }

    public function clockIn($lat, $lng)
    {
        if (!$this->internship) return;
        
        $distance = $this->calculateDistance($lat, $lng, self::OFFICE_LAT, self::OFFICE_LNG);
        if ($distance > self::MAX_DISTANCE_KM) {
            $this->alert('error', 'You are too far from the office.');
            return;
        }

        InternshipAttendance::create([
            'internship_id' => $this->internship->id,
            'date' => now()->toDateString(),
            'time_in' => now()->toTimeString(),
            'latitude_in' => $lat,
            'longitude_in' => $lng,
        ]);
        
        $this->alert('success', 'Clock in successful!');
        $this->todayAttendance = InternshipAttendance::where('internship_id', $this->internship->id)->where('date', now()->toDateString())->first();
    }

    public function clockOut($lat, $lng)
    {
        if (!$this->todayAttendance) return;

        $this->todayAttendance->update([
            'time_out' => now()->toTimeString(),
            'latitude_out' => $lat,
            'longitude_out' => $lng,
        ]);
        
        $this->alert('success', 'Clock out successful!');
    }

    public function saveLog()
    {
        $this->validate([
            'activity' => 'required|string|min:10',
            'photo' => 'nullable|image|max:2048', // 2MB max
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('interns/logs', 'public');
        }

        InternshipLog::create([
            'internship_id' => $this->internship->id,
            'date' => now()->toDateString(),
            'activity' => $this->activity,
            'photo_path' => $photoPath,
            'is_verified' => false,
        ]);

        $this->reset(['activity', 'photo']);
        $this->alert('success', 'Daily log submitted successfully.');
    }

    public function openSurvey()
    {
        if (!$this->internship) return;
        
        $eval = \App\Models\InternshipEvaluation::firstOrNew(['internship_id' => $this->internship->id]);
        $this->surveyFeedback = $eval->program_feedback_intern ?? '';
        $this->isSurveyModalOpen = true;
    }

    public function saveSurvey()
    {
        $this->validate([
            'surveyFeedback' => 'required|string|min:10',
        ]);

        \App\Models\InternshipEvaluation::updateOrCreate(
            ['internship_id' => $this->internship->id],
            [
                'program_feedback_intern' => $this->surveyFeedback,
            ]
        );

        $this->isSurveyModalOpen = false;
        $this->alert('success', 'Thank you for your feedback!');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344); // in KM
    }

    public function render()
    {
        $logs = collect([]);
        $tasksSummary = collect([]);

        if ($this->internship) {
            $logs = InternshipLog::where('internship_id', $this->internship->id)->latest()->take(5)->get();
            $tasksSummary = $this->internship->tasks()->groupBy('status')->selectRaw('status, count(*) as count')->get()->pluck('count', 'status');
        }

        return view('livewire.intern.dashboard', [
            'logs' => $logs,
            'tasksSummary' => $tasksSummary,
        ]);
    }
}
