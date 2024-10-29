<?php
namespace Database\Seeders;

use DB;
use Str;
use Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{

    public $limit = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limit = $this->limit;
        $data = array();
        for ($i = 0; $i < $limit; $i ++) {
            $user = self::generateUser($i);
            array_push($data, $user);
        }
        DB::table('users')->insert($data);
    }

    private function generateUser(int $i)
    {
        $now = Carbon::now();
        $time = time();
        $user = [
            'name' => Str::random(10),
            'email' => Str::random(10) . '_' . $time . '_' . $i . '@email.mkom',
            'password' => Hash::make('nopassword'),
            'created_at' => $now,
            'updated_at' => $now,
        ];
        return $user;
    }
}

?>