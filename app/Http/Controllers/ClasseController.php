<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClasseController extends Controller
{
    public function eleves(Classe $classe, Request $request)
    {
        if ($request->user()->role === 'primaire' && $classe->niveau->titre !== 'Primaire') {
            abort(403, "Vous n'avez pas accès à cette classe.");
        }

        $eleves = $classe->eleves()->orderBy('nom')->get()->map(fn($eleve) => [
            'id' => $eleve->id,
            'matricule' => $eleve->matricule,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'sexe' => $eleve->sexe,
            'date_naissance' => $eleve->date_naissance->format('d/m/Y'),
            'lieu_naissance' => $eleve->lieu_naissance,
            'adresse' => $eleve->adresse,
        ]);

        return Inertia::render('ClasseEleves', [
            'classe' => [
                'id' => $classe->id,
                'nom' => $classe->nom,
            ],
            'eleves' => $eleves,
        ]);
    }
}
