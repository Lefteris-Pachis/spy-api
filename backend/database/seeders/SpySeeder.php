<?php

namespace Database\Seeders;

use App\Models\Spy;
use Illuminate\Database\Seeder;

class SpySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spies = [
            [
                'name' => 'James',
                'surname' => 'Bond',
                'agency' => 'MI6',
                'country_of_operation' => 'United Kingdom',
                'date_of_birth' => '1920-04-13',
                'date_of_death' => null,
            ],
            [
                'name' => 'Natasha',
                'surname' => 'Romanoff',
                'agency' => 'KGB',
                'country_of_operation' => 'Russia',
                'date_of_birth' => '1984-11-22',
                'date_of_death' => null,
            ],
            [
                'name' => 'Jason',
                'surname' => 'Bourne',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1970-02-15',
                'date_of_death' => null,
            ],
            [
                'name' => 'Ethan',
                'surname' => 'Hunt',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1971-08-14',
                'date_of_death' => null,
            ],
            [
                'name' => 'Vesper',
                'surname' => 'Lynd',
                'agency' => 'MI6',
                'country_of_operation' => 'United Kingdom',
                'date_of_birth' => '1980-05-01',
                'date_of_death' => '2006-11-16',
            ],
            [
                'name' => 'Tom',
                'surname' => 'Rider',
                'agency' => 'MI6',
                'country_of_operation' => 'United Kingdom',
                'date_of_birth' => '1975-03-10',
                'date_of_death' => null,
            ],
            [
                'name' => 'Mikhail',
                'surname' => 'Kovalenko',
                'agency' => 'KGB',
                'country_of_operation' => 'Russia',
                'date_of_birth' => '1965-07-30',
                'date_of_death' => null,
            ],
            [
                'name' => 'Claire',
                'surname' => 'Underwood',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1983-06-15',
                'date_of_death' => null,
            ],
            [
                'name' => 'William',
                'surname' => 'Grayson',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1990-02-28',
                'date_of_death' => null,
            ],
            [
                'name' => 'Lucas',
                'surname' => 'Davis',
                'agency' => 'MI6',
                'country_of_operation' => 'United Kingdom',
                'date_of_birth' => '1960-12-05',
                'date_of_death' => null,
            ],
            [
                'name' => 'Sergei',
                'surname' => 'Petrov',
                'agency' => 'KGB',
                'country_of_operation' => 'Russia',
                'date_of_birth' => '1980-08-19',
                'date_of_death' => null,
            ],
            [
                'name' => 'Lara',
                'surname' => 'Croft',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1995-01-12',
                'date_of_death' => null,
            ],
            [
                'name' => 'Simon',
                'surname' => 'Tanner',
                'agency' => 'CIA',
                'country_of_operation' => 'United States',
                'date_of_birth' => '1988-04-20',
                'date_of_death' => null,
            ],
        ];

        foreach ($spies as $spy) {
            Spy::create($spy);
        }
    }
}
