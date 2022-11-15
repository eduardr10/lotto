<?php

namespace Database\Seeders;

use App\Models\Host;
use Illuminate\Database\Seeder;

class HostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Host::create([
            'name' => 'Lotto Activo',
            'created_at' => now(),
        ]);
    }
}
