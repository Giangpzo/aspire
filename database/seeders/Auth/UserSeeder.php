<?php

namespace Database\Seeders\Auth;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\Reader;

class UserSeeder extends Seeder
{
    // php artisan db:seed --class=Database\\Seeders\\Auth\\UserSeeder

    /**
     * Seed user data
     *
     * @throws Exception
     */
    public function run(): void
    {
        $fileLocation = storage_path('data/user.csv');
        $csv = Reader::createFromPath($fileLocation, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            User::updateOrCreate([
                'id' => $record['id']
            ],[
                'id'=>$record['id'],
                'name'=>$record['name'],
                'email'=>$record['email'],
                'type'=>$record['type'],
                'password'=>$record['password']
            ]);
        }
    }
}
