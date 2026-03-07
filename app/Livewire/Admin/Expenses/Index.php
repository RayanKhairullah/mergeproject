<?php

namespace App\Livewire\Admin\Expenses;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $vehicleFilter = '';

    public string $dateFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
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
        $expense = VehicleExpense::findOrFail($id);

        // Delete documentation photos if exists
        if ($expense->documentation_photos && is_array($expense->documentation_photos)) {
            foreach ($expense->documentation_photos as $photo) {
                \Storage::disk('public')->delete($photo);
            }
        }

        $expense->delete();

        session()->flash('success', __('expenses.success_deleted'));
    }

    public function downloadExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $expenses = $this->getFilteredExpenses();

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ExpensesExport($expenses),
                'laporan-rupa-rupa-'.now()->format('Y-m-d').'.xlsx'
            );
        } catch (\Exception $e) {
            session()->flash('error', __('expenses.error_download', ['message' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    public function downloadPdf()
    {
        try {
            $expenses = $this->getFilteredExpenses();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.expenses-pdf', ['expenses' => $expenses])
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'laporan-rupa-rupa-'.now()->format('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            session()->flash('error', __('expenses.error_download', ['message' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    protected function getFilteredExpenses()
    {
        return VehicleExpense::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('expense_type', $this->typeFilter);
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
        $expenses = VehicleExpense::query()
            ->with(['vehicle', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($q) {
                    $q->where('license_plate', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('expense_type', $this->typeFilter);
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

        return view('livewire.admin.expenses.index', [
            'title' => __('sidebar.expenses'),
            'expenses' => $expenses,
            'vehicles' => $vehicles,
        ]);
    }
}
