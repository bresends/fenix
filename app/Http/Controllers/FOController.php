<?php

namespace App\Http\Controllers;

use App\Enums\FoEnum;
use App\Enums\PlatoonEnum;
use App\Enums\StatusFoEnum;
use App\Models\Fo;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FOController extends Controller
{
    public function cfp(): BinaryFileResponse
    {
        $fos = Fo::select('fos.id', 'users.platoon', 'users.rg', 'users.name')
            ->leftJoin('users', 'fos.user_id', '=', 'users.id')
            ->where('fos.type', FoEnum::Negativo->value)
            ->where('fos.status', StatusFoEnum::DEFERIDO->value)
            ->where('fos.paid', 0)
            ->whereNotIn('users.platoon', PlatoonEnum::CFO())
            ->orderBy('users.platoon')
            ->orderBy('users.name')
            ->orderBy('fos.id')
            ->get();

        $excelFile = $this->generateExcel($fos, 'fos_cfp');

        return Response::download($excelFile, 'fos_cfp_' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }

    public function cfo()
    {
        $fos = Fo::select('fos.id', 'users.platoon', 'users.rg', 'users.name')
            ->leftJoin('users', 'fos.user_id', '=', 'users.id')
            ->where('fos.type', 'Negativo')
            ->where('fos.status', 'Deferido')
            ->where('fos.paid', 0)
            ->whereIn('users.platoon', PlatoonEnum::CFO())
            ->orderBy('users.platoon')
            ->orderBy('users.name')
            ->orderBy('fos.id')
            ->get();

        $excelFile = $this->generateExcel($fos, 'fos_cfo');

        return Response::download($excelFile, 'fos_cfo_' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }

    private function generateExcel($fos, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Nº do FO', 'Pelotão', 'RG', 'Posto/Grad.', 'Nome'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Populate data
        $row = 2;
        foreach ($fos as $fo) {
            $sheet->setCellValue('A' . $row, $fo->id);
            $sheet->setCellValue('B' . $row, $fo->platoon ?? 'N/A');
            $sheet->setCellValue('C' . $row, $fo->rg ?? 'N/A');
            $sheet->setCellValue('D' . $row, $filename === 'fos_cfo' ? 'Cad' : 'Al Sd');
            $sheet->setCellValue('E' . $row, $fo->name ?? 'N/A');
            $row++;
        }

        // Auto-size the name columns
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:E' . ($row - 1))->applyFromArray($borderStyle);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return $tempFile;
    }
}
