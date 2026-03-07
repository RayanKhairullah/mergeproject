<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    // Modal / form state
    public bool $showForm = false;

    public ?int $editingVehicleId = null;

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

    public ?string $existingImage = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function showEditForm(int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $this->editingVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate;
        $this->current_mileage = $vehicle->current_mileage;
        $this->status = $vehicle->status;

        /** @var \Illuminate\Support\Carbon|null $lastServiceDate */
        $lastServiceDate = $vehicle->last_service_date;
        $this->last_service_date = $lastServiceDate?->format('Y-m-d');

        $this->existingImage = $vehicle->image;

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'license_plate' => $this->license_plate,
            'current_mileage' => $this->current_mileage,
            'status' => $this->status,
            'last_service_date' => $this->last_service_date,
        ];

        if ($this->image) {
            $data['image'] = $this->convertToWebP($this->image, 'vehicles');
        }

        Vehicle::create($data);

        $this->alert('success', __('vehicles.success_added'));
        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function update(): void
    {
        $this->validate([
            'license_plate' => 'required|string|max:15|unique:vehicles,license_plate,'.$this->editingVehicleId,
            'current_mileage' => 'required|integer|min:0',
            'status' => 'required|in:available,in_use,maintenance',
            'last_service_date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $vehicle = Vehicle::findOrFail($this->editingVehicleId);

        $data = [
            'license_plate' => $this->license_plate,
            'current_mileage' => $this->current_mileage,
            'status' => $this->status,
            'last_service_date' => $this->last_service_date,
        ];

        if ($this->image) {
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }

            $data['image'] = $this->convertToWebP($this->image, 'vehicles');
        }

        $vehicle->update($data);

        $this->alert('success', __('vehicles.success_updated'));
        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $vehicle = Vehicle::findOrFail($id);

        // Check if vehicle has active loans
        if ($vehicle->loans()->whereNull('return_date')->exists()) {
            session()->flash('error', __('vehicles.error_cannot_delete_in_use'));

            return;
        }

        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        session()->flash('success', __('vehicles.success_deleted'));
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->editingVehicleId = null;
        $this->license_plate = '';
        $this->current_mileage = 0;
        $this->status = 'available';
        $this->last_service_date = null;
        $this->image = null;
        $this->existingImage = null;
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

        /** @noinspection PhpDeprecationInspection */
        imagedestroy($imageResource);

        unlink($fullPath);

        return $directory.'/'.$filename;
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->when($this->search, function ($query) {
                $query->where('license_plate', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('license_plate')
            ->paginate(10);

        return view('livewire.admin.vehicles.index', [
            'title' => __('sidebar.vehicles'),
            'vehicles' => $vehicles,
        ]);
    }
}
