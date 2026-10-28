<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatriculeExamenController extends Controller
{
    const PREFIXES_AUTORISES = ['CM2', '3ᵉ', 'Terminale A', 'Terminale C', 'Terminale D'];

    public function saisie(Classe $classe)
    {

        if (!collect(self::PREFIXES_AUTORISES)->contains(fn($p) => str_starts_with($classe->nom, $p))) {
            abort(403, "La saisie des matricules d'examen n'est disponible que pour CM2, 3e et Terminale.");
        }

        $eleves = $classe->eleves()->orderBy('nom')->get(['id', 'nom', 'prenom', 'matricule_examen']);

        return Inertia::render('MatriculesExamen', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'eleves' => $eleves,
        ]);
    }

    public function enregistrer(Request $request)
    {
        $data = $request->validate([
            'matricules' => 'required|array',
            'matricules.*.eleve_id' => 'required|exists:eleves,id',
            'matricules.*.matricule_examen' => 'nullable|string|max:255',
        ]);

        foreach ($data['matricules'] as $m) {
            \App\Models\Eleve::where('id', $m['eleve_id'])->update([
                'matricule_examen' => $m['matricule_examen'],
            ]);
        }

        return back()->with('success', 'Matricules d\'examen enregistrés.');
    }
}
