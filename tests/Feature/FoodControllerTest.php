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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);
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
        Sanctum::actingAs($user);

        $foods = Food::factory(2)->create([
            'user_id' => $user->id,
        ]);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);

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
        Sanctum::actingAs($user);
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


    /** @test */
    public function it_can_access_food_create_as_authorized_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->get(route('foods.create'))->assertOk();
    }

    /** @test */
    public function it_cannot_access_food_create_as_unauthorized_user()
    {
        $this->get(route('foods.create'))->assertRedirect('/login');
    }

    /** @test */
    public function it_can_store_a_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = $this->getValidFoodData();
        $response = $this->post(route('foods.store'), $payload);

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseHas('foods', $payload);
    }

    /** @test */
    public function it_can_store_food_if_food_description_is_duplicate_of_another_users_food_description()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        Food::factory()->create([
            'description' => 'other users existing description',
            'alias' => 'existing alias',
            'user_id' => $anotherUser->id,
        ]);

        $payload = [
            'alias' => 'alias',
            'description' => 'other users existing description',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create()->id,
            'foodsource_id' => Foodsource::factory()->create()->id,
            'user_id' => auth()->user()->id,
        ];

        $response = $this->post(route('foods.store'), $payload);

        $response->assertSessionDoesntHaveErrors('description');
    }

    /** @test */
    public function it_can_store_food_if_food_alias_is_duplicate_of_another_users_food_alias()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        Food::factory()->create([
            'description' => 'other users existing description',
            'alias' => 'existing alias',
            'user_id' => $anotherUser->id,
        ]);

        $payload = [
            'alias' => 'existing alias',
            'description' => 'description',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create()->id,
            'foodsource_id' => Foodsource::factory()->create()->id,
            'user_id' => auth()->user()->id,
        ];

        $response = $this->post(route('foods.store'), $payload);

        $response->assertSessionDoesntHaveErrors('description');
    }

    /** @test */
    public function it_can_update_food_if_food_description_is_duplicate_of_another_users_food_description()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        $usersFood = Food::factory()->create([
            'description' => 'original description',
            'alias' => 'original alias',
        ]);

        $anotherUsersFood = Food::factory()->create([
            'description' => 'other users existing description',
            'alias' => 'other users existing alias',
            'user_id' => $anotherUser->id,
        ]);

        $payload = [
            'alias' => 'alias',
            'description' => 'other users existing description',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create()->id,
            'foodsource_id' => Foodsource::factory()->create()->id,
            'user_id' => auth()->user()->id,
        ];

        $response = $this->patch(route('foods.update', $usersFood->id), $payload);

        $response->assertSessionDoesntHaveErrors('description');
    }

    /** @test */
    public function it_can_update_food_if_food_alias_is_duplicate_of_another_users_food_alias()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        $usersFood = Food::factory()->create([
            'description' => 'original description',
            'alias' => 'original alias',
        ]);

        $anotherUsersFood = Food::factory()->create([
            'description' => 'other users existing description',
            'alias' => 'other users existing alias',
            'user_id' => $anotherUser->id,
        ]);

        $payload = [
            'alias' => 'other users existing alias',
            'description' => 'description',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create()->id,
            'foodsource_id' => Foodsource::factory()->create()->id,
            'user_id' => auth()->user()->id,
        ];

        $response = $this->patch(route('foods.update', $usersFood->id), $payload);

        $response->assertSessionDoesntHaveErrors('alias');
    }

    /** @test */
    public function it_can_store_a_food_if_food_alias_is_null_and_not_unique()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Food::factory()->create([
            'description' => 'existing description',
            'alias' => null,
        ]);

        $payload = [
            'alias' => null,
            'description' => 'my food',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create([
                'description' => 'Meals'
            ])->id,
            'foodsource_id' => Foodsource::factory()->create([
                'name' => 'User',
                'sharable' => false,
            ])->id,
            'user_id' => auth()->user()->id,
        ];

        $response = $this->post(route('foods.store'), $payload)
            ->assertRedirect();

        $response->assertSessionDoesntHaveErrors('alias');
    }

    /** @test */
    public function it_can_update_a_food_if_food_alias_is_null_and_not_unique()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
            'description' => 'existing description',
            'alias' => 'some alias',
        ]);

        $anotherFood = Food::factory()->create([
            'user_id' => $user->id,
            'description' => 'some description',
            'alias' => null,
        ]);

        $payload = [
            'alias' => null,
        ];

        $response = $this->patch(route('foods.update', $food->id), $payload)
            ->assertRedirect();

        $response->assertSessionDoesntHaveErrors('alias');
    }

        /** @test */
    public function it_can_update_a_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $initialFoodsource = Foodsource::factory()->create();
        $updatedFoodsource = Foodsource::factory()->create();
        $initialFoodgroup = Foodgroup::factory()->create();
        $updatedFoodgroup = Foodgroup::factory()->create();

        $food = Food::factory()->create($this->getValidFoodData());

        $payload = [
            'description' => 'new description',
            'alias' => 'new alias',
            'kcal' => 222,
            'fat' => 222,
            'protein' => 222,
            'carbohydrate' => 222,
            'potassium' => 222,
            'base_quantity' => 222,
            'foodgroup_id' => $updatedFoodgroup->id,
            'foodsource_id' => $updatedFoodsource->id,
        ];

        $response = $this->patch(route('foods.update', $food->id), $payload);

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseHas('foods', $payload);
    }

/** @test */
    public function it_can_update_a_foods_description_while_alias_is_unchanged()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
            'alias' => 'original alias',
            'description' => 'original description',
        ]);

        $payload = [
            'alias' => 'original alias',
            'description' => 'new description',
        ];

        $response = $this->patch(route('foods.update', $food->id), $payload);

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseHas('foods', $payload);
    }


    /** @test */
    public function it_can_update_a_foods_alias_while_description_is_unchanged()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
            'alias' => 'original alias',
            'description' => 'original description',
        ]);

        $payload = [
            'alias' => 'new alias',
            'description' => 'original description',
        ];

        $response = $this->patch(route('foods.update', $food->id), $payload);

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseHas('foods', $payload);
    }

    /** @test */
    public function it_cannot_update_another_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        $food = Food::factory()->create([
            'user_id' => $anotherUser->id,
            'description' => 'initial description',
        ]);

        $this->assertDatabaseHas('foods', [
            'user_id' => $anotherUser->id,
            'description' => 'initial description',
        ]);

        $payload = [
            'description' => 'new description'
        ];

        $response = $this->patch(route('foods.update', $food->id), $payload);
        $this->assertDatabaseMissing('foods', [
            'user_id' => $anotherUser->id,
            'description' => 'new description',
        ]);

        $this->assertDatabaseHas('foods', [
            'user_id' => $anotherUser->id,
            'description' => 'initial description',
        ]);

        $response->assertRedirect(route('foods.index'));
    }


    /** @test */
    public function it_can_delete_a_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete(route('foods.destroy', $food->id))
            ->assertRedirect(route('foods.index'));

        $this->assertSoftDeleted('foods', $food->toArray());
    }

    /** @test */
    public function it_does_not_delete_a_foods_ingredients_when_the_food_is_soft_deleted()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        $ingredient = Ingredient::factory()->create();

        $food->ingredients()->attach($ingredient, ['quantity' => 100]);
        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $food->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 100,
        ]);

        $response = $this->delete(route('foods.destroy', $food->id))
            ->assertRedirect(route('foods.index'));

        $this->assertSoftDeleted('foods', $food->toArray());

        $this->assertDatabaseHas('ingredients', [
            'parent_food_id' => $food->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 100,
        ]);

    }

    /** @test */
    public function it_cannot_delete_another_users_food()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $anotherUser = User::factory()->create();

        $food = Food::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->delete(route('foods.destroy', $food->id))
            ->assertRedirect(route('foods.index'));

        $response = $this->assertDatabaseHas('foods', ['id' => $food->id]);
    }

    /** @test */
    public function it_can_toggle_food_as_favourite()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
            'description' => 'test food one',
        ]);

        $user->favourites()->attach($food);

        $this->assertDatabaseHas('favourites', [
            'user_id' => $user->id,
            'food_id' => $food->id,
        ]);

        $response = $this->from(route('foods.index'))
            ->post(route('foods.toggle-favourite', $food->id));

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseMissing('favourites', [
            'food_id' => $food->id,
            'user_id' => $user->id,
        ]);

        $response = $this->from(route('foods.index'))
                        ->post(route('foods.toggle-favourite', $food->id));

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseHas('favourites', [
            'food_id' => $food->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * @dataProvider foodStoreValidationProvider
     */
    public function it_cannot_store_food_if_food_data_is_invalid($getData)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Food::factory()->create([
            'description' => 'existing description',
            'alias' => 'existing alias',
            'user_id' => $user->id,
        ]);

        [$ruleName, $payload] = $getData();

        $response = $this->post(route('foods.store'), $payload);

        $response->assertSessionHasErrors($ruleName);
    }

    public function foodStoreValidationProvider()
    {
        return [
            'it fails if alias is not a string' => [
                function () {
                    return [
                        'alias',
                        array_merge($this->getValidFoodData(), ['alias' => []]),
                    ];
                }
            ],
            'it fails if alias is not unique' => [
                function () {
                    return [
                        'alias',
                        array_merge($this->getValidFoodData(), ['alias' => 'existing alias']),
                    ];
                }
            ],
            'it fails if description is not unique' => [
                function () {
                    return [
                        'description',
                        array_merge($this->getValidFoodData(), ['description' => 'existing description']),
                    ];
                }
            ],
            'it fails if description is not a non-empty string' => [
                function () {
                    return [
                        'description',
                        array_merge($this->getValidFoodData(), ['description' => '']),
                    ];
                }
            ],
            'it fails if kcal is not an integer' => [
                function () {
                    return [
                        'kcal',
                        array_merge($this->getValidFoodData(), ['kcal' => 'not an integer']),
                    ];
                }
            ],
            'it fails if kcal is not a non-negative integer' => [
                function () {
                    return [
                        'kcal', ['kcal' => -1],
                    ];
                }
            ],
            'it fails if fat is not an integer' => [
                function () {
                    return [
                        'fat',
                        array_merge($this->getValidFoodData(), ['fat' => 'not an integer']),
                    ];
                }
            ],
            'it fails if fat is not a non-negative integer' => [
                function () {
                    return [
                        'fat', ['fat' => -1],
                    ];
                }
            ],
            'it fails if protein is not an integer' => [
                function () {
                    return [
                        'protein',
                        array_merge($this->getValidFoodData(), ['protein' => 'not an integer']),
                    ];
                }
            ],
            'it fails if protein is not a non-negative integer' => [
                function () {
                    return [
                        'protein', ['protein' => -1],
                    ];
                }
            ],
            'it fails if carbohydrate is not an integer' => [
                function () {
                    return [
                        'carbohydrate',
                        array_merge($this->getValidFoodData(), ['carbohydrate' => 'not an integer']),
                    ];
                }
            ],
            'it fails if carbohydrate is not a non-negative integer' => [
                function () {
                    return [
                        'carbohydrate', ['carbohydrate' => -1],
                    ];
                }
            ],
            'it fails if potassium is not an integer' => [
                function () {
                    return [
                        'potassium',
                        array_merge($this->getValidFoodData(), ['potassium' => 'not an integer']),
                    ];
                }
            ],
            'it fails if potassium is not a non-negative integer' => [
                function () {
                    return [
                        'potassium', ['potassium' => -1],
                    ];
                }
            ],
            'it fails if base_quantity is not an integer' => [
                function () {
                    return [
                        'base_quantity',
                        array_merge($this->getValidFoodData(), ['base_quantity' => 'not an integer']),
                    ];
                }
            ],
            'it fails if base_quantity is not a non-negative integer' => [
                function () {
                    return [
                        'base_quantity', ['base_quantity' => -1],
                    ];
                }
            ],
            'it fails if foodgroup_id is not a valid foodgroup id' => [
                function () {
                    return [
                        'foodgroup_id',
                        array_merge($this->getValidFoodData(), ['foodgroup_id' => 99999999]),
                    ];
                }
            ],
            'it fails if user_id is not a valid user id' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidFoodData(), ['user_id' => 99999999]),
                    ];
                }
            ]
        ];
    }

    /**
     * @test
     * @dataProvider foodUpdateValidationProvider
     */
    public function it_cannot_update_food_if_food_data_is_invalid($getData)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $food = Food::factory()->create([
            'user_id' => $user->id,
        ]);

        $anotherFood = Food::factory()->create([
            'user_id' => $user->id,
            'alias' => 'another foods alias',
            'description' => 'another foods description',
        ]);

        [$ruleName, $payload] = $getData();

        $response = $this->patch(route('foods.update', $food->id), $payload);

        $response->assertSessionHasErrors($ruleName);
    }

    public function foodUpdateValidationProvider()
    {
        return [
            'it fails if alias is not a string' => [
                function () {
                    return [
                        'alias',
                        array_merge($this->getValidFoodData(), ['alias' => []]),
                    ];
                }
            ],
            'it fails if alias is not unique' => [
                function () {
                    return [
                        'alias',
                        array_merge($this->getValidFoodData(), ['alias' => 'another foods alias']),
                    ];
                }
            ],
            'it fails if description is not a non-empty string' => [
                function () {
                    return [
                        'description',
                        array_merge($this->getValidFoodData(), ['description' => '']),
                    ];
                }
            ],
            'it fails if description is not unique' => [
                function () {
                    return [
                        'description',
                        array_merge($this->getValidFoodData(), ['description' => 'another foods description']),
                    ];
                }
            ],
            'it fails if kcal is not an integer' => [
                function () {
                    return [
                        'kcal',
                        array_merge($this->getValidFoodData(), ['kcal' => 'not an integer']),
                    ];
                }
            ],
            'it fails if kcal is not a non-negative integer' => [
                function () {
                    return [
                        'kcal',
                        array_merge($this->getValidFoodData(), ['kcal' => -1]),
                    ];
                }
            ],
            'it fails if fat is not an integer' => [
                function () {
                    return [
                        'fat',
                        array_merge($this->getValidFoodData(), ['fat' => 'not an integer']),
                    ];
                }
            ],
            'it fails if fat is not a non-negative integer' => [
                function () {
                    return [
                        'fat',
                        array_merge($this->getValidFoodData(), ['fat' => -1]),
                    ];
                }
            ],
            'it fails if protein is not an integer' => [
                function () {
                    return [
                        'protein',
                        array_merge($this->getValidFoodData(), ['protein' => 'not an integer']),
                    ];
                }
            ],
            'it fails if protein is not a non-negative integer' => [
                function () {
                    return [
                        'protein',
                        array_merge($this->getValidFoodData(), ['protein' => -1]),
                    ];
                }
            ],
            'it fails if carbohydrate is not an integer' => [
                function () {
                    return [
                        'carbohydrate',
                        array_merge($this->getValidFoodData(), ['carbohydrate' => 'not an integer']),
                    ];
                }
            ],
            'it fails if carbohydrate is not a non-negative integer' => [
                function () {
                    return [
                        'carbohydrate',
                        array_merge($this->getValidFoodData(), ['carbohydrate' => -1]),
                    ];
                }
            ],
            'it fails if potassium is not an integer' => [
                function () {
                    return [
                        'potassium',
                        array_merge($this->getValidFoodData(), ['potassium' => 'not an integer']),
                    ];
                }
            ],
            'it fails if potassium is not a non-negative integer' => [
                function () {
                    return [
                        'potassium',
                        array_merge($this->getValidFoodData(), ['potassium' => -1]),
                    ];
                }
            ],
            'it fails if base_quantity is not an integer' => [
                function () {
                    return [
                        'base_quantity',
                        array_merge($this->getValidFoodData(), ['base_quantity' => 'not an integer']),
                    ];
                }
            ],
            'it fails if base_quantity is not a non-negative integer' => [
                function () {
                    return [
                        'base_quantity',
                        array_merge($this->getValidFoodData(), ['base_quantity' => -1]),
                    ];
                }
            ],
            'it fails if foodgroup_id is not a valid foodgroup id' => [
                function () {
                    return [
                        'foodgroup_id',
                        array_merge($this->getValidFoodData(), ['foodgroup_id' => "not an integer"]),
                    ];
                }
            ],
            'it fails if user_id is not a valid user id' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidFoodData(), ['user_id' => 99999999]),
                    ];
                }
            ]
        ];
    }


    public function getValidFoodData()
    {
        return [
            'alias' => 'alias',
            'description' => 'my food',
            'kcal' => 123,
            'fat' => 789,
            'protein' => 246,
            'carbohydrate' => 135,
            'potassium' => 456,
            'base_quantity' => 200,
            'foodgroup_id' => Foodgroup::factory()->create([
                'description' => 'Meals'
            ])->id,
            'foodsource_id' => Foodsource::factory()->create([
                'name' => 'User',
                'sharable' => false,
            ])->id,
            'user_id' => auth()->user()->id,
        ];
    }

}
