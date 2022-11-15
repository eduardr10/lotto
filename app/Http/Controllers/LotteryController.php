<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LotteryController extends Controller
{
    public function by_hours()
    {
        $dates = [
            Carbon::createFromFormat('Y-m-d', '2019-01-01')->format('Y-m-d'),
            Carbon::createFromFormat('Y-m-d', '2019-01-09')->format('Y-m-d'),
        ];

        return Lottery::query()
            ->with('animal')
            ->mostAppearances($dates)
            ->select('hour', DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->get()
            // ->groupBy('animal')
            // 
        ;

        return Lottery::query()
            ->with('animal:id,name,number')
            ->get()
            ->map(function ($item) {
                dd($item->pluck('animal')->sortBy('number')->groupBy('id')->map(function ($animal) {
                    dd($animal);
                    return [
                        'id' => $animal->id,
                        'count' => $animal->count(),
                    ];
                }));
                return [
                    'animals' => $item->groupBy('animal'),
                ];
            })
            // 
        ;
    }
}
