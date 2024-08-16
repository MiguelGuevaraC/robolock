<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Person;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $array = [
            [
                'id' => '1',
                'typeofDocument' => 'DNI',
                'documentNumber' => '11111110',
                'names' => 'Administrador',
                'fatherSurname' => '-',
                'motherSurname' => '-',

                'telephone' => '903017426',
                'email' => 'guevaracajusolmiguel@gmail.com',

            ],
            [
                'id' => '2',
                'typeofDocument' => 'DNI',
                'documentNumber' => '00000000',
                'names' => 'VARIOS',
                'fatherSurname' => '-',
                'motherSurname' => '-',

                'telephone' => '903017426',
                'email' => 'johndoe@gmail.com',

            ],
            [
                'id' => '3',
                'typeofDocument' => 'DNI',
                'documentNumber' => '11111111',
                'names' => 'Administrador',
                'fatherSurname' => '-',
                'motherSurname' => '-',

                'telephone' => '903017426',
                'email' => 'guevaracajusolmiguel@gmail.com',

            ],
            [
                'id' => '4',
                'typeofDocument' => 'DNI',
                'documentNumber' => '75258022',
                'names' => 'Miguel Angel',
                'fatherSurname' => 'Guevara',
                'motherSurname' => 'Cajusol',
                'telephone' => '903017426',
                'email' => 'mguevaracaj@unprg.edu.pe',
                'uid' => '425397269755',
            ], [
                'id' => '5',
                'typeofDocument' => 'DNI',
                'documentNumber' => '74567191',
                'names' => 'Leonardo',
                'fatherSurname' => 'Rivadeneira',
                'motherSurname' => 'Romero',
                'telephone' => '932152135',
                'email' => 'lrivadeneira@unprg.edu.pe',
                'uid' => '4328719364',
            ],
            [
                'id' => '6',
                'typeofDocument' => 'DNI',
                'documentNumber' => '74208869',
                'names' => 'Gardenia',
                'fatherSurname' => 'Zapata',
                'motherSurname' => 'Vargas',
                'telephone' => '907670558',
                'email' => 'lzapatav@unprg.edu.pe',
                'uid' => '408504999976',
            ],
            [ 
                'id' => '7',
                'typeofDocument' => 'DNI',
                'documentNumber' => '77169795',
                'names' => 'Smith',
                'fatherSurname' => 'Fernandez',
                'motherSurname' => 'Valle',
                'telephone' => '962902259',
                'email' => 'afernandezval@unprg.edu.pe',
                'uid' => 'PARAQUE',
            ],
            [
                'id' => '8',
                'typeofDocument' => 'DNI',
                'documentNumber' => '11111112',
                'names' => 'Juan',
                'fatherSurname' => 'Villegas',
                'motherSurname' => 'Cubas',

                'telephone' => '9999999999',
                'email' => 'jvillegasc@unprg.edu.pe',
                'uid' => '98745632148',
            ],

        ];

        foreach ($array as $object) {
            $typeOfuser1 = Person::find($object['id']);
            if ($typeOfuser1) {
                $typeOfuser1->update($object);
            } else {
                Person::create($object);
            }
        }

        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);

        $this->call(OptionMenuSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AccessSeeder::class);
    }
}
