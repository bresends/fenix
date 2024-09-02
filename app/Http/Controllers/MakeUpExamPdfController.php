<?php

namespace App\Http\Controllers;

use App\Models\MakeUpExam;
use App\Models\Military;
use Barryvdh\DomPDF\Facade\Pdf;

class MakeUpExamPdfController extends Controller
{
    public function __invoke(MakeUpExam $record)
    {
        $military = Military::firstWhere('rg', $record->user->rg);

        return Pdf::loadView('makeUpExamPdf', ['record' => $record, 'military' => $military])
            ->stream($record->id . '.pdf');
    }
}
