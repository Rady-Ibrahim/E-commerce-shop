<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'الإلكترونيات',
                'description' => 'أحدث الأجهزة الإلكترونية والهواتف الذكية',
                'image' => 'storage/images/dc.jpg ',
            ],
            [
                'name' => 'الملابس',
                'description' => 'أحدث صيحات الموضة والملابس',
                'image' => 'storage/images/ms1.jpg',
            ],
            [
                'name' => 'الأحذية',
                'description' => 'أحذية رياضية وعادية',
                'image' => 'storage/images/Shoe.jpg',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'image' => $categoryData['image'],
            ]);

            // Create Products for each category
            for ($i = 1; $i <= 8; $i++) {
                $product = Product::create([
                    'name' => "منتج {$categoryData['name']} {$i}",
                    'slug' => Str::slug("منتج {$categoryData['name']} {$i}"),
                    'description' => "وصف تفصيلي لمنتج {$categoryData['name']} رقم {$i}",
                    'price' => rand(100, 1000),
                    'sale_price' => rand(0, 1) ? rand(50, 900) : null,
                    'quantity' => rand(0, 50),
                    'sku' => 'SKU-' . rand(1000, 9999),
                    'is_active' => true,
                    'category_id' => $category->id,
                ]);

                // Add product images
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'storage/images/camera.jpg',
                    'is_primary' => true,
                    'sort_order' => 1,
                ]);

                // Add additional images
                for ($j = 2; $j <= 4; $j++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => "https://via.placeholder.com/400x400?text=Image{$j}",
                        'is_primary' => false,
                        'sort_order' => $j,
                    ]);
                }
            }
        }
    }
}
