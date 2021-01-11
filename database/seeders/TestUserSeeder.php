<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
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
            'id' => 2,
            'name' => 'Test User',
            'password' => Hash::make('tester'),
            'email' => 'tester@example.com',
        ]);

        DB::insert("
            INSERT INTO `foods` (`alias`, `description`, `potassium`, `kcal`, `protein`, `carbohydrate`, `fat`, `foodgroup_id`, `foodsource_id`, `user_id`, `base_quantity`)
            VALUES
                ('tf1','Test food one',119,204,'9.54','5.91','15.7',22,1,2,100)");

        $food = Food::where('alias','tf1')->first();

        $food->ingredients()->attach(2,['quantity' => 200]);
        $food->ingredients()->attach(4, ['quantity' => 250]);

        $testUser->favourites()->sync([2,4,$food->id]);

        $logentry = Logentry::factory()->create([
            'user_id' => $testUser->id,
            'food_id' => $food->id,
            'quantity' => 100,
            'consumed_at' => Carbon::now(),
        ]);
    }
}
