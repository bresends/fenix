<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\SickNote;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AbsentController extends Controller
{
    public function generate(): BinaryFileResponse
    {
        $leaves = Leave::select('leaves.id', 'users.platoon', 'users.rg', 'users.name', 'leaves.motive')
            ->leftJoin('users', 'leaves.user_id', '=', 'users.id')
            ->orderBy('users.platoon')
            ->orderBy('users.name')
            ->orderBy('leaves.id')
            ->get();

        $sick_notes = SickNote::select('sick_notes.id', 'users.platoon', 'users.rg', 'users.name', 'sick_notes.motive')
            ->leftJoin('users', 'sick_notes.user_id', '=', 'users.id')
            ->orderBy('users.platoon')
            ->orderBy('users.name')
            ->orderBy('sick_notes.id')
            ->get();

        $groupedData = $leaves->merge($sick_notes)->groupBy('user_id');

        $groupedData = $groupedData->sortBy(function ($item) {
            return $item->first()->platoon . $item->first()->name;
        });

        $excelFile = $this->generateExcel($groupedData, 'dispensas_atestados');

        return Response::download($excelFile, 'dispensas_atestados_' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }

    private function generateExcel($groupedData, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dispensas');

        // Updated headers with "Type" column
        $headers = ['Nº da Dispensa/Atestado', 'Pelotão', 'RG', 'Posto/Grad.', 'Nome', 'Motivo', 'Tipo'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Sort by Platoon and Name before adding to the Excel sheet
        $sortedData = $groupedData->flatten()->sortBy(function ($item) {
            return $item->platoon . $item->name;
        });

        // Populate data
        $row = 2;
        $division = 'Al Sd';
        foreach ($sortedData as $leave) {
            if (str_contains($leave->platoon, 'CFO')) {
                $division = 'Cad';
            } elseif (str_contains($leave->platoon, 'CHOA')) {
                $division = 'Al Of Adm';
            }

            // Determine the type (Dispensa or Estado)
            $type = $leave instanceof Leave ? 'Dispensa' : 'Atestado';

            $sheet->setCellValue('A' . $row, $leave->id);
            $sheet->setCellValue('B' . $row, $leave->platoon ?? 'N/A');
            $sheet->setCellValue('C' . $row, $leave->rg ?? 'N/A');
            $sheet->setCellValue('D' . $row, $division);
            $sheet->setCellValue('E' . $row, $leave->name ?? 'N/A');
            $sheet->setCellValue('F' . $row, $type); // Add type column
            $sheet->setCellValue('G' . $row, strip_tags($leave->motive) ?? 'N/A');
            $row++;
        }

        // Auto-size the name columns
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setWidth(100);
        $sheet->getStyle('G1:G' . ($row - 1))->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        $sheet->getStyle('A1:G' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return $tempFile;
    }


}
