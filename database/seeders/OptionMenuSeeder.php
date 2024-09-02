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
            ['id' => '1', 'name' => 'Reporte Accesos', 'route' => 'reporteaccesos', 'groupmenu_id' => 2, 'icon' => 'fa-solid fa-file-circle-check'],
            ['id' => '2', 'name' => 'Personas Autorizadas', 'route' => 'estudiante', 'groupmenu_id' => 1, 'icon' => 'fa fa-users-cog'],
            ['id' => '3', 'name' => 'Gestionar Accesos', 'route' => 'access', 'groupmenu_id' => 3, 'icon' => 'fa fa-lock'],
            ['id' => '4', 'name' => 'Usuario', 'route' => 'user', 'groupmenu_id' => 3, 'icon' => 'fa fa-user'],
            ['id' => '5', 'name' => 'Dashboard', 'route' => 'dashboard', 'groupmenu_id' => 2, 'icon' => 'fa-solid fa-chart-line'],
        ];

        foreach ($array as $object) {
            Optionmenu::create($object);
        }
    }
}
