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
/** @test */
    public function it_can_return_a_list_of_users_logentries()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
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
