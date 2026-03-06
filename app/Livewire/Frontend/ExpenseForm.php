<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExpenseForm extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    #[Validate('required|exists:vehicles,id')]
    public ?int $vehicle_id = null;

    #[Validate('required|string|max:255')]
    public string $reporter_name = '';

    #[Validate('required|in:BBM,E-Money,Parkir,Cuci Mobil,Lainnya')]
    public string $expense_type = 'BBM';

    #[Validate('required|in:UANG_MUKA,UANG_PRIBADI,KOPERASI_KONSUMEN_SUKA_BAHARI')]
    public string $funding_source = 'UANG_MUKA';

    #[Validate('nullable|in:PERTALITE,PERTAMAX,PERTADEX,PERTAMAX TURBO,Lainnya')]
    public ?string $fuel_type = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $fuel_liters = null;

    #[Validate('required|numeric|min:0')]
    public float $nominal = 0;

    public array $documentation_photos = [];

    #[Validate('nullable|image|max:2048')]
    public $payment_proof;

    #[Validate('nullable|image|max:2048')]
    public $vehicle_photo;

    #[Validate('nullable|image|max:2048')]
    public $fuel_indicator;

    #[Validate('nullable|image|max:2048')]
    public $activity_photo;

    #[Validate('nullable|string')]
    public ?string $notes = null;

    #[Validate('required|integer|min:0')]
    public int $current_mileage = 0;

    public array $fuelPrices = [
        'PERTALITE' => 10000,
        'PERTAMAX' => 11800,
        'PERTAMAX TURBO' => 12700,
        'DEXLITE' => 13250,
        'PERTADEX' => 13500,
    ];

    public function updatedExpenseType(): void
    {
        if ($this->expense_type !== 'BBM') {
            $this->fuel_type = null;
            $this->fuel_liters = null;
        }
    }

    public function updatedVehicleId(): void
    {
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $this->current_mileage = $vehicle->current_mileage;
                $this->resetErrorBag('current_mileage');
            }
        } else {
            $this->current_mileage = 0;
        }
    }

    public function updatedCurrentMileage(): void
    {
        if ($this->vehicle_id && $this->current_mileage) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle && $this->current_mileage < $vehicle->current_mileage) {
                $this->addError('current_mileage', 'Kilometer tidak boleh lebih kecil dari posisi terakhir ('.number_format($vehicle->current_mileage).' km)');
            } else {
                $this->resetErrorBag('current_mileage');
            }
        }
    }

    public function updatedFuelType(): void
    {
        $this->calculateAmount();
    }

    public function updatedFuelLiters(): void
    {
        $this->calculateAmount();
    }

    protected function calculateAmount(): void
    {
        if ($this->expense_type === 'BBM' && $this->fuel_type && $this->fuel_liters) {
            $price = $this->fuelPrices[$this->fuel_type] ?? 0;
            $this->nominal = $price * $this->fuel_liters;
        }
    }

    public function clearPaymentProof(): void
    {
        $this->payment_proof = null;
        $this->resetErrorBag('payment_proof');
    }

    public function clearVehiclePhoto(): void
    {
        $this->vehicle_photo = null;
        $this->resetErrorBag('vehicle_photo');
    }

    public function clearFuelIndicator(): void
    {
        $this->fuel_indicator = null;
        $this->resetErrorBag('fuel_indicator');
    }

    public function clearActivityPhoto(): void
    {
        $this->activity_photo = null;
        $this->resetErrorBag('activity_photo');
    }

    public function submitExpense(): void
    {
        // Build the documentation photos array based on uploaded files
        $photos = [];

        // Convert and store images as WebP
        if ($this->payment_proof) {
            $photos['payment_proof'] = $this->convertToWebP($this->payment_proof, 'expenses');
        }

        if ($this->expense_type === 'BBM') {
            if ($this->vehicle_photo) {
                $photos['vehicle_photo'] = $this->convertToWebP($this->vehicle_photo, 'expenses');
            }
            if ($this->fuel_indicator) {
                $photos['fuel_indicator'] = $this->convertToWebP($this->fuel_indicator, 'expenses');
            }
        } else {
            if ($this->activity_photo) {
                $photos['activity_photo'] = $this->convertToWebP($this->activity_photo, 'expenses');
            }
        }

        // Validate based on expense type
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'reporter_name' => 'required|string|max:255',
            'expense_type' => 'required|in:BBM,E-Money,Parkir,Cuci Mobil,Lainnya',
            'funding_source' => 'required|in:UANG_MUKA,UANG_PRIBADI,KOPERASI_KONSUMEN_SUKA_BAHARI',
            'nominal' => 'required|numeric|min:0',
            'current_mileage' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ];

        // Add fuel-specific validation for BBM
        if ($this->expense_type === 'BBM') {
            $rules['fuel_type'] = 'required|in:PERTALITE,PERTAMAX,PERTADEX,PERTAMAX TURBO,Lainnya';
            $rules['fuel_liters'] = 'required|numeric|min:0';
        }

        // Validate required photos based on expense type
        if ($this->expense_type === 'BBM') {
            if (! $this->payment_proof) {
                $this->addError('payment_proof', 'Bukti pembayaran wajib diupload');

                return;
            }
            if (! $this->vehicle_photo) {
                $this->addError('vehicle_photo', 'Foto kendaraan belakang wajib diupload');

                return;
            }
            if (! $this->fuel_indicator) {
                $this->addError('fuel_indicator', 'Foto indikator BBM wajib diupload');

                return;
            }
        } elseif ($this->expense_type !== 'Parkir') {
            if (! $this->payment_proof) {
                $this->addError('payment_proof', 'Bukti pembayaran wajib diupload');

                return;
            }
            if (! $this->activity_photo) {
                $this->addError('activity_photo', 'Foto kegiatan wajib diupload');

                return;
            }
        } else {
            // For Parkir, only activity photo is required
            if (! $this->activity_photo) {
                $this->addError('activity_photo', 'Foto kegiatan wajib diupload');

                return;
            }
        }

        $this->validate($rules);

        // Validate mileage
        $vehicle = Vehicle::find($this->vehicle_id);
        if ($vehicle && $this->current_mileage < $vehicle->current_mileage) {
            $this->addError('current_mileage', 'Kilometer tidak boleh lebih kecil dari posisi terakhir');

            return;
        }

        VehicleExpense::create([
            'vehicle_id' => $this->vehicle_id,
            'user_id' => auth()->id(),
            'reporter_name' => $this->reporter_name,
            'expense_type' => $this->expense_type,
            'funding_source' => $this->funding_source,
            'fuel_type' => $this->fuel_type,
            'fuel_liters' => $this->fuel_liters,
            'nominal' => $this->nominal,
            'documentation_photos' => $photos,
            'notes' => $this->notes,
        ]);

        // Update vehicle mileage if changed
        if ($vehicle && $this->current_mileage !== $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $this->current_mileage]);
        }

        $this->alert('success', 'Expense berhasil disimpan!');

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
        $users = \App\Models\User::query()
            ->orderBy('name')
            ->get();

        return view('livewire.frontend.expense-form', [
            'vehicles' => $vehicles,
            'users' => $users,
        ]);
    }
}
