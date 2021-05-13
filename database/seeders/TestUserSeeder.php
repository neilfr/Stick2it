<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
use App\Models\Foodsource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
    {
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'password' => Hash::make('temporary'),
            'email' => 'tester@example.com',
        ]);

        $userFoodsource = Foodsource::where('name', 'User')->first();

        DB::insert("
            INSERT INTO `foods` (`alias`, `description`, `potassium`, `kcal`, `protein`, `carbohydrate`, `fat`, `foodgroup_id`, `foodsource_id`, `user_id`, `base_quantity`)
            VALUES
                ('tf1','Test food one',111,111,'9.54','5.91','15.7',22,{$userFoodsource->id},{$testUser->id},100),
                ('tf2','Test food two',222,222,'1.54','4.91','3.7',22,{$userFoodsource->id},{$testUser->id},200)
            ");

        $food1 = Food::where('alias','tf1')->first();
        $food2 = Food::where('alias','tf2')->first();

        $ingredient = Food::first();

        $food1->ingredients()->attach($ingredient->id,['quantity' => 200]);

        Logentry::factory()->create([
            'user_id' => $testUser->id,
            'food_id' => $food1->id,
            'quantity' => 200,
            'consumed_at' => Carbon::now()->subDays(1)->subHours(1),
        ]);
        Logentry::factory()->create([
            'user_id' => $testUser->id,
            'food_id' => $food2->id,
            'quantity' => 300,
            'consumed_at' => Carbon::now()->subDays(2)->subHours(2),
        ]);
    }
}
