<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Food;
use App\Models\User;
use App\Models\Ingredient;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FoodIngredientControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ingredients_have_many_parent_foods()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $parentfoods = Food::factory()->times(2)->create();

        $ingredient = Food::factory()->create([
            'description' => 'ingredient',
        ]);

        foreach ($parentfoods as $food) {
            $ingredient->parentfoods()->attach($food->id, ['quantity' => 555]);
        }

        foreach ($parentfoods as $food) {
            $this->assertEquals($food->description, $ingredient->parentfoods()->find($food->id)->description);
        }
    }

    /** @test */
    public function ingredients_have_quantities()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $ingredient = Ingredient::factory()->create([
            'description' => 'ingredient',
        ]);

        $parentfood = Food::factory()->create([
            'description' => 'parentfood',
        ]);

        $parentfood->ingredients()->attach($ingredient->id, ['quantity' => 555]);

        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $parentfood->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 555,
        ]);
    }

    /** @test */
    public function it_can_return_a_list_of_ingredients_with_quantities_for_a_user_owned_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $ingredients = Ingredient::factory()->times(2)->create([
            'base_quantity' => 333,
        ]);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        foreach ($ingredients as $ingredient) {
            $food->ingredients()->attach($ingredient->id, ['quantity' => 555]);
        }

        $response = $this->get(route('foods.ingredients.index', $food));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertPropValue('ingredients', function ($returnedIngredients) use ($ingredients) {
            $this->assertCount(2, $returnedIngredients['data']);
            foreach($returnedIngredients['data'] as $index => $returnedIngredient){
                $this->assertEquals($returnedIngredient['description'], $ingredients->toArray()[$index]['description']);
                $this->assertEquals($returnedIngredient['base_quantity'], $ingredients->toArray()[$index]['base_quantity']);
                $this->assertEquals($returnedIngredient['quantity'], 555);
            }
        });
    }

    /** @test */
    public function it_cannot_return_a_list_of_ingredients_for_another_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        $ingredients = Food::factory()->times(2)->create();

        $food = Food::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        foreach ($ingredients as $ingredient) {
            $food->ingredients()->attach($ingredient->id, ['quantity' => 100]);
        }

        $response = $this->get(route('foods.ingredients.index', $food));

        $response->assertRedirect(route('foods.index'));
    }

    /** @test */
    public function it_can_add_an_ingredient_to_a_user_owned_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create();

        $ingredient = Ingredient::factory()->create();

        $payload = [
            'ingredient_id' => $ingredient->id,
            'quantity' => 200,
        ];

        $response = $this->post(route('foods.ingredients.store', $food), $payload);

        $response->assertRedirect(route('foods.show', $food));
        $this->assertDatabaseHas('ingredients', $payload);
    }

    /** @test */
    public function it_cannot_add_the_same_ingredient_to_a_user_owned_food_more_than_once()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create();

        $ingredient = Ingredient::factory()->create();

        $food->ingredients()->attach($ingredient->id, ['quantity' => 100]);

        $payload = [
            'ingredient_id' => $ingredient->id,
            'quantity' => 200,
        ];

        $response = $this->post(route('foods.ingredients.store', $food), $payload);

        $response->assertRedirect(route('foods.show', $food));
        $this->assertDatabaseMissing('ingredients', $payload);
    }

    /** @test */
    public function it_will_have_an_ingredient_with_quantity_of_ingredients_base_quantity_if_not_provided()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create();

        $ingredient = Ingredient::factory()->create([
            'base_quantity' => 999,
        ]);

        $payload = [
            'ingredient_id' => $ingredient->id,
        ];
        $expectedResult = array_merge($payload, ['quantity' => 999]);

        $response = $this->post(route('foods.ingredients.store', $food), $payload);

        $response->assertRedirect(route('foods.show', $food));
        $this->assertDatabaseHas('ingredients', $expectedResult);
    }

    /**
     * @test
     * @dataProvider ingredientStoreValidationProvider
     *  */
    public function it_cannot_store_ingredient_if_ingredient_data_is_invalid($getData)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        [$ruleName, $payload] = $getData();

        $response = $this->post(route('foods.ingredients.store', $payload['parent_food_id']), $payload);

        $response->assertSessionHasErrors($ruleName);
    }

    public function ingredientStoreValidationProvider()
    {
        return [
            'it fails if quantity is not an integer' => [
                function () {
                    return [
                        'quantity',
                        array_merge($this->getValidIngredientData(), ['quantity' => 'not an integer']),
                    ];
                }
            ],
            'it fails if ingredient_id is not an integer' => [
                function () {
                    return [
                        'ingredient_id',
                        array_merge($this->getValidIngredientData(), ['ingredient_id' => 'not an integer']),
                    ];
                }
            ],
            'it fails if ingredient_id is not a valid food id' => [
                function () {
                    return [
                        'ingredient_id',
                        array_merge($this->getValidIngredientData(), ['ingredient_id' => 99999]),
                    ];
                }
            ]
        ];
    }

    public function getValidIngredientData()
    {
        return [
            'parent_food_id' => Food::factory()->create()->id,
            'ingredient_id' => Ingredient::factory()->create()->id,
            'quantity' => 100,
        ];
    }

    /** @test */
    public function it_can_update_ingredient_quantity()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        $ingredient = Ingredient::factory()->create();

        $food->ingredients()->attach($ingredient, ['quantity' => 100]);

        $this->patch(route('foods.ingredients.update', [
                'food' => $food->id,
                'ingredient' => $ingredient->id,
            ]),
                ['quantity' => 200]);

        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $food->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 200,
        ]);
    }

    /** @test */
    public function it_cannot_update_ingredient_attributes_other_than_quantity()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        $ingredient = Ingredient::factory()->create();

        $food->ingredients()->attach($ingredient, ['quantity' => 100]);

        $response = $this->patch(route('foods.ingredients.update', [
                'food' => $food->id,
                'ingredient' => $ingredient->id,
            ]),
                ['alias' => 'new alias']);

        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $food->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 100,
        ]);
    }


    /** @test */
    public function it_cannot_update_ingredient_quantity_for_another_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $ingredient = Ingredient::factory()->create();

        $food->ingredients()->attach($ingredient, ['quantity' => 100]);

        $this->patch(route('foods.ingredients.update', [
                'food' => $food->id,
                'ingredient' => $ingredient->id,
            ]),
                ['quantity' => 200]);

        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $food->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 100,
        ]);
    }
}
