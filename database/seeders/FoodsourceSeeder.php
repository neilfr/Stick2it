<?php

namespace Database\Seeders;

use App\Models\Foodsource;
use Illuminate\Database\Seeder;

class FoodsourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Foodsource::factory()->create([
            'name' => 'Health Canada',
            'sharable' => true,
        ]);
        Foodsource::factory()->create([
            'name' => 'User',
            'sharable' => false,
        ]);
    }
}
