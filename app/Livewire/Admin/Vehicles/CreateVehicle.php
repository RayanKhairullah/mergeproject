<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateVehicle extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    #[Validate('required|string|max:15|unique:vehicles,license_plate')]
    public string $license_plate = '';

    #[Validate('required|integer|min:0')]
    public int $current_mileage = 0;

    #[Validate('required|in:available,in_use,maintenance')]
    public string $status = 'available';

    #[Validate('nullable|date')]
    public ?string $last_service_date = null;

    #[Validate('nullable|image|max:2048')]
    public $image;

    public function save(): void
    {
        $this->validate();

        $data = [
            'license_plate' => $this->license_plate,
            'current_mileage' => $this->current_mileage,
            'status' => $this->status,
            'last_service_date' => $this->last_service_date,
        ];

        // Handle image upload
        if ($this->image) {
            $data['image'] = $this->convertToWebP($this->image, 'vehicles');
        }

        Vehicle::create($data);

        $this->alert('success', 'Kendaraan berhasil ditambahkan!');

        $this->redirect(route('admin.vehicles.index'), true);
    }

    protected function convertToWebP($image, string $directory): string
    {
        $imagePath = $image->store('temp', 'public');
        $fullPath = storage_path('app/public/'.$imagePath);

        $imageInfo = getimagesize($fullPath);
        $mimeType = $imageInfo['mime'];

        $imageResource = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($fullPath),
            'image/png' => imagecreatefrompng($fullPath),
            'image/gif' => imagecreatefromgif($fullPath),
            'image/webp' => imagecreatefromwebp($fullPath),
            default => null,
        };

        if (! $imageResource) {
            return $image->store($directory, 'public');
        }

        $filename = uniqid().'_'.time().'.webp';
        $webpPath = storage_path('app/public/'.$directory.'/'.$filename);

        if (! file_exists(storage_path('app/public/'.$directory))) {
            mkdir(storage_path('app/public/'.$directory), 0755, true);
        }

        imagewebp($imageResource, $webpPath, 80);
        imagedestroy($imageResource);
        unlink($fullPath);

        return $directory.'/'.$filename;
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.vehicles.create-vehicle');
    }
}
