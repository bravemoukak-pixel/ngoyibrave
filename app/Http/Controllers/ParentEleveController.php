<?php

namespace App\Http\Controllers;

use App\Models\ParentEleve;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParentEleveController extends Controller
{
    public function index()
    {
        $parents = ParentEleve::with('eleves')->orderBy('nom')->get()->map(fn($p) => [
            'id' => $p->id,
            'nom' => $p->nom,
            'prenom' => $p->prenom,
            'fonction' => $p->fonction,
            'lien_parente' => $p->lien_parente,
            'telephone' => $p->telephone,
            'eleves' => $p->eleves->map(fn($e) => $e->nom . ' ' . $e->prenom),
        ]);

        return Inertia::render('Parents/Index', [
            'parents' => $parents,
        ]);
    }

    public function create()
    {
        return Inertia::render('Parents/Create', [
            'eleves' => Eleve::orderBy('nom')->get(['id', 'nom', 'prenom']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'lien_parente' => 'required|string|max:50',
            'telephone' => 'required|string|max:20',
            'eleves' => 'array',
            'eleves.*' => 'exists:eleves,id',
        ]);

        $parent = ParentEleve::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'fonction' => $validated['fonction'],
            'lien_parente' => $validated['lien_parente'],
            'telephone' => $validated['telephone'],
        ]);

        if (!empty($validated['eleves'])) {
            Eleve::whereIn('id', $validated['eleves'])->update(['parent_eleve_id' => $parent->id]);
        }

        return redirect()->route('parents.index')->with('success', 'Parent ajouté avec succès.');
    }

    public function edit(ParentEleve $parent)
    {
        return Inertia::render('Parents/Edit', [
            'parent' => [
                'id' => $parent->id,
                'nom' => $parent->nom,
                'prenom' => $parent->prenom,
                'fonction' => $parent->fonction,
                'lien_parente' => $parent->lien_parente,
                'telephone' => $parent->telephone,
                'eleves' => $parent->eleves->pluck('id'),
            ],
            'eleves' => Eleve::orderBy('nom')->get(['id', 'nom', 'prenom']),
        ]);
    }

    public function update(Request $request, ParentEleve $parent)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'lien_parente' => 'required|string|max:50',
            'telephone' => 'required|string|max:20',
            'eleves' => 'array',
            'eleves.*' => 'exists:eleves,id',
        ]);

        $parent->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'fonction' => $validated['fonction'],
            'lien_parente' => $validated['lien_parente'],
            'telephone' => $validated['telephone'],
        ]);

        // Détache les élèves qui ne sont plus sélectionnés
        Eleve::where('parent_eleve_id', $parent->id)->update(['parent_eleve_id' => null]);

        // Rattache les élèves sélectionnés
        if (!empty($validated['eleves'])) {
            Eleve::whereIn('id', $validated['eleves'])->update(['parent_eleve_id' => $parent->id]);
        }

        return redirect()->route('parents.index')->with('success', 'Parent modifié avec succès.');
    }

    public function destroy(ParentEleve $parent)
    {
        $parent->delete();

        return redirect()->back()->with('success', 'Parent supprimé avec succès.');
    }

    public function paiements()
    {
        return $this->hasMany(PaiementScolarite::class, 'parent_eleve_id');
    }
    public function recu(PaiementScolarite $paiement)
    {
        $paiement->load('parentEleve.eleves', 'parentEleve.paiements');

        $totalDu = $paiement->parentEleve->eleves->sum('montant_annuel');
        $totalPaye = $paiement->parentEleve->paiements->sum('montant');

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
        ]);
    }
}
