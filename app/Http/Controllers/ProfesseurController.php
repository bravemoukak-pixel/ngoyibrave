<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfesseurController extends Controller
{
    public function index()
    {
        $professeurs = Professeur::with('classes.niveau')->orderBy('nom')->get();

        $estCollege = fn($prof) => $prof->classes->contains(fn($c) => $c->niveau->titre === 'Collège');
        $estLycee = fn($prof) => $prof->classes->contains(fn($c) => str_starts_with($c->niveau->titre, 'Lycée'));
        $estPrimaire = fn($prof) => $prof->classes->contains(fn($c) => $c->niveau->titre === 'Primaire');

        $formatter = fn($prof) => [
            'id' => $prof->id,
            'nom' => $prof->nom,
            'prenom' => $prof->prenom,
            'sexe' => $prof->sexe,
            'date_naissance' => $prof->date_naissance?->format('d/m/Y'),
            'lieu_naissance' => $prof->lieu_naissance,
            'matiere' => $prof->matiere,
            'telephone' => $prof->telephone,
            'email' => $prof->email,
            'classes' => $prof->classes->pluck('nom'),
        ];

        return Inertia::render('Professeurs/Index', [
            'professeursCollege' => $professeurs->filter($estCollege)->map($formatter)->values(),
            'professeursLycee' => $professeurs->filter($estLycee)->map($formatter)->values(),
            'professeursPrimaire' => $professeurs->filter($estPrimaire)->map($formatter)->values(),
        ]);
    }
    public function create()
    {
        return Inertia::render('Professeurs/Create', [
            'classes' => Classe::all(['id', 'nom']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'matiere' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'classes' => 'array',
            'classes.*' => 'exists:classes,id',
        ]);

        $professeur = Professeur::create($validated);

        if (!empty($validated['classes'])) {
            $professeur->classes()->sync($validated['classes']);
        }

        return redirect()->route('professeurs.index')->with('success', 'Professeur ajouté avec succès.');
    }

    public function edit(Professeur $professeur)
    {
        return Inertia::render('Professeurs/Edit', [
            'professeur' => [
                'id' => $professeur->id,
                'nom' => $professeur->nom,
                'prenom' => $professeur->prenom,
                'sexe' => $professeur->sexe,
                'date_naissance' => $professeur->date_naissance?->format('Y-m-d'),
                'lieu_naissance' => $professeur->lieu_naissance,
                'matiere' => $professeur->matiere,
                'telephone' => $professeur->telephone,
                'email' => $professeur->email,
                'pin' => $professeur->pin,
                'classes' => $professeur->classes->pluck('id'),
            ],
            'classes' => Classe::all(['id', 'nom']),
        ]);
    }
    public function regenererPin(Professeur $professeur)
    {
        $professeur->update([
            'pin' => str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
        ]);

        return redirect()->back()->with('success', 'Nouveau PIN généré : ' . $professeur->pin);
    }

    public function update(Request $request, Professeur $professeur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'matiere' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'classes' => 'array',
            'classes.*' => 'exists:classes,id',
        ]);

        $professeur->update($validated);
        $professeur->classes()->sync($validated['classes'] ?? []);

        return redirect()->route('professeurs.index')->with('success', 'Professeur modifié avec succès.');
    }

    public function destroy(Professeur $professeur)
    {
        $professeur->delete();

        return redirect()->back()->with('success', 'Professeur supprimé avec succès.');
    }
}
