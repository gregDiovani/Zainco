<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{

    protected $model = ProductCategory::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($this->faker));
        return [
            'name' => $this->faker->beverageName(),
            'users_id' => User::all('id')->random(),
        ];
    }
}
