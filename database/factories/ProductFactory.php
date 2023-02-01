<?php

namespace Database\Factories;


use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($this->faker));

        return [
            'name' => $this->faker->foodName(),
            'price' => $this->faker->randomNumber(2),
            'users_id' => User::all('id')->random(),
            'categories_id' => ProductCategory::all('id')->random(),
           
        ];
    }
}
