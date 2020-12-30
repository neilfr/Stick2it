<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Food::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
            'alias' => $this->faker->sentence,
            'kcal' => $this->faker->numberBetween(1, 300),
            'fat' => $this->faker->numberBetween(1, 300),
            'protein' => $this->faker->numberBetween(1, 300),
            'carbohydrate' => $this->faker->numberBetween(1, 300),
            'potassium' => $this->faker->numberBetween(1, 300),
            'base_quantity' => $this->faker->numberBetween(1,300),
            'foodgroup_id' => factory(Foodgroup::class)->create()->id,
            'foodsource_id' => factory(Foodsource::class)->create()->id,
            'user_id' => auth()->user()->id,
        ];
    }
}
