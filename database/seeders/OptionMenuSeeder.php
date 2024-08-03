<?php

namespace Database\Seeders;

use App\Models\Optionmenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Reporte Accesos', 'route' => 'reporteaccesos', 'groupmenu_id' => 2, 'icon' => 'fa-solid fa-file-circle-check'],
            ['name' => 'Gestionar Usuarios', 'route' => 'manage-users', 'groupmenu_id' => 3, 'icon' => 'fa fa-users-cog'],
            ['name' => 'Usuario', 'route' => 'user', 'groupmenu_id' => 3, 'icon' => 'fa fa-user-cog'],
            ['name' => 'Personas Autorizadas', 'route' => 'autorizado', 'groupmenu_id' => 1, 'icon' => 'fa fa-users-cog'],
        ];

        foreach ($array as $object) {

            Optionmenu::create($object);

        }
    }
}
