<?php

namespace App\Http\Controllers;

use App\Models\SwitchShift;
use App\Models\Military;
use Barryvdh\DomPDF\Facade\Pdf;

class SwitchShiftPdfController extends Controller
{
    public function __invoke(SwitchShift $record)
    {
        $military = Military::firstWhere('rg', $record->user->rg);
        $evaluated_by = Military::firstWhere('rg', $record->evaluator->rg);

        $asking_military = Military::firstWhere('name', $record->first_shift_paying_military);
        $receiving_military = Military::firstWhere('name', $record->first_shift_receiving_military);

        return Pdf::loadView('switchShiftPdf', [
            'record' => $record,
            'military' => $military,
            'evaluated_by' => $evaluated_by,
            'asking_military' => $asking_military,
            'receiving_military' => $receiving_military
        ])
            ->stream($record->id . '.pdf');
    }
}
