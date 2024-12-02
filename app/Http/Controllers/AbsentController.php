<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\SickNote;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AbsentController extends Controller {
    public function generate(): BinaryFileResponse {
        $leaves = Leave::select('leaves.id', 'leaves.motive', 'leaves.date_leave', 'leaves.date_back', 'users.platoon', 'users.rg', 'users.name')
                       ->leftJoin('users', 'leaves.user_id', '=', 'users.id')
                       ->orderBy('users.platoon')
                       ->orderBy('users.name')
                       ->get();

        $sickNotes = SickNote::select('sick_notes.id', 'users.platoon', 'users.rg', 'users.name', 'sick_notes.motive', 'sick_notes.date_issued', 'sick_notes.days_absent')
                             ->leftJoin('users', 'sick_notes.user_id', '=', 'users.id')
                             ->orderBy('users.platoon')
                             ->orderBy('users.name')
                             ->get();

        $data = $this->prepareData($leaves, $sickNotes);

        $filePath = $this->generateExcel($data, 'dispensas_atestados');

        return Response::download($filePath, 'dispensas_atestados_' . date('Y-m-d') . '.xlsx')
                       ->deleteFileAfterSend(true);
    }

    private function prepareData($leaves, $sickNotes) {
        $formattedLeaves = $leaves->map(function ($leave) {
            return [
                'id' => $leave->id,
                'platoon' => $leave->platoon ?? 'N/A',
                'rg' => $leave->rg ?? 'N/A',
                'division' => $this->getDivision($leave->platoon),
                'name' => $leave->name ?? 'N/A',
                'type' => 'Dispensa',
                'date_range' => $leave->date_leave->format('d/m/Y') . ' a ' . $leave->date_back->format('d/m/Y'),
                'total_days' => $leave->date_leave->startOfDay()
                                                  ->diffInDays($leave->date_back->startOfDay()) + 1,
                'motive' => strip_tags($leave->motive) ?? 'N/A',
            ];
        });

        $formattedSickNotes = $sickNotes->map(function ($note) {
            return [
                'id' => $note->id,
                'platoon' => $note->platoon ?? 'N/A',
                'rg' => $note->rg ?? 'N/A',
                'division' => $this->getDivision($note->platoon),
                'name' => $note->name ?? 'N/A',
                'type' => 'Atestado',
                'date_range' => $note->date_issued->format('d/m/Y') . ' a ' . $note->date_issued->addDays($note->days_absent)
                                                                                                ->format('d/m/Y'),
                'total_days' => $note->days_absent,
                'motive' => strip_tags($note->motive) ?? 'N/A',
            ];
        });

        // Merge and sort data
        return $formattedLeaves->merge($formattedSickNotes)
                               ->sortBy(['platoon', 'name']);
    }

    private function getDivision($platoon) {
        if (str_contains($platoon, 'CFO')) {
            return 'Cad';
        }

        if (str_contains($platoon, 'CHOA')) {
            return 'Al Of Adm';
        }
        return 'Al Sd';
    }

    private function generateExcel($data, $filename) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Nº da Dispensa/Atestado', 'Pelotão', 'RG', 'Posto/Grad.', 'Nome', 'Tipo', 'Data', 'Total Dias', 'Motivo'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')
              ->getFont()
              ->setBold(true);

        // Populate data
        $sheet->fromArray($data->toArray(), null, 'A2');

        // Auto-size columns
        foreach (['B', 'E', 'G'] as $col) {
            $sheet->getColumnDimension($col)
                  ->setAutoSize(true);
        }

        $sheet->getColumnDimension('I')
              ->setWidth(100);

        $sheet->getStyle('I')
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_TOP)
              ->setWrapText(true);

        // Save to a temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return $tempFile;
    }


}
