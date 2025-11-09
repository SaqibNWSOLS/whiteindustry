<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Blend products
            [
                'product_code' => 'BL-001',
                'name' => 'Premium Coffee Blend',
                'description' => 'A premium blend of Arabica and Robusta beans',
                'category' => 'blend',
                'product_type' => 'finished_goods',
                'unit_price' => 25.50,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'BL-002',
                'name' => 'Special Tea Blend',
                'description' => 'Herbal tea blend with natural ingredients',
                'category' => 'blend',
                'product_type' => 'finished_goods',
                'unit_price' => 18.75,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],

            // Raw material products
            [
                'product_code' => 'RM-001',
                'name' => 'Arabica Coffee Beans',
                'description' => 'High-quality Arabica coffee beans',
                'category' => 'raw_material',
                'product_type' => 'raw_material',
                'unit_price' => 15.00,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'RM-002',
                'name' => 'Green Tea Leaves',
                'description' => 'Organic green tea leaves',
                'category' => 'raw_material',
                'product_type' => 'raw_material',
                'unit_price' => 12.50,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'RM-003',
                'name' => 'Cane Sugar',
                'description' => 'Organic cane sugar',
                'category' => 'raw_material',
                'product_type' => 'raw_material',
                'unit_price' => 8.25,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],

            // Packaging products
            [
                'product_code' => 'PK-001',
                'name' => '250g Foil Bags',
                'description' => 'Stand-up foil bags with one-way valve',
                'category' => 'packaging',
                'product_type' => 'packaging',
                'unit_price' => 0.85,
                'volume'=> 10,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'PK-002',
                'name' => 'Product Labels',
                'description' => 'Custom printed product labels',
                'category' => 'packaging',
                'product_type' => 'packaging',
                'unit_price' => 0.15,
                'volume'=> 10,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'PK-003',
                'name' => 'Shipping Boxes',
                'description' => 'Corrugated cardboard shipping boxes',
                'category' => 'packaging',
                'product_type' => 'packaging',
                'unit_price' => 2.50,
                'volume'=> 10,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],

            // Final product products
            [
                'product_code' => 'FP-001',
                'name' => 'Packaged Coffee 250g',
                'description' => 'Premium coffee blend in 250g packaging',
                'category' => 'final_product',
                'product_type' => 'finished_goods',
                'unit_price' => 32.00,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'FP-002',
                'name' => 'Packaged Tea 100g',
                'description' => 'Special tea blend in 100g packaging',
                'category' => 'final_product',
                'product_type' => 'finished_goods',
                'unit_price' => 22.50,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
            [
                'product_code' => 'FP-003',
                'name' => 'Coffee Gift mg',
                'description' => 'Premium coffee gift mg with accessories',
                'category' => 'final_product',
                'product_type' => 'finished_goods',
                'unit_price' => 89.99,
                'unit_of_measure' => 'mg',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}