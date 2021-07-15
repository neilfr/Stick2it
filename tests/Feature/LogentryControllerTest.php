<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
use App\Models\Foodgroup;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogentryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_access_logentries_index_as_an_authenticated_user()
    {
        Sanctum::actingAs($this->user);

        $response = $this->get(route('logentries.index'))
            ->assertOk();
    }

    /** @test */
    public function it_cannot_access_logentries_index_if_unauthenticated()
    {
        $response = $this->get(route('logentries.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_can_return_a_logentry()
    {
        Sanctum::actingAs($this->user);

        $food = Food::factory()->create([
            'user_id' => $this->user->id,
            'kcal' => 123,
        ]);

        $logentry = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'food_id' => $food->id,
            'quantity' => 499,
        ]);

        $response = $this->get(route('logentries.index'))
            ->assertOk()
            ->assertPropValue('logentries', function ($returnedLogentries) use($food, $logentry) {
                $this->assertEquals(1, count($returnedLogentries['data']));
                foreach($returnedLogentries['data'] as $index => $returnedLogentry){
                    $this->assertEquals($logentry->food->id,$returnedLogentry['food']['id']);
                }
            });
    }

    /** @test */
    public function it_can_return_a_logentry_with_deleted_food()
    {
        Sanctum::actingAs($this->user);

        $food = Food::factory()->create([
            'id' => 99,
            'user_id' => $this->user->id,
            'kcal' => 123,
        ]);

        $logentry = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'food_id' => $food->id,
            'quantity' => 100,
        ]);

        $food->delete();

        $this->assertSoftDeleted('foods', $food->toArray());

        $response = $this->get(route('logentries.index', $this->user))
            ->assertOk()
            ->assertPropValue('logentries', function ($returnedLogentry) use($logentry) {
                $this->assertEquals(1, count($returnedLogentry['data']));
                $this->assertEquals($logentry->food_id,$returnedLogentry['data'][0]['food']['id']);
            });
    }


    /** @test */
    public function it_can_return_a_user_logentry_in_a_date_range()
    {
        Sanctum::actingAs($this->user);

        $logentries[0] = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => Carbon::parse(Carbon::now()->subDays(5))->format('Y-m-d')
        ]);

        $logentries[1] = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => Carbon::parse(Carbon::now())->format('Y-m-d')
        ]);

        $logentries[2] = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => Carbon::parse(Carbon::now()->addDays(5))->format('Y-m-d')
        ]);

        $response = $this->get(route('logentries.index', [
                'user_id' => $this->user,
                'from' => Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d'),
                'to' => Carbon::parse(Carbon::now()->addDays(2))->format('Y-m-d'),
                ]
            ))
            ->assertOk()
            ->assertPropValue('logentries', function ($returnedLogentries) use($logentries) {
                $this->assertEquals(1, count($returnedLogentries['data']));
                $this->assertEquals($logentries[1]->food->toArray(), $returnedLogentries['data'][0]['food']);
            });
    }

    /** @test */
    public function it_cannot_return_another_users_logentries()
    {
        Sanctum::actingAs($this->user);

        $anotherUser = User::factory()->create();

        $anotherUsersLogentry = Logentry::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->get(route('logentries.index'))
            ->assertOk()
            ->assertPropValue('logentries', function ($returnedLogentries) {
                $this->assertEquals(0, count($returnedLogentries['data']));
            });
    }

    /** @test */
    public function it_can_store_a_new_logentry()
    {
        Sanctum::actingAs($this->user);

        $food = Food::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $payload = [
            "user_id" => $this->user->id,
            'quantity' => 100,
            'food_id' => $food->id,
            'consumed_at' => Carbon::now(),
        ];

        $this->post(route('logentries.store'), $payload)
            ->assertRedirect(route('logentries.index'));

        $this->assertDatabaseHas('logentries', $payload);
    }

    /**
     * @test
     * @dataProvider logentryStoreDataProvider
    */
    public function it_cannot_store_a_new_logentry_with_invalid_data($getData)
    {
        Sanctum::actingAs($this->user);

        [$ruleName, $payload] = $getData();

        $this->post(route('logentries.store'), $payload)
            ->assertSessionHasErrors($ruleName);

        $this->assertDatabaseMissing('logentries', $payload);
    }

    public function logentryStoreDataProvider()
    {
        return [
            'it fails if user_id is not an integer' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidLogentryData(), ['user_id' => 'not an integer']),
                    ];
                }
            ],
            'it fails if user_id is not in users table' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidLogentryData(), ['user_id' => 999]),
                    ];
                }
            ],
            'it fails if food_id is not an integer' => [
                function () {
                    return [
                        'food_id',
                        array_merge($this->getValidLogentryData(), ['food_id' => 'not an integer']),
                    ];
                }
            ],
            'it fails if food_id is not in foods table' => [
                function () {
                    return [
                        'food_id',
                        array_merge($this->getValidLogentryData(), ['food_id' => 999]),
                    ];
                }
            ],

            'it fails if quantity is not an integer' => [
                function () {
                    return [
                        'quantity',
                        array_merge($this->getValidLogentryData(), ['quantity' => 'not an integer']),
                    ];
                }
            ],
            'it fails if quantity is below zero' => [
                function () {
                    return [
                        'quantity',
                        array_merge($this->getValidLogentryData(), ['quantity' => -1]),
                    ];
                }
            ],
            'it fails if consumed_at is not a date' => [
                function () {
                    return [
                        'consumed_at',
                        array_merge($this->getValidLogentryData(), ['consumed_at' => 'not an integer']),
                    ];
                }
            ],
        ];
    }

    public function getValidLogentryData()
    {
        return [
            'user_id' => $user_id = auth()->user()->id,
            'food_id' => $food = Food::factory()->create()->id,
            'quantity' => 100,
            'consumed_at' => Carbon::now(),
        ];
    }

    /** @test */
    public function it_can_update_a_logentry()
    {
        Sanctum::actingAs($this->user);

        $logentry = Logentry::factory()->create($this->getValidLogentryData());

        $payload = [
            'user_id' => $this->user->id,
            'food_id' => $this->getValidLogentryData()['food_id'],
            'quantity' => 999,
            'consumed_at' => Carbon::now(),
        ];

        $this->patch(route('logentries.update', $logentry), $payload)
            ->assertRedirect(route('logentries.index'));

        $this->assertDatabaseHas('logentries', array_merge(['id' => $logentry->id], $payload));
    }

    /** @test */
    public function it_cannot_update_another_users_logentry()
    {
        Sanctum::actingAs($this->user);

        $anotherUser = User::factory()->create();

        $anotherUsersLogentry = Logentry::factory()->create(
            array_merge(
                $this->getValidLogentryData(),
                ['user_id' => $anotherUser->id]
            ));

        $payload = array_merge(
            $anotherUsersLogentry->toArray(),
            ['quantity' => 999],
        );

        $this->patch(route('logentries.update', $anotherUsersLogentry), $payload)
            ->assertRedirect(route('logentries.index'));

        $this->assertDatabaseHas('logentries', $anotherUsersLogentry->toArray());
    }

    /**
     * @test
     * @dataProvider logentryUpdateDataProvider
    */
    public function it_cannot_update_a_logentry_with_invalid_data($getData)
    {
        Sanctum::actingAs($this->user);

        $logentry = Logentry::factory()->create($this->getValidLogentryData());

        [$ruleName, $payload] = $getData();

        $this->patch(route('logentries.update', $logentry), $payload)
            ->assertSessionHasErrors($ruleName);
    }

    public function logentryUpdateDataProvider()
    {
        return [
            'it fails if user_id is not an integer' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidLogentryData(), ['user_id' => 'not an integer']),
                    ];
                }
            ],
            'it fails if user_id is not in users table' => [
                function () {
                    return [
                        'user_id',
                        array_merge($this->getValidLogentryData(), ['user_id' => 999]),
                    ];
                }
            ],
            'it fails if food_id is not an integer' => [
                function () {
                    return [
                        'food_id',
                        array_merge($this->getValidLogentryData(), ['food_id' => 'not an integer']),
                    ];
                }
            ],
            'it fails if food_id is not in foods table' => [
                function () {
                    return [
                        'food_id',
                        array_merge($this->getValidLogentryData(), ['food_id' => 999999]),
                    ];
                }
            ],
            'it fails if quantity is not an integer' => [
                function () {
                    return [
                        'quantity',
                        array_merge($this->getValidLogentryData(), ['quantity' => 'not an integer']),
                    ];
                }
            ],
            'it fails if quantity is below zero' => [
                function () {
                    return [
                        'quantity',
                        array_merge($this->getValidLogentryData(), ['quantity' => -1]),
                    ];
                }
            ],
            'it fails if consumed_at is not a date' => [
                function () {
                    return [
                        'consumed_at',
                        array_merge($this->getValidLogentryData(), ['consumed_at' => 'not an integer']),
                    ];
                }
            ],
        ];
    }

    /** @test */
    public function it_can_delete_a_log_entry()
    {
        Sanctum::actingAs($this->user);

        $logentry = Logentry::factory()->create($this->getValidLogentryData());

        $this->delete(route('logentries.destroy', $logentry))
            ->assertRedirect(route('logentries.index'));

        $this->assertDatabaseMissing('logentries', $logentry->toArray());
    }

    /** @test */
    public function it_can_access_logentries_create_as_an_authenticated_user()
    {
        Sanctum::actingAs($this->user);

        $response = $this->get(route('logentries.create'))
        ->assertOk();
    }

    /** @test */
    public function it_cannot_access_logentries_create_if_unauthenticated()
    {
        $response = $this->get(route('logentries.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_can_create_a_log_entry_from_users_list_of_favourite_foods()
    {
        Sanctum::actingAs($this->user);

        $userFoods = Food::factory()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->favourites()->sync($userFoods->pluck('id'));

        $anotherUser = User::factory()->create();

        $anotherUserFoods = Food::factory()->count(1)->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->get(route('logentries.create'))
            ->assertOk()
            ->assertPropValue('foods', function ($returnedFoods) use($userFoods) {
                $this->assertEquals(2, count($returnedFoods['data']));
                foreach($returnedFoods['data'] as $index => $returnedFood){
                    $this->assertEquals($userFoods[$index]->description,$returnedFood['description']);
                }
            });
    }

    /** @test */
    public function it_can_create_a_log_entry_using_foodgroups()
    {
        Sanctum::actingAs($this->user);

        $foodgroups = Foodgroup::factory()->count(2)->create();

        $response = $this->get(route('logentries.create'))
            ->assertOk()
            ->assertPropValue('foodgroups', function ($returnedFoodgroups) use($foodgroups) {
                $this->assertEquals(2, count($returnedFoodgroups['data']));
                foreach($returnedFoodgroups['data'] as $index => $returnedFoodgroup){
                    $this->assertEquals($foodgroups[$index]->description,$returnedFoodgroup['description']);
                }
            });
    }

    /** @test */
    public function it_can_access_logentries_edit_as_an_authenticated_user()
    {
        Sanctum::actingAs($this->user);

        $logentry = Logentry::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('logentries.edit', $logentry))
            ->assertOk();
    }

    /** @test */
    public function it_cannot_access_logentries_edit_if_unauthenticated()
    {
        $response = $this->get(route('logentries.edit', 1))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_daily_total_potassium_with_index()
    {
        Sanctum::actingAs($this->user);

        $yesterdaysLogentry = Logentry::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => now()->subDays(2)
        ]);

        $todaysLogentries = Logentry::factory(2)->create([
            'user_id' => $this->user->id,
            'consumed_at' => now(),
        ]);

        $tomorrowsLogentries = Logentry::factory(3)->create([
            'user_id' => $this->user->id,
            'consumed_at' => now()->addDays(2)
        ]);

        $yesterdaysPotassium = $yesterdaysLogentry->food->potassium;

        $todaysPotassium = $todaysLogentries->reduce(function ($acc, $logentry) {
            return $acc+$logentry->food->potassium;
        }, 0);

        $tomorrowsPotassium = $tomorrowsLogentries->reduce(function ($acc, $logentry) {
            return $acc+$logentry->food->potassium;
        }, 0);

        $totalPotassium = $yesterdaysPotassium + $todaysPotassium + $tomorrowsPotassium;

        $response = $this->get(route('logentries.index'))
            ->assertOk()
            ->assertPropValue('logentries', function ($returnedLogentries) {
                $this->assertEquals(6, count($returnedLogentries['data']));
            })
            ->assertPropValue('todaysPotassium', $todaysPotassium)
            ->assertPropValue('totalPotassium', $totalPotassium);
    }

    /** @test */
    public function it_returns_default_period_start_and_end_dates_with_index()
    {
        Carbon::setTestNow();
        Sanctum::actingAs($this->user);
        $response = $this->get(route('logentries.index'))
            ->assertOk()
            ->assertPropValue('periodStart', now()->toDateString())
            ->assertPropValue('periodEnd', now()->addDays(7)->toDateString());
    }
}
