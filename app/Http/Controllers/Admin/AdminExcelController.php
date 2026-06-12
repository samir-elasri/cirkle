<?php
namespace App\Http\Controllers\Admin;

use App\Imports\ExcelImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
            Excel::import($import, $request->file('file'));
            
            return redirect()->back()->with('success', 'Import fait.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
