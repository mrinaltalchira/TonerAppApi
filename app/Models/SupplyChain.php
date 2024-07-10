<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyChain extends Model
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
        'add_by',
        'client_id'
    ];
    
    public static function dashBoardDetails()
    {
        $todayDate = now()->format('Y-m-d');

        $totalDistribute = self::where('dispatch_receive', 0)->count();
        $totalReturn = self::where('dispatch_receive', 1)->count();

        $todayDistribute = self::where('dispatch_receive', 0)
            ->whereDate('date_time', $todayDate)
            ->count();

        $todayReturn = self::where('dispatch_receive', 1)
            ->whereDate('date_time', $todayDate)
            ->count();

        return [
            'total_distribute' => $totalDistribute,
            'total_return' => $totalReturn,
            'today_distribute' => $todayDistribute,
            'today_return' => $todayReturn,
        ];
    }
}
