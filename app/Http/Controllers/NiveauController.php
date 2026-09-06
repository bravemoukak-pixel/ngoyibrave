<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Inertia\Inertia;

class NiveauController extends Controller
{
    public function index()
    {
        $niveauxQuery = Niveau::with('classes');

        if (auth()->user()->role === 'primaire') {
            $niveauxQuery->where('titre', 'Primaire');
        }

        $niveaux = $niveauxQuery->get()->map(function ($niveau) {
            return [
                'id' => $niveau->id,
                'titre' => $niveau->titre,
                'couleur' => $niveau->couleur,
                'icone' => $niveau->icone,
                'classes' => $niveau->classes->map(fn($classe) => [
                    'id' => $classe->id,
                    'nom' => $classe->nom,
                ]),
            ];
        });

        return Inertia::render('Niveaux', [
            'niveaux' => $niveaux,
        ]);
    }

    public function eleves(Niveau $niveau)
    {
        if (auth()->user()->role === 'primaire' && $niveau->titre !== 'Primaire') {
            abort(403);
        }

        $niveau->load('classes.eleves');

        $eleves = $niveau->classes->flatMap(function ($classe) {
            return $classe->eleves()->orderBy('nom')->get()->map(fn($eleve) => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'sexe' => $eleve->sexe,
                'date_naissance' => $eleve->date_naissance->format('d/m/Y'),
                'lieu_naissance' => $eleve->lieu_naissance,
                'adresse' => $eleve->adresse,
                'classe' => $classe->nom,
            ]);
        })->values();

        return Inertia::render('NiveauEleves', [
            'niveau' => [
                'id' => $niveau->id,
                'titre' => $niveau->titre,
            ],
            'eleves' => $eleves,
        ]);
    }
}
