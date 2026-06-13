<?php
namespace App\Http\Controllers\Admin;

use App\Imports\ExcelImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class AdminExcelController extends BaseController
{
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Fichier non valide.');
        }

        try {
            $import = new ExcelImport();
            $stats = $import->import($request->file('file')->getRealPath());

            $message = sprintf(
                'Import fait : %s (%s, %s) — %d services, %d capacités, %d mots-clés, prix : %s.',
                $stats['profession'],
                $stats['provider_type'] ?? 'clientèle non précisée',
                $stats['locale'],
                $stats['services'],
                $stats['capabilities'],
                $stats['keywords'],
                $stats['prices'] ? implode(', ', array_map(
                    static fn ($d, $c) => "{$d} mois {$c}\$",
                    array_keys($stats['prices']),
                    $stats['prices']
                )) : 'aucun'
            );

            if (!empty($stats['warnings'])) {
                $message .= ' Avertissements : ' . implode(' ', $stats['warnings']);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
