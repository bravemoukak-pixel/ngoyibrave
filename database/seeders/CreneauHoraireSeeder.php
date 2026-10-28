<?php

namespace Database\Seeders;

use App\Models\CreneauHoraire;
use Illuminate\Database\Seeder;

class CreneauHoraireSeeder extends Seeder
{
    public function run(): void
    {
        $creneaux = [
            ['07:15', '08:15', 'cours'],
            ['08:15', '09:15', 'cours'],
            ['09:15', '10:15', 'cours'],
            ['10:15', '10:45', 'recreation'],
            ['10:45', '11:45', 'cours'],
            ['11:45', '12:45', 'cours'],
            ['12:45', '13:45', 'cours'],
            ['13:45', '14:15', 'pause'],
            ['14:15', '15:15', 'cours'],
            ['15:15', '16:15', 'cours'],
            ['16:15', '17:15', 'cours'],
        ];

        foreach ($creneaux as $i => [$debut, $fin, $type]) {
            CreneauHoraire::create([
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'ordre' => $i + 1,
                'type' => $type,
            ]);
        }
    }
}
