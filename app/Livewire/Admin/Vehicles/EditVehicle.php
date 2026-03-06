<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditVehicle extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public Vehicle $vehicle;

    #[Validate('required|string|max:15')]
    public string $license_plate = '';

    #[Validate('required|integer|min:0')]
    public int $current_mileage = 0;

    #[Validate('required|in:available,in_use,maintenance')]
    public string $status = 'available';

    #[Validate('nullable|date')]
    public ?string $last_service_date = null;

    #[Validate('nullable|image|max:2048')]
    public $image;

    public ?string $existing_image = null;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->license_plate = $vehicle->license_plate;
        $this->current_mileage = $vehicle->current_mileage;
        $this->status = $vehicle->status;
        $this->last_service_date = $vehicle->last_service_date?->format('Y-m-d');
        $this->existing_image = $vehicle->image;
    }

    public function update(): void
    {
        $this->validate([
            'license_plate' => 'required|string|max:15|unique:vehicles,license_plate,'.$this->vehicle->id,
            'current_mileage' => 'required|integer|min:0',
            'status' => 'required|in:available,in_use,maintenance',
            'last_service_date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'license_plate' => $this->license_plate,
            'current_mileage' => $this->current_mileage,
            'status' => $this->status,
            'last_service_date' => $this->last_service_date,
        ];

        // Handle image upload
        if ($this->image) {
            // Delete old image
            if ($this->vehicle->image) {
                \Storage::disk('public')->delete($this->vehicle->image);
            }
            $data['image'] = $this->convertToWebP($this->image, 'vehicles');
        }

        $this->vehicle->update($data);

        $this->alert('success', 'Kendaraan berhasil diupdate!');

        $this->redirect(route('admin.vehicles.index'), true);
    }

    public function deleteImage(): void
    {
        if ($this->vehicle->image) {
            \Storage::disk('public')->delete($this->vehicle->image);
            $this->vehicle->update(['image' => null]);
            $this->existing_image = null;
            $this->alert('success', 'Foto berhasil dihapus');
        }
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
        return view('livewire.admin.vehicles.edit-vehicle');
    }
}
