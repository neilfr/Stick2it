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
                ('tf1','Test food one',111,111,'9.54','5.91','15.7',22,1,2,100),
                ('tf2','Test food two',222,222,'1.54','4.91','3.7',22,1,2,200)
            ");

        $food1 = Food::where('alias','tf1')->first();
        $food2 = Food::where('alias','tf2')->first();

        $food1->ingredients()->attach(2,['quantity' => 200]);
        $food2->ingredients()->attach(4, ['quantity' => 250]);

        $testUser->favourites()->sync([2,4,$food1->id]);
        $testUser->favourites()->sync([2,4,$food2->id]);

        Logentry::factory()->create([
            'user_id' => $testUser->id,
            'description' => 'log entry from food one',
            'quantity' => 200,
            'kcal' => 201,
            'fat' => 202,
            'protein' => 203,
            'carbohydrate' => 204,
            'potassium' => 205,
            'consumed_at' => Carbon::now()->subDays(1)->subHours(1),
        ]);
        Logentry::factory()->create([
            'user_id' => $testUser->id,
            'description' => 'log entry from food two',
            'quantity' => 300,
            'kcal' => 301,
            'fat' => 302,
            'protein' => 303,
            'carbohydrate' => 304,
            'potassium' => 305,
            'consumed_at' => Carbon::now()->subDays(2)->subHours(2),
        ]);
        $logentries = Logentry::factory()->times(20)->create([
            'user_id' => $testUser->id,
        ]);
    }
}
