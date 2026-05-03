<?php

namespace App\Livewire\Intern;

use App\Models\InternshipTask;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.frontend')]
class Tasks extends Component
{
    use LivewireAlert;

    public $internship;
    public $selectedTask = null;
    public $isViewModalOpen = false;

    public function mount()
    {
        $this->internship = Auth::user()->internships()->where('status', 'active')->latest()->first();
    }

    public function updateStatus($taskId, $status)
    {
        if (!in_array($status, ['todo', 'in_progress', 'done'])) return;
        
        $task = InternshipTask::find($taskId);
        if ($task && $task->internship_id === $this->internship->id) {
            $task->update(['status' => $status]);
            $this->alert('success', 'Task status updated.');
        }
    }

    public function openView($taskId)
    {
        $this->selectedTask = InternshipTask::find($taskId);
        if ($this->selectedTask && $this->selectedTask->internship_id === $this->internship->id) {
            $this->isViewModalOpen = true;
        }
    }

    public function closeView()
    {
        $this->isViewModalOpen = false;
        $this->selectedTask = null;
    }

    public function render()
    {
        $tasks = $this->internship ? InternshipTask::where('internship_id', $this->internship->id)->get() : collect([]);
        return view('livewire.intern.tasks', [
            'todoTasks' => $tasks->where('status', 'todo'),
            'inProgressTasks' => $tasks->where('status', 'in_progress'),
            'doneTasks' => $tasks->where('status', 'done'),
        ]);
    }
}
