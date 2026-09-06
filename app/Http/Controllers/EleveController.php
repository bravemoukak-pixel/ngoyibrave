<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EleveController extends Controller
{
    public function create(Niveau $niveau)
    {
        return Inertia::render('InscriptionEleve', [
            'niveau' => [
                'id' => $niveau->id,
                'titre' => $niveau->titre,
                'montant_annuel_defaut' => $niveau->montant_annuel_defaut,
            ],
            'classes' => $niveau->classes->map(fn($c) => [
                'id' => $c->id,
                'nom' => $c->nom,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'montant_annuel' => 'nullable|numeric|min:0',
        ]);

        Eleve::create($validated);

        return redirect()->route('niveaux.index')->with('success', 'Élève inscrit avec succès.');
    }

    public function edit(Eleve $eleve)
    {
        return Inertia::render('Eleves/Edit', [
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'sexe' => $eleve->sexe,
                'date_naissance' => $eleve->date_naissance->format('Y-m-d'),
                'lieu_naissance' => $eleve->lieu_naissance,
                'adresse' => $eleve->adresse,
                'classe_id' => $eleve->classe_id,
                'montant_annuel' => $eleve->montant_annuel,
            ],
            'classes' => Classe::all(['id', 'nom']),
        ]);
    }

    public function update(Request $request, Eleve $eleve)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'montant_annuel' => 'nullable|numeric|min:0',
        ]);

        $eleve->update($validated);

        return redirect()->back()->with('success', 'Élève modifié avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        $eleve->delete();

        return redirect()->back()->with('success', 'Élève supprimé avec succès.');
    }
}
