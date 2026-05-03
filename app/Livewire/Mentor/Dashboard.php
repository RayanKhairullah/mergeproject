<?php

namespace App\Livewire\Mentor;

use App\Models\Internship;
use App\Models\InternshipTask;
use App\Models\InternshipLog;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app.frontend')]
class Dashboard extends Component
{
    use LivewireAlert, WithFileUploads;

    public $activeInternshipId = null;

    // Task Form
    public $isTaskModalOpen = false;
    public $taskTitle = '';
    public $taskDescription = '';
    public $taskDeadline = '';
    public $taskFile;

    // Evaluation Form
    public $isEvalModalOpen = false;
    public $evalTechnical = 0;
    public $evalCommunication = 0;
    public $evalTeamwork = 0;
    public $evalDiscipline = 0;
    public $evalFeedback = '';
    public $evalPassed = false;

    public function mount()
    {
        $myInternships = Auth::user()->mentoredInternships()->where('status', 'active')->get();
        if ($myInternships->isNotEmpty()) {
            $this->activeInternshipId = $myInternships->first()->id;
        }
    }

    public function selectInternship($id)
    {
        $this->activeInternshipId = $id;
    }

    public function verifyLog($logId)
    {
        $log = InternshipLog::find($logId);
        if ($log && $log->internship->mentor_id === Auth::id()) {
            $log->update(['is_verified' => true]);
            $this->alert('success', 'Log verified.');
        }
    }

    public function rateTask($taskId, $rating)
    {
        if ($rating < 1 || $rating > 5) return;

        $task = InternshipTask::find($taskId);
        if ($task && $task->internship->mentor_id === Auth::id() && $task->status === 'done') {
            $task->update(['rating' => $rating]);
            $this->alert('success', "Task rated $rating stars.");
        }
    }

    public function createTask()
    {
        $this->reset(['taskTitle', 'taskDescription', 'taskDeadline', 'taskFile']);
        $this->isTaskModalOpen = true;
    }

    public function saveTask()
    {
        $this->validate([
            'taskTitle' => 'required|string|max:255',
            'taskDescription' => 'nullable|string',
            'taskDeadline' => 'nullable|date',
            'taskFile' => 'nullable|file|max:5120', // 5MB max
            'activeInternshipId' => 'required',
        ]);

        $filePath = null;
        if ($this->taskFile) {
            $filePath = $this->taskFile->store('tasks/attachments', 'public');
        }

        InternshipTask::create([
            'internship_id' => $this->activeInternshipId,
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'deadline' => $this->taskDeadline ?: null,
            'attachment_path' => $filePath,
            'status' => 'todo',
        ]);

        $this->isTaskModalOpen = false;
        $this->alert('success', 'Task assigned to intern successfully.');
    }

    public function openEvaluation()
    {
        if (!$this->activeInternshipId) return;
        
        $eval = \App\Models\InternshipEvaluation::firstOrNew(['internship_id' => $this->activeInternshipId]);
        $this->evalTechnical = $eval->technical_skill ?? 0;
        $this->evalCommunication = $eval->communication_skill ?? 0;
        $this->evalTeamwork = $eval->teamwork_skill ?? 0;
        $this->evalDiscipline = $eval->discipline_skill ?? 0;
        $this->evalFeedback = $eval->mentor_feedback ?? '';
        $this->evalPassed = $eval->is_passed;

        $this->isEvalModalOpen = true;
    }

    public function saveEvaluation()
    {
        $this->validate([
            'evalTechnical' => 'required|integer|min:1|max:5',
            'evalCommunication' => 'required|integer|min:1|max:5',
            'evalTeamwork' => 'required|integer|min:1|max:5',
            'evalDiscipline' => 'required|integer|min:1|max:5',
            'evalFeedback' => 'required|string|min:10',
        ]);

        \App\Models\InternshipEvaluation::updateOrCreate(
            ['internship_id' => $this->activeInternshipId],
            [
                'technical_skill' => $this->evalTechnical,
                'communication_skill' => $this->evalCommunication,
                'teamwork_skill' => $this->evalTeamwork,
                'discipline_skill' => $this->evalDiscipline,
                'mentor_feedback' => $this->evalFeedback,
                'is_passed' => $this->evalPassed,
                'is_completed' => true,
            ]
        );

        $this->isEvalModalOpen = false;
        $this->alert('success', 'Internship evaluation saved successfully.');
    }

    public function render()
    {
        $myInternships = Auth::user()->mentoredInternships()->where('status', 'active')->with('user')->get();
        
        $activeInternData = null;
        if ($this->activeInternshipId) {
            $activeInternData = Internship::with(['user', 'tasks' => function($query) {
                $query->orderBy('status', 'desc'); // simple sort
            }, 'logs' => function($query) {
                $query->orderBy('date', 'desc')->take(10);
            }])->find($this->activeInternshipId);
        }

        return view('livewire.mentor.dashboard', [
            'internships' => $myInternships,
            'activeIntern' => $activeInternData,
        ]);
    }
}
