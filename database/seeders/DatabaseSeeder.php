<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AccessLog;
use App\Models\Person;
use App\Models\Photo;
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
                'email' => 'johndo@gmail.com',
 
            ], [
                'id' => '5',
                'typeofDocument' => 'DNI',
                'documentNumber' => '22222222',
                'names' => 'Estudiante',
                'fatherSurname' => 'Padre',
                'motherSurname' => 'Madre',
                
                'telephone' => '987654321',
                'email' => 'padredeestudiante@gmail.com',
      
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

        $people = Person::factory(3)->create();

        foreach ($people as $person) {
            Photo::factory(3)->create(['authorized_person_id' => $person->id]);
            AccessLog::factory(5)->create(['authorized_person_id' => $person->id]);
        }

        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);

        $this->call(OptionMenuSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AccessSeeder::class);
    }
}
