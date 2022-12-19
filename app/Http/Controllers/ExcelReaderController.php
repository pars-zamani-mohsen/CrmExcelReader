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
            'file' => ['required', 'file', 'mimes:xlsx', 'max:4096'],
            'date' => ['required', 'string'],
            'user_id_index' => ['required', 'int'],
            'price_index' => ['required', 'int'],
            'employee_role_index' => ['required', 'int'],
            'group_id_index' => ['required', 'int'],
            'city_id_index' => ['required', 'int'],
        );
        $validatedData = $this->validate($request, $validate_data);

        try {
            $i = 0;
            $collection = Excel::toCollection(new SalesImport, $request->file, null,  \Maatwebsite\Excel\Excel::XLSX);
            $collection->each(function($sheet) use(&$i, $validatedData) {
                $sheet->each(function($row) use(&$i, $validatedData) {
                    if (count($row) and $row[$validatedData['user_id_index']]) {
                        $sales = new ExcelReader();
                        $sales->user_id = $row[$validatedData['user_id_index']];
                        $sales->employee_role = $row[$validatedData['employee_role_index']];
                        $sales->group_id = $row[$validatedData['group_id_index']];
                        $sales->city_id = $row[$validatedData['city_id_index']];
                        $sales->price = $row[$validatedData['price_index']] ?? 0;
                        $sales->order_at = Date::shamsiToTimestamp($validatedData['date']) ?? time();
                        $sales->created_at = time();
                        $sales->save();
                        $i++;
                    }
                });
            });
            echo '<a href="/" class="btn btn-primary">Return to home</a>';
            dd("done #$i");

        } catch (Exception $e) {
            dd('alert', $e->getMessage());
        }
    }
}
