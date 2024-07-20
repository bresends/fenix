<?php

namespace App\Http\Controllers;

use App\Models\ExamAppeal;
use App\Models\Military;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamAppealPdfController extends Controller
{
    public function __invoke(ExamAppeal $record)
    {
        $military = Military::firstWhere('rg', $record->user->rg);

        return Pdf::loadView('examPdf', ['record' => $record, 'military' => $military])
            ->stream($record->id . '.pdf');
    }
}
