<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Host;
use App\Models\Lottery;
use Carbon\Carbon;
use DateTime;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class GoutteController extends Controller
{
    public function index()
    {
        $date = Carbon::createFromFormat('Y-m-d', '2018-01-01');
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));

        while ($date->lessThanOrEqualTo(now())) {
            $uri = 'https://loteriadehoy.com/animalito/lottoactivo/historico/' . $date->format('Y-m-d') . '/2022-11-13/';
            $crawler = $client->request('GET', $uri);
            $weekly = $crawler->filter('.semanal');
            $dates = $weekly->filter('thead > tr > th')->each(function ($item) {
                if (DateTime::createFromFormat('Y-m-d', $item->filter('th')->text())) {
                    return $item->filter('th')->text();
                }
            });

            $dates = collect($dates)->filter(function ($item) {
                return !is_null($item) ?: $item;
            })
                ->values()
                ->toArray();

            $results = $weekly->filter('tbody > tr')->each(function ($item) use ($dates) {
                $week = count($dates);
                $hour = $item->filter('tr th')->text();
                for ($i = 0; $i < $week; $i++) {
                    $response[$hour][$dates[$i]] = $item->filter('tr td')->eq($i)->each(function ($animal) {
                        return $animal->text();
                    });
                }
                return $response;
            });
            $host_id = Host::where('name', 'Lotto Activo')->first()->id;
            $response = [];

            $response = collect($results)->map(function ($hour) use ($host_id) {
                $dates = collect($hour)->first();
                $hour = collect($hour)->keys()->first();
                foreach ($dates as $key => $value) {
                    if (isset($value[0]) != '') {
                        if ($value[0] != '') {
                            $response[$key . " " . $hour] = [
                                'animal_name' => $value[0],
                                'host_id' => $host_id,
                                'hour' => $hour,
                                'created_at' => date('Y-m-d H:i:s', strtotime($key . " " . $hour)),
                            ];
                        }
                    }
                }
                return $response;
            });
            $loop[] = $this->save($response);
            $date = $date->addDays(7);
        }
        return $loop;
    }

    public function save($results)
    {
        $animals = Animal::all(['id', 'name']);
        $response = collect($results)->map(function ($hour) use ($animals) {
            $response = collect($hour)->map(function ($date) use ($animals) {
                $date['animal_id'] = $animals->where('name', $date['animal_name'])->first()->id;
                unset($date['animal_name']);
                return $date;
            });
            return $response;
        });

        return Lottery::insert(collect($response)->collapse()->values()->toArray());
    }
}
