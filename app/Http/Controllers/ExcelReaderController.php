<?php

namespace App\Http\Controllers;

use App\AdditionalClasses\Date;
use Exception;
use App\Models\ExcelReader;
use App\Imports\SalesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelReaderController extends Controller
{
    public function import(Request $request)
    {
        $validate_data = array(
            'file' => ['required', 'file', 'mimes:xlsx', 'max:2048'],
            'date' => ['required', 'string']
        );
        $validatedData = $this->validate($request, $validate_data);

        try {
            $i = 0;
            $collection = Excel::toCollection(new SalesImport, $request->file, null,  \Maatwebsite\Excel\Excel::XLSX);
            $collection->each(function($sheet) use(&$i, $validatedData) {
                $sheet->each(function($row) use(&$i, $validatedData) {
                    if (count($row) and $row[0]) {
                        $sales = new ExcelReader();
                        $sales->user_id = $row[0] ?? 0;
                        $sales->price = $row[1] ?? 0;
                        $sales->order_at = Date::shamsiToTimestamp($validatedData['date']) ?? time();
                        $sales->created_at = time();
                        $sales->save();
                        $i++;
                    }
                });
            });
            dd("done #$i");

        } catch (Exception $e) {
            dd('alert', $e->getMessage());
        }
    }
}
