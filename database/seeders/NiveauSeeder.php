<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        $donnees = [
            [
                'titre' => 'Collège',
                'couleur' => 'blue',
                'icone' => '🏫',
                'classes' => ['6ᵉ', '5ᵉ', '4ᵉ', '3ᵉ'],
            ],
            [
                'titre' => 'Lycée - Série A',
                'couleur' => 'green',
                'icone' => '📘',
                'classes' => ['Seconde A', 'Première A', 'Terminale A'],
            ],
            [
                'titre' => 'Lycée - Série C',
                'couleur' => 'purple',
                'icone' => '📗',
                'classes' => ['Seconde C', 'Première C', 'Terminale C'],
            ],
            [
                'titre' => 'Lycée - Série D',
                'couleur' => 'red',
                'icone' => '📕',
                'classes' => ['Première D', 'Terminale D'],
            ],
        ];

        foreach ($donnees as $entry) {
            $niveau = Niveau::create([
                'titre' => $entry['titre'],
                'couleur' => $entry['couleur'],
                'icone' => $entry['icone'],
            ]);

            foreach ($entry['classes'] as $nomClasse) {
                $niveau->classes()->create(['nom' => $nomClasse]);
            }
        }
    }
}
