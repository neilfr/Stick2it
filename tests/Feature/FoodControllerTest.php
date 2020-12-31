<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Food;
use App\Models\User;
use App\Models\Foodgroup;
use App\Models\Foodsource;
use App\Models\Ingredient;
use Laravel\Sanctum\Sanctum;
use App\Http\Resources\IngredientResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FoodControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_access_foods_as_authorized_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $this->get(route('foods.index'))->assertOk();
    }

    /** @test */
    public function it_cannot_access_food_as_unauthorized_user()
    {
        $this->get(route('foods.index'))->assertRedirect('/login');
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $food = Food::factory()->create([
            'description' => 'my food',
        ]);

        $user->foods()->save($food);

        $this->assertEquals($food->description, $user->foods()->first()->pluck('description')[0]);
    }

    /** @test */
    public function it_belongs_to_a_foodgroup()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $foodgroup = Foodgroup::factory()->create();
        $food = Food::factory()->create([
            "foodgroup_id" => $foodgroup->id,
        ]);

        $this->assertEquals($foodgroup->description, $food->foodgroup->description);
    }


    /** @test */
    public function it_belongs_to_a_foodsource()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $foodsource = Foodsource::factory()->create();
        $food = Food::factory()->create([
            "foodsource_id" => $foodsource->id,
        ]);

        $this->assertEquals($foodsource->name, $food->foodsource->name);
    }

    /** @test */
    public function it_has_many_ingredients()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $ingredients = Ingredient::factory(2)->create();

        $parentfood = Food::factory()->create([
            'description' => 'parentfood',
        ]);

        foreach ($ingredients as $ingredient) {
            $parentfood->ingredients()->attach($ingredient->id, ['quantity' => 555]);
        }
        $ingredientsCollection=IngredientResource::collection($parentfood->ingredients);

        foreach ($ingredientsCollection as $ingredient) {
            $this->assertEquals($ingredient->description, $parentfood->ingredients()->find($ingredient->id)->description);
            $this->assertEquals($ingredient->pivot->quantity, 555);
        }
    }

    /** @test */
    public function it_can_return_user_owned_foods()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $foods = Food::factory(2)->create();

        foreach($foods as $food) {
            $user->foods()->save($food);
        }

        $response = $this->get(route('foods.index'))
            ->assertOk();

        $response->assertPropValue('foods', function ($returnedFoods) use ($foods) {
            foreach ($returnedFoods['data'] as $index => $food) {
                $this->assertEquals($food['description'], $foods->toArray()[$index]['description']);
            }
        });
    }

    /** @test */
    public function it_can_return_other_users_shared_foods()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $anotherUser = User::factory()->create();
        $foodsource = Foodsource::factory()->create([
            'sharable' => true,
        ]);

        $foods = Food::factory(2)->create([
            'user_id' => $anotherUser->id,
            'foodsource_id' => $foodsource->id,
        ]);

        $response = $this->get(route('foods.index'))
            ->assertOk();

        $response->assertPropValue('foods', function ($returnedFoods) use ($foods) {
            foreach ($returnedFoods['data'] as $index => $food) {
                $this->assertEquals($food['description'], $foods->toArray()[$index]['description']);
            }
        });
    }

    /** @test */
    public function it_cannot_return_other_users_foods_that_are_not_sharable()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );
        $otherUser = User::factory()->create();

        $foodsource = Foodsource::factory()->create([
            'sharable' => false,
        ]);

        $foods = Food::factory(2)->create([
            'user_id' => $otherUser->id,
            'foodsource_id' => $foodsource->id,
        ]);

        $response = $this->get(route('foods.index'));

        $response->assertPropValue('foods', function ($returnedFoods) use ($foods) {
            $this->assertCount(0, $returnedFoods['data']);
        });
    }

    /** @test */
    public function it_can_return_a_specific_user_owned_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $foods = Food::factory(2)->create();

        $response = $this->get(route('foods.show', $foods[0]));

        $response->assertOk()
            ->assertPropValue('food', function ($returnedFood) use ($foods) {
                $this->assertEquals($foods[0]['description'],$returnedFood['data']['description']);
            });
    }

    /** @test */
    public function it_can_return_a_specific_user_owned_food_with_ingredients_and_quantities()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $foods = Food::factory(2)->create([
            'user_id' => $user->id,
        ]);

        $ingredients = Ingredient::factory(2)->create([
            'user_id' => $user->id,
        ]);

        foreach ($ingredients as $ingredient) {
            $foods[0]->ingredients()->attach($ingredient->id, ['quantity' => 555]);
        }

        $response = $this->get(route('foods.show', $foods[0]));

        $response->assertOk()
            ->assertPropValue('food', function ($returnedFood) use ($foods) {
                $this->assertEquals($foods[0]['description'], $returnedFood['data']['description']);
                $items = $foods[0]->ingredients()->get();
                foreach($items as $item) {
                    $this->assertEquals($item->toArray()['pivot']['quantity'], 555);
                }
            });
    }

    /** @test */
    public function it_can_return_a_specific_food_owned_by_another_user_if_the_food_is_sharable()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );

        $otheruser = User::factory()->create();

        $foodsource = Foodsource::factory()->create([
            'sharable' => true,
        ]);

        $food = Food::factory()->create([
            'user_id' => $otheruser->id,
            'foodsource_id' => $foodsource->id,
        ]);

        $response = $this->get(route('foods.show', $food));

        $response->assertOk()
            ->assertPropValue('food', function ($returnedFood) use ($food) {
                $this->assertEquals($returnedFood['data']['description'], $food['description']);
            });
    }


    /** @test */
    public function it_cannot_return_a_specific_food_owned_by_another_user_that_is_not_sharable()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
        );
        $otheruser = User::factory()->create();

        $foodsource = Foodsource::factory()->create([
            'sharable' => false,
        ]);

        $food = Food::factory()->create([
            'user_id' => $otheruser->id,
            'foodsource_id' => $foodsource->id,
        ]);

        $response = $this->get(route('foods.show', $food));

        $response->assertRedirect(route('foods.index'));
    }


}
