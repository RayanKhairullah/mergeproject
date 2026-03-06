<?php

namespace App\Livewire\Admin\Inspections;

use App\Models\Inspection;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $timeFilter = '';

    public string $vehicleFilter = '';

    public string $dateFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTimeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $inspection = Inspection::findOrFail($id);

        // Delete speedometer photo if exists
        if ($inspection->speedometer_photo_url) {
            \Storage::disk('public')->delete($inspection->speedometer_photo_url);
        }

        $inspection->delete();

        session()->flash('success', 'Laporan inspeksi berhasil dihapus');
    }

    public function downloadExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $inspections = $this->getFilteredInspections();

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\InspectionsExport($inspections),
                'laporan-inspeksi-'.now()->format('Y-m-d').'.xlsx'
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunduh file: '.$e->getMessage());

            return redirect()->back();
        }
    }

    public function downloadPdf()
    {
        try {
            $inspections = $this->getFilteredInspections();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.inspections-pdf', ['inspections' => $inspections])
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'laporan-inspeksi-'.now()->format('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunduh PDF: '.$e->getMessage());

            return redirect()->back();
        }
    }

    protected function getFilteredInspections()
    {
        return Inspection::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->timeFilter, function ($query) {
                $query->where('inspection_time', $this->timeFilter);
            })
            ->when($this->vehicleFilter, function ($query) {
                $query->where('vehicle_id', $this->vehicleFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->latest()
            ->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $inspections = Inspection::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->timeFilter, function ($query) {
                $query->where('inspection_time', $this->timeFilter);
            })
            ->when($this->vehicleFilter, function ($query) {
                $query->where('vehicle_id', $this->vehicleFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->latest()
            ->paginate(10);

        $vehicles = Vehicle::orderBy('license_plate')->get();

        // Get latest mileage per vehicle with source information
        $vehicleMileages = Vehicle::query()
            ->with(['loans' => fn ($q) => $q->latest()->limit(1), 'inspections' => fn ($q) => $q->latest()->limit(1)])
            ->orderBy('license_plate')
            ->get()
            ->map(function ($vehicle) {
                $latestLoan = $vehicle->loans->first();
                $latestInspection = $vehicle->inspections->first();

                // Determine the most recent source
                $source = 'Initial';
                $sourceDate = null;

                if ($latestLoan && $latestInspection) {
                    if ($latestLoan->updated_at > $latestInspection->updated_at) {
                        $source = 'Peminjaman';
                        $sourceDate = $latestLoan->updated_at;
                    } else {
                        $source = 'Inspeksi';
                        $sourceDate = $latestInspection->updated_at;
                    }
                } elseif ($latestLoan) {
                    $source = 'Peminjaman';
                    $sourceDate = $latestLoan->updated_at;
                } elseif ($latestInspection) {
                    $source = 'Inspeksi';
                    $sourceDate = $latestInspection->updated_at;
                }

                return [
                    'id' => $vehicle->id,
                    'license_plate' => $vehicle->license_plate,
                    'current_mileage' => $vehicle->current_mileage,
                    'source' => $source,
                    'source_date' => $sourceDate,
                ];
            });

        return view('livewire.admin.inspections.index', [
            'inspections' => $inspections,
            'vehicles' => $vehicles,
            'vehicleMileages' => $vehicleMileages,
        ]);
    }
}
