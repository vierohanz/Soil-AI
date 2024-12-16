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
        // Gunakan factory untuk membuat 50 data
        CollectData::factory(50)->create();
    }
}
