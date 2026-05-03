<?php

namespace App\Livewire\Admin\Internships;

use App\Models\Division;
use App\Models\Internship;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use LivewireAlert;
    use WithPagination;
    use WithFileUploads;

    public $isModalOpen = false;
    public $editingId = null;

    // Form inputs
    public $intern_id = '';
    public $mentor_id = '';
    public $division_id = '';
    public $position = '';
    public $department = '';
    public $start_date = '';
    public $end_date = '';
    public $contract = null;
    public $status = 'active';

    public function rules()
    {
        return [
            'intern_id' => 'required|exists:users,id',
            'mentor_id' => 'required|exists:users,id',
            'division_id' => 'required|exists:divisions,id',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract' => 'nullable|file|mimes:pdf|max:10240', // 10MB, PDF only
            'status' => 'required|in:active,completed,terminated',
        ];
    }

    public function create()
    {
        $this->reset(['editingId', 'intern_id', 'mentor_id', 'division_id', 'position', 'department', 'start_date', 'end_date', 'contract', 'status']);
        $this->isModalOpen = true;
    }

    public function edit(Internship $internship)
    {
        $this->editingId = $internship->id;
        $this->intern_id = $internship->user_id;
        $this->mentor_id = $internship->mentor_id;
        $this->division_id = $internship->division_id;
        $this->position = $internship->position;
        $this->department = $internship->department;
        $this->start_date = $internship->start_date->format('Y-m-d');
        $this->end_date = $internship->end_date->format('Y-m-d');
        $this->status = $internship->status;

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => $this->intern_id,
            'mentor_id' => $this->mentor_id,
            'division_id' => $this->division_id,
            'position' => $this->position,
            'department' => $this->department,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ];

        if ($this->contract) {
            $data['contract_path'] = $this->contract->store('interns/contracts', 'public');
        }

        if ($this->editingId) {
            Internship::find($this->editingId)->update($data);
            $this->alert('success', 'Internship updated successfully.');
        } else {
            $internship = Internship::create($data);
            
            // Assign roles automatically
            $intern = User::find($this->intern_id);
            if (!$intern->hasRole('intern')) {
                $intern->assignRole('intern');
            }

            $mentor = User::find($this->mentor_id);
            if (!$mentor->hasRole('mentor')) {
                $mentor->assignRole('mentor');
            }

            $this->alert('success', 'Internship created successfully.');
        }

        $this->closeModal();
    }

    public function delete(Internship $internship)
    {
        $internship->delete();
        $this->alert('success', 'Internship deleted successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function render()
    {
        return view('livewire.admin.internships.index', [
            'internships' => Internship::with(['user', 'mentor', 'division'])->latest()->paginate(10),
            'divisions' => Division::all(),
            'interns' => User::role('intern')->orderBy('name')->get(),
            'mentors' => User::role('mentor')->orderBy('name')->get(),
        ]);
    }
}
