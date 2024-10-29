<?php
namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = array();
        array_push($products, self::getProduct('phone', 8, 20));
        array_push($products, self::getProduct('tablet', 8, 30));
        DB::table('products')->insert($products);
    }

    private function getProduct(string $name, int $count, float $price): array
    {
        $now = Carbon::now();
        $product = array(
            'product_name' => $name,
            'count' => $count,
            'price' => $price,
            'created_at' => $now,
            'updated_at' => $now,
        );
        return $product;
    }
}

?>