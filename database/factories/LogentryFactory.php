<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\User;
use App\Models\Logentry;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogentryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Logentry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $user = User::factory()->create(),
            'description' => $this->faker->sentence(),
            'kcal' => $this->faker->numberBetween(1, 300),
            'fat' => $this->faker->numberBetween(1, 300),
            'protein' => $this->faker->numberBetween(1, 300),
            'carbohydrate' => $this->faker->numberBetween(1, 300),
            'potassium' => $this->faker->numberBetween(1, 300),
            'quantity' => $this->faker->numberBetween(1,300),
            'consumed_at' => Carbon::now(),
            ];
    }
}
