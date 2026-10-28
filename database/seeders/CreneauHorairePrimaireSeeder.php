<?php

namespace Database\Seeders;

use App\Models\CreneauHoraire;
use Illuminate\Database\Seeder;

class CreneauHorairePrimaireSeeder extends Seeder
{
    public function run(): void
    {
        $vagueA = [
            ['07:00', '08:00', 'cours'],
            ['08:00', '09:45', 'cours'],
            ['09:45', '10:15', 'recreation'],
            ['10:15', '11:00', 'cours'],
            ['11:00', '12:00', 'cours'],
        ];

        $vagueB = [
            ['12:15', '13:15', 'cours'],
            ['13:15', '14:45', 'cours'],
            ['14:45', '15:15', 'recreation'],
            ['15:15', '16:15', 'cours'],
            ['16:15', '17:15', 'cours'],
        ];

        foreach ($vagueA as $i => [$debut, $fin, $type]) {
            CreneauHoraire::create([
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'ordre' => $i + 1,
                'type' => $type,
                'grille' => 'primaire_a',
            ]);
        }

        foreach ($vagueB as $i => [$debut, $fin, $type]) {
            CreneauHoraire::create([
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'ordre' => $i + 1,
                'type' => $type,
                'grille' => 'primaire_b',
            ]);
        }
    }
}
