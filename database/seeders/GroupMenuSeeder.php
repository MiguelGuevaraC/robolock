<?php

namespace Database\Seeders;

use App\Models\GroupMenu;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $array = [

            ['name' => 'Administración', 'icon' => 'fa fa-exchange-alt'],
            ['name' => 'Reporte', 'icon' => 'fa fa-chart-bar'],
            ['name' => 'Seguridad', 'icon' => 'fa fa-shield-alt'],
        ];

        foreach ($array as $object) {

            GroupMenu::create($object);

        }
    }
}
