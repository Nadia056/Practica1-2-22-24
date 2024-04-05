<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class productSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'A laptop is a computer that is portable and suitable for use while traveling.',
            'price' => '500',
            'category_id' => '1',
            'status' => 'active'

        ]);
        Product::create([
            'name' => 'T-shirt',
            'description' => 'A T-shirt is a style of fabric shirt named after the T shape of its body and sleeves.',
            'price' => '20',
            'category_id' => '2',
            'status' => 'active'
        ]);
        Product::create([
            'name' => 'Harry Potter',
            'description' => 'Harry Potter is a series of seven fantasy novels written by British author J. K. Rowling.',
            'price' => '30',
            'category_id' => '3',
            'status' => 'active'
        ]);
        Product::create([
            'name' => 'Table',
            'description' => 'A table is an item of furniture with a flat top and one or more legs, used as a surface for working at, eating from or on which to place things.',
            'price' => '100',
            'category_id' => '4',
            'status' => 'active'
        ]);
        Product::create([
            'name' => 'Lego',
            'description' => 'Lego is a line of plastic construction toys that are manufactured by The Lego Group.',
            'price' => '50',
            'category_id' => '5',
            'status' => 'active'
        ]);
        Product::create([
            'name' => 'Pizza',
            'description' => 'Pizza is a savory dish of Italian origin consisting of a usually round, flattened base of leavened wheat-based dough topped with tomatoes, cheese, and often various other ingredients.',
            'price' => '10',
            'category_id' => '6',
            'status' => 'active'
        ]);
        


    }
}
