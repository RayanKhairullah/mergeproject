<?php

namespace App\Livewire\Admin\Loans;

use App\Models\Loan;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $vehicleFilter = '';

    public string $dateFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
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
        $loan = Loan::findOrFail($id);

        // Delete speedometer photo if exists
        if ($loan->speedometer_photo_url) {
            \Storage::disk('public')->delete($loan->speedometer_photo_url);
        }

        $loan->delete();

        session()->flash('success', __('loans.success_deleted'));
    }

    public function downloadExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $loans = $this->getFilteredLoans();

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LoansExport($loans),
                'laporan-peminjaman-'.now()->format('Y-m-d').'.xlsx'
            );
        } catch (\Exception $e) {
            session()->flash('error', __('loans.error_download', ['message' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    public function downloadPdf()
    {
        try {
            $loans = $this->getFilteredLoans();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.loans-pdf', ['loans' => $loans])
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'laporan-peminjaman-'.now()->format('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            session()->flash('error', __('loans.error_download', ['message' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    protected function getFilteredLoans()
    {
        return Loan::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                })->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereNull('return_date');
            })
            ->when($this->statusFilter === 'returned', function ($query) {
                $query->whereNotNull('return_date');
            })
            ->when($this->vehicleFilter, function ($query) {
                $query->where('vehicle_id', $this->vehicleFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('loan_date', $this->dateFilter);
            })
            ->latest()
            ->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $loans = Loan::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                })->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereNull('return_date');
            })
            ->when($this->statusFilter === 'returned', function ($query) {
                $query->whereNotNull('return_date');
            })
            ->when($this->vehicleFilter, function ($query) {
                $query->where('vehicle_id', $this->vehicleFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('loan_date', $this->dateFilter);
            })
            ->latest()
            ->paginate(10);

        $vehicles = Vehicle::orderBy('license_plate')->get();

        return view('livewire.admin.loans.index', [
            'loans' => $loans,
            'vehicles' => $vehicles,
        ])->title(__('sidebar.loans'));
    }
}
