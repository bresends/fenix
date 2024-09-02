<?php

namespace App\Http\Controllers;

use App\Enums\FoEnum;
use App\Enums\PlatoonEnum;
use App\Enums\StatusFoEnum;
use App\Models\Fo;
use Illuminate\Support\Facades\Response;

class FOController extends Controller
{
    public function cfp()
    {
        $fos = Fo::select('fos.id', 'users.platoon', 'users.rg', 'users.name')
            ->leftJoin('users', 'fos.user_id', '=', 'users.id')
            ->where('fos.type', FoEnum::Negativo->value)
            ->where('fos.status', StatusFoEnum::DEFERIDO->value)
            ->where('fos.paid', 0)
            ->whereNotIn('users.platoon', PlatoonEnum::CFO())
            ->orderBy('users.platoon')
            ->orderBy('users.name')
            ->get();

        $csvFileName = 'fos_cfp_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function () use ($fos) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['Nº do FO', 'Pelotão', 'RG', 'Posto/Grad.', 'Nome']);

            foreach ($fos as $fo) {
                fputcsv($handle, [
                    $fo->id,
                    $fo->platoon ?? 'N/A',
                    $fo->rg ?? 'N/A',
                    'Al Sd',
                    $fo->name ?? 'N/A'
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
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
            ->get();

        $csvFileName = 'fos_cfo_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function () use ($fos) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['Nº do FO', 'Pelotão', 'RG', 'Posto/Grad.', 'Nome']);

            foreach ($fos as $fo) {
                fputcsv($handle, [
                    $fo->id,
                    $fo->platoon ?? 'N/A',
                    $fo->rg ?? 'N/A',
                    'Cad',
                    $fo->name ?? 'N/A'
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
