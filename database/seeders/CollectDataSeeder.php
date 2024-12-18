<?php

namespace Database\Seeders;

use App\Models\CollectData;
use Illuminate\Database\Seeder;

class CollectDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CollectData::factory(50)->create();
    }
}
