<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPromotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPromotion>
 */
class ProductPromotionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ProductPromotion>
     */
    protected $model = ProductPromotion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 week', '+1 week');

        return [
            'product_id' => Product::factory(),
            'promotion_started_at' => $startsAt,
            'promotion_ends_at' => fake()->dateTimeBetween($startsAt, '+1 month'),
            'promotion' => fake()->randomElement([
                '10% de descuento',
                '2x1 por temporada',
                'Envio gratis',
                'Precio especial por volumen',
            ]),
        ];
    }
}
