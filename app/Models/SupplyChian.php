<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyChian extends Model
{
    use HasFactory;

    protected $table = 'supplychain';
    
    protected $fillable = [
        'dispatch_receive',
        'client_name',
        'client_city',
        'model_no',
        'date_time',
        'qr_code',
        'reference',
        'add_by'
    ];

}
