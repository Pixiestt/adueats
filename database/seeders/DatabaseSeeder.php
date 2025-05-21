<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if doesn't exist
        if (!User::where('email', 'admin@adueats.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@adueats.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create food categories
        $categories = [
            [
                'name' => 'Rice Meals',
                'description' => 'Complete meals with rice'
            ],
            [
                'name' => 'Sandwiches',
                'description' => 'Quick and easy sandwiches'
            ],
            [
                'name' => 'Beverages',
                'description' => 'Refreshing drinks'
            ],
            [
                'name' => 'Snacks',
                'description' => 'Light snacks and sides'
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create products
        $products = [
            // Rice Meals
            [
                'name' => 'Adobo Rice Bowl',
                'description' => 'Classic Filipino adobo with rice',
                'price' => 85.00,
                'category_id' => 1,
                'image' => 'adobo.jpg',
                'available' => true
            ],
            [
                'name' => 'Sisig Rice Bowl',
                'description' => 'Sizzling sisig served with rice',
                'price' => 95.00,
                'category_id' => 1,
                'image' => 'sisig.jpg',
                'available' => true
            ],
            [
                'name' => 'Beef Tapa',
                'description' => 'Sweet beef tapa with rice and egg',
                'price' => 105.00,
                'category_id' => 1,
                'image' => 'tapa.jpg',
                'available' => true
            ],
            
            // Sandwiches
            [
                'name' => 'Chicken Sandwich',
                'description' => 'Grilled chicken with lettuce and mayo',
                'price' => 65.00,
                'category_id' => 2,
                'image' => 'chicken_sandwich.jpg',
                'available' => true
            ],
            [
                'name' => 'Tuna Melt',
                'description' => 'Tuna sandwich with melted cheese',
                'price' => 75.00,
                'category_id' => 2,
                'image' => 'tuna_melt.jpg',
                'available' => true
            ],
            
            // Beverages
            [
                'name' => 'Iced Tea',
                'description' => 'Refreshing iced tea',
                'price' => 35.00,
                'category_id' => 3,
                'image' => 'iced_tea.jpg',
                'available' => true
            ],
            [
                'name' => 'Soda',
                'description' => 'Carbonated soft drink',
                'price' => 40.00,
                'category_id' => 3,
                'image' => 'soda.jpg',
                'available' => true
            ],
            [
                'name' => 'Coffee',
                'description' => 'Hot brewed coffee',
                'price' => 45.00,
                'category_id' => 3,
                'image' => 'coffee.jpg',
                'available' => true
            ],
            
            // Snacks
            [
                'name' => 'French Fries',
                'description' => 'Crispy fried potatoes',
                'price' => 55.00,
                'category_id' => 4,
                'image' => 'fries.jpg',
                'available' => true
            ],
            [
                'name' => 'Nachos',
                'description' => 'Tortilla chips with cheese',
                'price' => 65.00,
                'category_id' => 4,
                'image' => 'nachos.jpg',
                'available' => true
            ],
            
            // Desserts
            [
                'name' => 'Leche Flan',
                'description' => 'Sweet caramel custard',
                'price' => 50.00,
                'category_id' => 5,
                'image' => 'leche_flan.jpg',
                'available' => true
            ],
            [
                'name' => 'Halo-Halo',
                'description' => 'Mixed Filipino dessert with shaved ice',
                'price' => 75.00,
                'category_id' => 5,
                'image' => 'halo_halo.jpg',
                'available' => true
            ]
        ];

        foreach ($products as $product) {
            $createdProduct = Product::create($product);
            
            // Create inventory entry for each product
            Inventory::create([
                'product_id' => $createdProduct->id,
                'quantity' => rand(10, 50),
                'minimum_stock' => 10,
                'last_restock_date' => now()
            ]);
        }
    }
}
