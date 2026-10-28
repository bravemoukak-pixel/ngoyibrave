<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\HistoriqueScolarite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoriqueClasseController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classe::orderBy('nom')->get(['id', 'nom']);
        $annees = HistoriqueScolarite::distinct()->pluck('annee_scolaire')->sortDesc()->values();

        $classeId = $request->query('classe_id');
        $anneeScolaire = $request->query('annee_scolaire');

        $eleves = null;
        $classeNom = null;

        if ($classeId && $anneeScolaire) {
            $classe = Classe::find($classeId);
            $classeNom = $classe?->nom;

            $eleves = HistoriqueScolarite::where('classe_id', $classeId)
                ->where('annee_scolaire', $anneeScolaire)
                ->with('eleve')
                ->get()
                ->pluck('eleve')
                ->filter()
                ->sortBy('nom')
                ->values()
                ->map(fn($eleve) => [
                    'id' => $eleve->id,
                    'matricule' => $eleve->matricule,
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'sexe' => $eleve->sexe,
                ]);
        }

        return Inertia::render('HistoriqueClasses/Index', [
            'classes' => $classes,
            'annees' => $annees,
            'classeId' => $classeId,
            'anneeScolaire' => $anneeScolaire,
            'classeNom' => $classeNom,
            'eleves' => $eleves,
        ]);
    }
}
