<?php

use App\Models\Product;
use App\Models\ProductPromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('products can be listed with active promotion data', function (): void {
    $product = Product::factory()->create([
        'name' => 'Featured Coffee',
        'sku' => 'FEATURED-001',
    ]);

    ProductPromotion::factory()
        ->for($product)
        ->create([
            'promotion' => 'Launch discount',
            'promotion_started_at' => now()->subDay(),
            'promotion_ends_at' => now()->addDay(),
        ]);

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.0.sku', 'FEATURED-001')
        ->assertJsonPath('data.0.has_active_promotion', true)
        ->assertJsonPath('data.0.active_promotion.promotion', 'Launch discount');
});

test('a product can be created', function (): void {
    $payload = [
        'name' => 'API Product',
        'sku' => 'api-001',
        'description' => 'Created from the API.',
        'price' => 199.99,
        'currency' => 'mxn',
    ];

    $this->postJson('/api/products', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'API Product')
        ->assertJsonPath('data.sku', 'API-001')
        ->assertJsonPath('data.currency', 'MXN');

    $this->assertDatabaseHas('products', [
        'sku' => 'API-001',
        'name' => 'API Product',
    ]);
});

test('a product uses the default currency when it is not provided', function (): void {
    $this->postJson('/api/products', [
        'name' => 'Default Currency Product',
        'sku' => 'DEFAULT-CURRENCY-001',
        'price' => 99.99,
    ])
        ->assertCreated()
        ->assertJsonPath('data.currency', 'MXN');
});

test('product creation validation errors use the api response format', function (): void {
    $this->postJson('/api/products', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('data', null)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'sku',
                'price',
            ],
        ]);
});

test('a product can be updated', function (): void {
    $product = Product::factory()->create([
        'sku' => 'OLD-001',
        'price' => 100,
    ]);

    $this->patchJson("/api/products/{$product->id}", [
        'name' => 'Updated Product',
        'price' => 250.50,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Updated Product')
        ->assertJsonPath('data.price', '250.50');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
    ]);
});

test('a product can be soft deleted', function (): void {
    $product = Product::factory()->create();

    $this->deleteJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data', null);

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});
