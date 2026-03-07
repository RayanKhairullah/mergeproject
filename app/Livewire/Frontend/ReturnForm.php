<?php

namespace App\Livewire\Frontend;

use App\Models\Loan;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReturnForm extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public Loan $loan;

    #[Validate('required|integer|min:0')]
    public int $end_mileage = 0;

    #[Validate('required|image|max:2048')]
    public $speedometer_photo;

    public function mount(Loan $loan): void
    {
        if ($loan->status !== 'active') {
            $this->alert('error', 'Peminjaman ini sudah dikembalikan');
            $this->redirect(route('vehicles.monitor'), true);

            return;
        }

        $this->loan = $loan;
        $this->end_mileage = $loan->start_mileage;
    }

    public function submitReturn(): void
    {
        $this->validate();

        if ($this->end_mileage < $this->loan->start_mileage) {
            $this->addError('end_mileage', 'Kilometer akhir tidak boleh lebih kecil dari kilometer awal');

            return;
        }

        // Upload speedometer photo
        $photoPath = $this->speedometer_photo->store('speedometer', 'public');

        // Return vehicle
        $this->loan->returnVehicle($this->end_mileage, $photoPath);

        $this->alert('success', __('vehicles.return_success'));

        $this->redirect(route('vehicles.monitor'), true);
    }

    #[Layout('components.layouts.app.frontend')]
    public function render(): View
    {
        return view('livewire.frontend.return-form', [
            'title' => __('global.pengembalian'),
        ]);
    }
}
