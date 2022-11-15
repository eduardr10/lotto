<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Lottery;
use Carbon\Carbon;
use Rubix\ML\AnomalyDetectors\GaussianMLE;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use Rubix\ML\AnomalyDetectors\Loda;
use Rubix\ML\AnomalyDetectors\OneClassSVM;
use Rubix\ML\AnomalyDetectors\RobustZScore;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Illuminate\Http\Request;
use Rubix\ML\CrossValidation\Metrics\FBeta;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Circle;

class AnimalController extends Controller
{
    public function animals_by_hours()
    {
        // request()->filled('start') ? request()->start : now()->subDays(7)->format('Y-m-d'),
        //     request()->filled('end') ? request()->end : now()->format('Y-m-d'),

        $dates = [
            Carbon::createFromFormat('Y-m-d', '2018-01-01')->setTime(0, 0, 0)->format('Y-m-d'),
            Carbon::createFromFormat('Y-m-d', '2022-12-31')->setTime(24, 59, 59)->format('Y-m-d'),
        ];

        $results = Animal::query()
            ->select(['id', 'name', 'number'])
            ->withCount(['lotteries as appearances' => function ($query) use ($dates) {
                $query->inRange($dates);
            }])
            ->orderByDesc('appearances')
            ->get();
        return $results->makeHidden(['number']);
    }

    public function simulator()
    {
        $quantity_of_animals = 1;
        $factor = 1;
        $simulation_range = [
            Carbon::createFromFormat('Y-m-d', '2018-01-01')->setTime(0, 0, 0)->format('Y-m-d'),
            Carbon::createFromFormat('Y-m-d', '2022-12-31')->setTime(24, 59, 59)->format('Y-m-d'),
        ];
        for ($i = 0; $i < 6; $i++) {
            $top = collect($this->animals_by_hours())->take($quantity_of_animals)->pluck('id');

            $totalizer = Lottery::query()
                ->inRange($simulation_range)
                ->count();

            $results = Animal::query()
                ->select(['id', 'name'])
                ->whereIn('id', $top)
                ->withCount(['lotteries as appearances' => function ($query) use ($simulation_range) {
                    $query->inRange($simulation_range);
                }])
                ->orderByDesc('appearances')
                ->get();

            $val[] = [
                'sorteos' => $totalizer,
                'quantity_of_animals' => $quantity_of_animals,
                // 'results' => $results,
                'estimado' => [
                    'pasivo' => $totalizer * $quantity_of_animals * $factor,
                    'activo' => $results->sum('appearances') * 30 * $factor,
                    'ganancia' => ($results->sum('appearances') * 30 * $factor) - ($totalizer * $quantity_of_animals * $factor)
                ],
            ];
            $quantity_of_animals++;
        }
        return collect($val)->sortByDesc('estimado.ganancia')->values();
    }

    public function by_hour()
    {
        // $generator = new Agglomerate([
        //     0 => new Blob([0.0, 0.0], 2.0),
        //     1 => new Circle(0.0, 0.0, 8.0, 1.0),
        // ], [0.9, 0.1]);

        // $estimator = new GaussianMLE(0.1, 1e-8);

        // $metric = new FBeta();

        // $data = RubixService::trainWithoutTest(
        //     $all,
        //     estimator_algorithm: new GaussianMLE(contamination: 0.005)
        // );

        // RubixAi::toCsv($data, 'anomalies.csv');

        $dates = [
            Carbon::createFromFormat('Y-m-d', '2022-01-01')->setTime(0, 0, 0)->format('Y-m-d'),
            Carbon::createFromFormat('Y-m-d', '2022-12-31')->setTime(24, 59, 59)->format('Y-m-d'),
        ];

        $results = Animal::query()
            ->select(['id', 'name', 'number'])
            ->withCount(['lotteries as appearances' => function ($query) use ($dates) {
                $query->inRange($dates);
            }])
            ->orderByDesc('appearances')
            ->get();
        return $results;
    }
    // public function by_hour()
    // {
    //     $dates = [
    //         Carbon::createFromFormat('Y-m-d', '2022-01-01')->setTime(0, 0, 0)->format('Y-m-d'),
    //         Carbon::createFromFormat('Y-m-d', '2022-12-31')->setTime(24, 59, 59)->format('Y-m-d'),
    //     ];

    //     $results = Animal::query()
    //         ->select(['id', 'name', 'number'])
    //         ->withCount(['lotteries as appearances' => function ($query) use ($dates) {
    //             $query->inRange($dates);
    //         }])
    //         ->orderByDesc('appearances')
    //         ->get();
    //     return $results;
    // }
}
