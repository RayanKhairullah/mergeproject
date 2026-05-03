<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InternshipCertificateController extends Controller
{
    public function download(Internship $internship)
    {
        // Ensure the current user owns this internship or is an admin
        if (auth()->id() !== $internship->user_id && !auth()->user()->hasRole(['super-admin', 'admin', 'hr-admin'])) {
            abort(403);
        }

        $evaluation = $internship->evaluations()->where('is_passed', true)->first();
        
        if (!$evaluation) {
            return back()->with('error', 'Certificate not available yet or requirements not met.');
        }

        $data = [
            'internship' => $internship,
            'user' => $internship->user,
            'evaluation' => $evaluation,
            'date' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('exports.internship-certificate', $data);
        
        return $pdf->download("Certificate_{$internship->user->name}.pdf");
    }
}
