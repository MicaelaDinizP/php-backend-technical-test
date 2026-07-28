<?php

namespace ContatoSeguro\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Contatoseguro\TesteBackend\Model\Product;

class ProductTest extends TestCase
{
    public function testConstructor(): void
    {
        $product = new Product(
            1,
            2,
            'Test Product',
            99.99,
            true,
            10,
            '2023-10-01'
        );

        $this->assertEquals(1, $product->id);
        $this->assertEquals(2, $product->companyId);
        $this->assertEquals('Test Product', $product->title);
        $this->assertEquals(99.99, $product->price);
        $this->assertTrue($product->active);
        $this->assertEquals(10, $product->stock);
        $this->assertEquals('2023-10-01', $product->createdAt);
        $this->assertEquals([], $product->categories);
    }

    public function testHydrateByFetch(): void
    {
        $fetch = new \stdClass();
        $fetch->id = 1;
        $fetch->company_id = 2;
        $fetch->title = 'Test Product';
        $fetch->price = 99.99;
        $fetch->active = true;
        $fetch->stock = 10;
        $fetch->created_at = '2023-10-01';

        $product = Product::hydrateByFetch($fetch);

        $this->assertEquals(1, $product->id);
        $this->assertEquals(2, $product->companyId);
        $this->assertEquals('Test Product', $product->title);
        $this->assertEquals(99.99, $product->price);
        $this->assertTrue($product->active);
        $this->assertEquals(10, $product->stock);
        $this->assertEquals('2023-10-01', $product->createdAt);
        $this->assertEquals([], $product->categories);
    }

    public function testSetCategories(): void
    {
        $product = new Product(
            1,
            2,
            'Test Product',
            99.99,
            true,
            10,
            '2023-10-01'
        );

        $categories = [
            [
                'id' => 1,
                'title' => 'Electronics'
            ],
            [
                'id' => 2,
                'title' => 'Computers'
            ]
        ];

        $product->setCategories($categories);

        $this->assertEquals($categories, $product->categories);
    }
}