<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalVehicles = 0;
    public int $activeLoans = 0;
    public int $upcomingMeetings = 0;
    public int $totalBooks = 0;

    public function mount(): void
    {
        $this->totalVehicles = Vehicle::count();
        $this->activeLoans = Loan::where('status', 'active')->count();
        $this->upcomingMeetings = Meeting::where('started_at', '>', now())->count();
        $this->totalBooks = Book::count();
    }

    #[Layout('components.layouts.app.frontend')]
    public function render(): View
    {
        return view('livewire.dashboard')->title(__('sidebar.dashboard'));
    }
}
