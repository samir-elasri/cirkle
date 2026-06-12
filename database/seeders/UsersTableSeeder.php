<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Str;

class UsersTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create('fr_CA');

        User::create([
            'email' 			=> 'mbiance@mbiance.com',
            'first_name' 		=> 'mbiance',
            'last_name' 		=> 'mbiance',
            'password' 			=> "QErjxQDoAHUt",
            'previous_login'  	=> $faker->dateTimeThisYear(),
            'admin'				=> true,
            'active'			=> true,
        ]);
    }

    public function createUserPassword()
    {
        $password = Str::random(12);
        $fp = fopen(__FILE__, 'r');
        $file_contents = fread($fp, filesize(__FILE__));
		$output = str_replace('"QErjxQDoAHUt"', '"' . $password . '"', $file_contents);
        fclose($fp);
        $fp = fopen(__FILE__, 'w');
        fwrite($fp, $output, strlen($output));
        fclose($fp);
        echo "=========CMS============\n" . 'mbiance@mbiance.com / ' .  $password . "\n\n";
        return $password;
    }
}
