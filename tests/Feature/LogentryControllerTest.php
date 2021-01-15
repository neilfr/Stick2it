<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
use Carbon\Carbon;
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
        $response = $this->get(route('logentries.index', 1))
            ->assertRedirect();
    }

    /** @test */
    public function it_can_return_a_list_of_users_logentries()
    {
        Sanctum::actingAs($this->user);

        $foods = Food::factory(2)->create([
            'user_id' => $this->user->id,
        ]);

        foreach($foods as $index => $food){
            $logentries[$index] = Logentry::factory()->create([
                'user_id' => $this->user->id,
                'food_id' => $food->id,
            ]);
        }

        foreach($logentries as $logentry){
            $this->user->logentries()->save($logentry);
        }

        $response = $this->get(route('logentries.index', $this->user));
        $response->assertOk();
        $response->assertPropValue('logentries', function ($returnedLogentries) use($logentries) {
            $this->assertEquals(2, count($returnedLogentries['data']));
            foreach($returnedLogentries['data'] as $index => $returnedLogentry){
                $this->assertEquals($logentries[$index]->food->description,$returnedLogentry['food']['description']);
                $this->assertEquals($logentries[$index]->user->email,$returnedLogentry['user']['email']);
            }
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

        $payload = [
            "user_id" => $this->user->id,
            'food_id' => Food::factory()->create(["user_id" => $this->user->id])->id,
            'quantity' => 100,
            'consumed_at' => Carbon::now(),
        ];

        $this->post(route('logentries.store'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('logentries', $payload);
    }

    /** @test */
    public function it_cannot_store_a_new_logentry_with_invalid_data()
    {
        Sanctum::actingAs($this->user);

        Food::factory()->create(["user_id" => $this->user->id]);

        $payload = [
            "user_id" => 999,
            'food_id' => 11,
            'quantity' => -1,
            'consumed_at' => "not a date",
        ];

        $this->post(route('logentries.store'), $payload)
            ->assertSessionHasErrors(['user_id','food_id','quantity','consumed_at']);

        $this->assertDatabaseMissing('logentries', $payload);
    }
}
