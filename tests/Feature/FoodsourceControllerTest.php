<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use App\Models\Foodsource;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FoodsourceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_foods()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $foodsource = Foodsource::factory()->create();

        $foods = Food::factory(5)->create([
            'foodsource_id' => $foodsource->id,
        ]);

        $this->assertCount(5, $foodsource->foods()->get());
    }
}
