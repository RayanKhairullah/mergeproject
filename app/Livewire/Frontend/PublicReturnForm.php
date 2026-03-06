<?php

namespace App\Livewire\Frontend;

use App\Models\Loan;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PublicReturnForm extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public ?int $loan_id = null;

    public ?Loan $selectedLoan = null;

    #[Validate('required|integer|min:0')]
    public int $end_mileage = 0;

    #[Validate('nullable|image|max:2048')]
    public $speedometer_photo;

    public array $activeLoans = [];

    public function mount(): void
    {
        // Active loans will be loaded from browser cache via JavaScript
    }

    public function updatedEndMileage(): void
    {
        if ($this->selectedLoan && $this->end_mileage < $this->selectedLoan->start_mileage) {
            $this->addError('end_mileage', 'Kilometer akhir tidak boleh lebih kecil dari kilometer awal ('.number_format($this->selectedLoan->start_mileage).' km)');
        } else {
            $this->resetErrorBag('end_mileage');
        }
    }

    public function selectLoan(): void
    {
        if (empty($this->activeLoans)) {
            $this->addError('loan_id', 'Tidak ada peminjaman aktif yang tersedia');

            return;
        }

        if (! $this->loan_id) {
            $this->addError('loan_id', 'Silakan pilih peminjam terlebih dahulu');

            return;
        }

        $this->resetErrorBag('loan_id');

        $loan = Loan::with('vehicle', 'user')->find($this->loan_id);

        if (! $loan) {
            $this->addError('loan_id', 'Peminjaman tidak ditemukan');

            return;
        }

        if ($loan->status !== 'active') {
            $this->alert('error', 'Peminjaman ini sudah dikembalikan');
            $this->loan_id = null;

            return;
        }

        $this->selectedLoan = $loan;
        $this->end_mileage = $loan->start_mileage;
    }

    public function clearPhoto(): void
    {
        $this->speedometer_photo = null;
        $this->resetErrorBag('speedometer_photo');
    }

    public function submitReturn(): void
    {
        if (! $this->selectedLoan) {
            $this->alert('error', 'Silakan pilih peminjaman terlebih dahulu');

            return;
        }

        $this->validate([
            'end_mileage' => 'required|integer|min:'.$this->selectedLoan->start_mileage,
            'speedometer_photo' => 'required|image|max:2048',
        ]);

        // Convert and store image as WebP
        $photoPath = $this->convertToWebP($this->speedometer_photo, 'speedometer');

        // Return vehicle
        $this->selectedLoan->returnVehicle($this->end_mileage, $photoPath);

        // Dispatch event to remove loan from cache
        $this->dispatch('remove-loan-from-cache', ['loanId' => $this->selectedLoan->id]);

        $this->alert('success', __('vehicles.return_success'));

        $this->redirect(route('vehicles.monitor'), true);
    }

    protected function convertToWebP($image, string $directory): string
    {
        $imagePath = $image->store('temp', 'public');
        $fullPath = storage_path('app/public/'.$imagePath);

        // Get image info
        $imageInfo = getimagesize($fullPath);
        $mimeType = $imageInfo['mime'];

        // Create image resource based on type
        $imageResource = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($fullPath),
            'image/png' => imagecreatefrompng($fullPath),
            'image/gif' => imagecreatefromgif($fullPath),
            'image/webp' => imagecreatefromwebp($fullPath),
            default => null,
        };

        if (! $imageResource) {
            // Fallback: just store the original
            return $image->store($directory, 'public');
        }

        // Generate WebP filename
        $filename = uniqid().'_'.time().'.webp';
        $webpPath = storage_path('app/public/'.$directory.'/'.$filename);

        // Ensure directory exists
        if (! file_exists(storage_path('app/public/'.$directory))) {
            mkdir(storage_path('app/public/'.$directory), 0755, true);
        }

        // Convert to WebP
        imagewebp($imageResource, $webpPath, 80);

        // Free memory
        imagedestroy($imageResource);

        // Delete temp file
        unlink($fullPath);

        return $directory.'/'.$filename;
    }

    #[Layout('components.layouts.app.frontend')]
    public function render(): View
    {
        return view('livewire.frontend.public-return-form');
    }
}
