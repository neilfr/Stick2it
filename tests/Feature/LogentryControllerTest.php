<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogentryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_a_list_of_users_logentries()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $foods = Food::factory(2)->create([
            'user_id' => $user->id,
        ]);
        // dd($foods);

        foreach($foods as $index => $food){
            $logentries[$index] = Logentry::factory()->create([
                'user_id' => $user->id,
                'food_id' => $food->id,
            ]);
        }

        foreach($logentries as $logentry){
            $user->logentries()->save($logentry);
        }

        $response = $this->get(route('logentries.index', $user));
        $response->assertOk();
        $response->assertPropValue('logentries', function ($returnedLogentries) use($logentries) {
            $this->assertEquals(2, count($returnedLogentries['data']));
            foreach($returnedLogentries['data'] as $index => $returnedLogentry){
                dd('logentries[index]', $logentries[$index], 'returnedlogentry', $returnedLogentry);
                $this->assertEquals($logentries[$index]->food_id, $returnedLogentry['food_id']);
                $this->assertEquals($logentries[$index]->user_id, $returnedLogentry['user_id']);
                // $this->assertEquals($logentries[$index]->description, $returnedLogentry['description']);
                // $this->assertEquals($logentries[$index]->alias, $returnedLogentry['alias']);
            }
        });
    }

    /** @test */
    public function it_returns_a_list_of_users_logentries_with_food_description_and_alias()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
            'description' => 'my food',
            'alias' => 'my alias',
        ]);

        $logentry = Logentry::factory()->create([
            'user_id' => $user->id,
            'food_id' => $food->id,
            'quantity' => 100,
        ]);

        $response = $this->get(route('logentries.index', $user));

        $response->assertOk();
        $response->assertPropValue('logentries', function ($logentries) use($logentry) {
            $this->assertCount(1, $logentries);
            $this->assertEquals($logentries[0]['user_id'], $logentry->user_id);
        });
    }
}
