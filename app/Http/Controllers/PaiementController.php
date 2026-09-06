<?php

namespace App\Http\Controllers;

use App\Models\ParentEleve;
use App\Models\PaiementScolarite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaiementController extends Controller
{
    public function index()
    {
        $parents = ParentEleve::with('eleves', 'paiements')->orderBy('nom')->get()->map(function ($parent) {
            $totalDu = $parent->montant_annuel_override ?? $parent->eleves->sum('montant_annuel');
            $totalPaye = $parent->paiements->sum('montant');

            return [
                'id' => $parent->id,
                'nom' => $parent->nom,
                'prenom' => $parent->prenom,
                'telephone' => $parent->telephone,
                'eleves' => $parent->eleves->map(fn($e) => $e->nom . ' ' . $e->prenom),
                'totalDu' => $totalDu,
                'totalPaye' => $totalPaye,
                'reste' => $totalDu - $totalPaye,
            ];
        });

        return Inertia::render('Paiements/Index', [
            'parents' => $parents,
        ]);
    }

    public function show(ParentEleve $parent)
    {
        $parent->load('eleves', 'paiements');

        $totalDu = $parent->montant_annuel_override ?? $parent->eleves->sum('montant_annuel');
        $totalPaye = $parent->paiements->sum('montant');

        return Inertia::render('Paiements/Show', [
            'parent' => [
                'id' => $parent->id,
                'nom' => $parent->nom,
                'prenom' => $parent->prenom,
                'telephone' => $parent->telephone,
                'eleves' => $parent->eleves->map(fn($e) => [
                    'nom' => $e->nom . ' ' . $e->prenom,
                    'montant_annuel' => $e->montant_annuel,
                ]),
            ],
            'paiements' => $parent->paiements->sortByDesc('mois')->values(),
            'totalDu' => $totalDu,
            'totalPaye' => $totalPaye,
            'reste' => $totalDu - $totalPaye,
        ]);
    }

    public function ajouterPaiement(Request $request)
    {
        $data = $request->validate([
            'parent_eleve_id' => 'required|exists:parent_eleves,id',
            'mois' => 'required',
            'montant' => 'required|numeric|min:0',
        ]);

        PaiementScolarite::create([
            'parent_eleve_id' => $data['parent_eleve_id'],
            'mois' => $data['mois'] . '-01',
            'montant' => $data['montant'],
        ]);

        return back()->with('success', 'Paiement enregistré avec succès.');
    }

    public function supprimerPaiement(PaiementScolarite $paiement)
    {
        $paiement->delete();

        return back()->with('success', 'Paiement supprimé avec succès.');
    }
    public function recu(PaiementScolarite $paiement)
    {
        $paiement->load('parentEleve.eleves', 'parentEleve.paiements');

        $totalDu = $paiement->parentEleve->montant_annuel_override ?? $paiement->parentEleve->eleves->sum('montant_annuel');
        $totalPaye = $paiement->parentEleve->paiements->sum('montant');
        $parametres = \App\Models\Parametre::first();

        return Inertia::render('Paiements/Recu', [
            'paiement' => [
                'id' => $paiement->id,
                'mois' => $paiement->mois,
                'montant' => $paiement->montant,
                'date_enregistrement' => $paiement->created_at,
            ],
            'parent' => [
                'nom' => $paiement->parentEleve->nom,
                'prenom' => $paiement->parentEleve->prenom,
                'telephone' => $paiement->parentEleve->telephone,
                'eleves' => $paiement->parentEleve->eleves->map(fn($e) => $e->nom . ' ' . $e->prenom),
            ],
            'totalDu' => $totalDu,
            'totalPaye' => $totalPaye,
            'reste' => $totalDu - $totalPaye,
            'telephonesEcole' => array_filter([$parametres->telephone_ecole_1, $parametres->telephone_ecole_2]),
        ]);
    }
}
