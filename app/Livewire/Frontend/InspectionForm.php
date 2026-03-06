<?php

namespace App\Livewire\Frontend;

use App\Models\Inspection;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class InspectionForm extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    #[Validate('required|exists:vehicles,id')]
    public ?int $vehicle_id = null;

    #[Validate('required|in:morning,afternoon')]
    public string $inspection_time = 'morning';

    // Tire condition
    public string $tire_condition_type = 'good';

    public string $tire_condition_notes = '';

    // Body condition
    public string $body_condition_type = 'good';

    public string $body_condition_notes = '';

    // Glass condition
    public string $glass_condition_type = 'good';

    public string $glass_condition_notes = '';

    public array $issue_photos = [];

    #[Validate('required|integer|min:0')]
    public int $mileage_check = 0;

    #[Validate('nullable|image|max:2048')]
    public $speedometer_photo;

    #[Validate('nullable|string')]
    public ?string $additional_notes = null;

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->can('access dashboard')) {
            abort(403, 'Unauthorized access. Only administrators can access this page.');
        }
    }

    public function updatedVehicleId(): void
    {
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $this->mileage_check = $vehicle->current_mileage;
                $this->resetErrorBag('mileage_check');
            }
        } else {
            $this->mileage_check = 0;
        }
    }

    public function updatedMileageCheck(): void
    {
        if ($this->vehicle_id && $this->mileage_check) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle && $this->mileage_check < $vehicle->current_mileage) {
                $this->addError('mileage_check', 'Kilometer tidak boleh lebih kecil dari posisi terakhir ('.number_format($vehicle->current_mileage).' km)');
            } else {
                $this->resetErrorBag('mileage_check');
            }
        }
    }

    public function clearPhoto(): void
    {
        $this->speedometer_photo = null;
        $this->resetErrorBag('speedometer_photo');
    }

    public function clearIssuePhoto(int $index): void
    {
        if (isset($this->issue_photos[$index])) {
            unset($this->issue_photos[$index]);
        }
    }

    public function submitInspection(): void
    {
        // Build condition strings based on radio selection
        $tireCondition = $this->tire_condition_type === 'good' ? 'Good' : $this->tire_condition_notes;
        $bodyCondition = $this->body_condition_type === 'good' ? 'Good' : $this->body_condition_notes;
        $glassCondition = $this->glass_condition_type === 'good' ? 'Good' : $this->glass_condition_notes;

        $this->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'inspection_time' => 'required|in:morning,afternoon',
            'mileage_check' => 'required|integer|min:0',
            'speedometer_photo' => 'required|image|max:2048',
            'additional_notes' => 'nullable|string',
        ]);

        // Validate condition notes if "lainnya" is selected
        if ($this->tire_condition_type === 'other' && empty($this->tire_condition_notes)) {
            $this->addError('tire_condition_notes', 'Catatan kondisi ban harus diisi');

            return;
        }
        if ($this->body_condition_type === 'other' && empty($this->body_condition_notes)) {
            $this->addError('body_condition_notes', 'Catatan kondisi body harus diisi');

            return;
        }
        if ($this->glass_condition_type === 'other' && empty($this->glass_condition_notes)) {
            $this->addError('glass_condition_notes', 'Catatan kondisi kaca harus diisi');

            return;
        }

        // Validate mileage
        $vehicle = Vehicle::find($this->vehicle_id);
        if ($vehicle && $this->mileage_check < $vehicle->current_mileage) {
            $this->addError('mileage_check', 'Kilometer tidak boleh lebih kecil dari posisi terakhir');

            return;
        }

        $issuePhotosPaths = [];
        foreach ($this->issue_photos as $photo) {
            if ($photo) {
                $issuePhotosPaths[] = $this->convertToWebP($photo, 'inspections');
            }
        }

        $speedometerPath = $this->convertToWebP($this->speedometer_photo, 'speedometer');

        Inspection::create([
            'vehicle_id' => $this->vehicle_id,
            'user_id' => auth()->id(),
            'inspection_time' => $this->inspection_time,
            'tire_condition' => $tireCondition,
            'body_condition' => $bodyCondition,
            'glass_condition' => $glassCondition,
            'issue_photos' => $issuePhotosPaths,
            'mileage_check' => $this->mileage_check,
            'speedometer_photo_url' => $speedometerPath,
            'additional_notes' => $this->additional_notes,
        ]);

        // Update vehicle mileage if changed
        if ($vehicle && $this->mileage_check !== $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $this->mileage_check]);
        }

        $this->alert('success', 'Inspeksi berhasil disimpan!');

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
        $vehicles = Vehicle::query()->orderBy('license_plate')->get();

        return view('livewire.frontend.inspection-form', [
            'vehicles' => $vehicles,
        ]);
    }
}
