<?php
namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class UserProductsReset extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->update(array(
            'wallet' => 100
        ));
        DB::table('products')->update(array(
            'count' => 8
        ));
    }
}

?>