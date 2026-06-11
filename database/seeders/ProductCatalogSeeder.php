<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed products, suppliers, promotions and their relationships.
     */
    public function run(): void
    {
        $suppliers = $this->seedSuppliers();
        $products = $this->seedProducts();

        $this->seedProductSuppliers($products, $suppliers);
        $this->seedPromotions($products);
    }

    /**
     * Create the base suppliers.
     *
     * @return Collection<string, Supplier>
     */
    private function seedSuppliers(): Collection
    {
        return collect([
            [
                'name' => 'Altura Cafe MX',
                'email' => 'contacto@alturacafe.mx',
                'phone' => '5551002001',
                'webs' => 'https://alturacafe.mx',
                'address_line' => 'Av. Insurgentes Sur 1200',
                'city' => 'Ciudad de Mexico',
                'state' => 'CDMX',
                'postal_code' => '03100',
            ],
            [
                'name' => 'Norte Distribuciones',
                'email' => 'ventas@nortedistribuciones.mx',
                'phone' => '8182003002',
                'webs' => 'https://nortedistribuciones.mx',
                'address_line' => 'Calzada San Pedro 455',
                'city' => 'Monterrey',
                'state' => 'Nuevo Leon',
                'postal_code' => '66220',
            ],
            [
                'name' => 'BioEmpaque Nacional',
                'email' => 'hola@bioempaque.mx',
                'phone' => '3334005003',
                'webs' => 'https://bioempaque.mx',
                'address_line' => 'Av. Mexico 2201',
                'city' => 'Guadalajara',
                'state' => 'Jalisco',
                'postal_code' => '44600',
            ],
            [
                'name' => 'Dulceria Central',
                'email' => 'pedidos@dulceriacentral.mx',
                'phone' => '2225006004',
                'webs' => 'https://dulceriacentral.mx',
                'address_line' => 'Calle 5 de Mayo 180',
                'city' => 'Puebla',
                'state' => 'Puebla',
                'postal_code' => '72000',
            ],
        ])->mapWithKeys(fn (array $supplier): array => [
            $supplier['email'] => Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                $supplier,
            ),
        ]);
    }

    /**
     * Create the base products.
     *
     * @return Collection<string, Product>
     */
    private function seedProducts(): Collection
    {
        return collect([
            [
                'name' => 'Cafe organico Chiapas 500g',
                'sku' => 'CAF-CHI-500',
                'description' => 'Cafe molido de tueste medio con notas de cacao y nuez.',
                'price' => 189.00,
                'currency' => 'MXN',
            ],
            [
                'name' => 'Termo acero inoxidable 750ml',
                'sku' => 'TER-INO-750',
                'description' => 'Termo de doble pared para bebidas frias y calientes.',
                'price' => 349.00,
                'currency' => 'MXN',
            ],
            [
                'name' => 'Caja empaque biodegradable mediana',
                'sku' => 'EMP-BIO-MED',
                'description' => 'Caja resistente para envio de productos pequenos y medianos.',
                'price' => 18.50,
                'currency' => 'MXN',
            ],
            [
                'name' => 'Chocolate artesanal 70%',
                'sku' => 'CHO-ART-70',
                'description' => 'Barra de chocolate semiamargo elaborada con cacao mexicano.',
                'price' => 95.00,
                'currency' => 'MXN',
            ],
            [
                'name' => 'Kit degustacion gourmet',
                'sku' => 'KIT-GOU-001',
                'description' => 'Seleccion de cafe, chocolate y accesorios para regalo.',
                'price' => 599.00,
                'currency' => 'MXN',
            ],
        ])->mapWithKeys(fn (array $product): array => [
            $product['sku'] => Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            ),
        ]);
    }

    /**
     * Attach products to suppliers with pivot data.
     *
     * @param  Collection<string, Product>  $products
     * @param  Collection<string, Supplier>  $suppliers
     */
    private function seedProductSuppliers(Collection $products, Collection $suppliers): void
    {
        $products->get('CAF-CHI-500')?->suppliers()->syncWithoutDetaching([
            $suppliers->get('contacto@alturacafe.mx')?->id => ['is_active' => true],
            $suppliers->get('ventas@nortedistribuciones.mx')?->id => ['is_active' => true],
        ]);

        $products->get('TER-INO-750')?->suppliers()->syncWithoutDetaching([
            $suppliers->get('ventas@nortedistribuciones.mx')?->id => ['is_active' => true],
        ]);

        $products->get('EMP-BIO-MED')?->suppliers()->syncWithoutDetaching([
            $suppliers->get('hola@bioempaque.mx')?->id => ['is_active' => true],
        ]);

        $products->get('CHO-ART-70')?->suppliers()->syncWithoutDetaching([
            $suppliers->get('pedidos@dulceriacentral.mx')?->id => ['is_active' => true],
            $suppliers->get('contacto@alturacafe.mx')?->id => ['is_active' => false],
        ]);

        $products->get('KIT-GOU-001')?->suppliers()->syncWithoutDetaching([
            $suppliers->get('contacto@alturacafe.mx')?->id => ['is_active' => true],
            $suppliers->get('hola@bioempaque.mx')?->id => ['is_active' => true],
            $suppliers->get('pedidos@dulceriacentral.mx')?->id => ['is_active' => true],
        ]);
    }

    /**
     * Create sample promotions for selected products.
     *
     * @param  Collection<string, Product>  $products
     */
    private function seedPromotions(Collection $products): void
    {
        $promotions = [
            'CAF-CHI-500' => [
                'promotion' => '15% de descuento en cafe de temporada',
                'promotion_started_at' => now()->subDays(3),
                'promotion_ends_at' => now()->addDays(14),
            ],
            'CHO-ART-70' => [
                'promotion' => '2x1 en la segunda barra seleccionada',
                'promotion_started_at' => now()->startOfDay(),
                'promotion_ends_at' => now()->addDays(7)->endOfDay(),
            ],
            'KIT-GOU-001' => [
                'promotion' => 'Envio gratis en kit gourmet',
                'promotion_started_at' => now()->subDay(),
                'promotion_ends_at' => now()->addDays(10),
            ],
        ];

        foreach ($promotions as $sku => $promotion) {
            $product = $products->get($sku);

            if (! $product instanceof Product) {
                continue;
            }

            ProductPromotion::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'promotion' => $promotion['promotion'],
                ],
                $promotion,
            );
        }
    }
}
