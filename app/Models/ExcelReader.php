<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelReader extends Model
{
    protected $table = 'ex18_sales';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'employee_role',
        'group_id',
        'city_id',
        'price',
        'order_at',
        'created_at',
    ];
}
