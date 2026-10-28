<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuiviPaiementController extends Controller
{
    public function index(Request $request)
    {
        $classeId = $request->query('classe_id');
        $mois = $request->query('mois', now()->format('Y-m'));

        $classes = Classe::orderBy('nom')->get(['id', 'nom']);

        $eleves = null;
        $classeSelectionnee = null;

        if ($classeId) {
            $classeSelectionnee = Classe::findOrFail($classeId);

            $eleves = $classeSelectionnee->eleves()
                ->with('parentEleve.paiements')
                ->orderBy('nom')
                ->get()
                ->map(function ($eleve) use ($mois) {
                    $aPaye = false;

                    if ($eleve->parentEleve) {
                        $aPaye = $eleve->parentEleve->paiements
                            ->contains(fn($p) => $p->mois->format('Y-m') === $mois);
                    }

                    return [
                        'id' => $eleve->id,
                        'matricule' => $eleve->matricule,
                        'nom' => $eleve->nom,
                        'prenom' => $eleve->prenom,
                        'parent' => $eleve->parentEleve
                            ? $eleve->parentEleve->nom . ' ' . $eleve->parentEleve->prenom
                            : '—',
                        'telephone' => $eleve->parentEleve?->telephone ?? '—',
                        'a_paye' => $aPaye,
                    ];
                });
        }

        return Inertia::render('SuiviPaiements', [
            'classes' => $classes,
            'classeId' => $classeId,
            'classeNom' => $classeSelectionnee?->nom,
            'mois' => $mois,
            'eleves' => $eleves,
        ]);
    }
}
