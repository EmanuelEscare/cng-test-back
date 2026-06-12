<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductPromotion;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class GenerateStaleProductPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-stale-promotions {--days=14 : Days without sales required to generate a promotion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a 20% discount promotion for products without sales in the configured period.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = max((int) $this->option('days'), 1);
        $cutoff = now()->subDays($days);

        $products = Product::query()
            ->whereDoesntHave(
                'orderItems',
                fn (Builder $query): Builder => $query->where('sold_at', '>=', $cutoff),
            )
            ->whereDoesntHave(
                'promotions',
                fn (Builder $query): Builder => $query->active(),
            )
            ->get();

        $products->each(function (Product $product) use ($days): void {
            ProductPromotion::create([
                'product_id' => $product->id,
                'promotion' => "20% discount for products without sales in {$days} days",
                'promotion_started_at' => now(),
                'promotion_ends_at' => now()->addDays(14),
                'discount_percentage' => 20,
            ]);
        });

        $this->info("Generated {$products->count()} stale product promotions.");

        return self::SUCCESS;
    }
}
