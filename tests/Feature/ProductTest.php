<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{

    public function test_displaying_products()
    {
        Product::factory()->count(15)->create();
        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'category',
                ],
            ],
        ]);
    }

    public function test_create_products()
    {
        // Arrange: Prepare data for a new product
        $productData = [
            'name' => 'Sample Product',
            'description' => 'A great product',
            'price' => 20,
            'category' => 'Electronics',
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_sorting_categories()
    {
        $products = Product::factory()->count(10)->create(['category' => 'Toys']);
        Product::factory()->count(2)->create(['category' => 'Clothing']);
        $response = $this->get('/api/products?category=Toys');
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment(['category' => 'Toys']);
    }
    function test_getting_maximum_number()
    {
        Product::factory()->create(['price' => 30]);
        Product::factory()->create(['price' => 40]);
        $response = $this->get('api/products/filter?max_number=35');
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
    }
    function test_getting_minimum_number()
    {
        Product::factory()->create(['price' => 10]);
        Product::factory()->create(['price' => 20]);
        $response = $this->get('api/products/filter?min_number=1');
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
    }

}
