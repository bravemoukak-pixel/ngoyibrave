<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReleveViergeController extends Controller
{
    public function index(Classe $classe, Request $request)
    {
        if (NoteController::estPrimaire($classe->nom)) {
            abort(403, "Ce relevé n'est disponible que pour le collège et le lycée.");
        }

        $matiere = $request->query('matiere', NoteController::MATIERES[0]);
        $trimestre = (int) $request->query('trimestre', 1);
        $typeEvaluation = $request->query('type_evaluation', NoteController::TYPES[0]);

        $eleves = $classe->eleves()->orderBy('nom')->get(['id', 'matricule', 'nom', 'prenom', 'sexe']);

      $professeur = \App\Models\Professeur::where('matiere', 'like', '%' . $matiere . '%')
    ->whereHas('classes', fn($q) => $q->where('classes.id', $classe->id))
    ->first();
        return Inertia::render('ReleveVierge', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'matiere' => $matiere,
            'trimestre' => $trimestre,
            'typeEvaluation' => $typeEvaluation,
            'matieres' => NoteController::MATIERES,
            'types' => NoteController::TYPES,
            'eleves' => $eleves,
            'professeur' => $professeur ? $professeur->nom . ' ' . $professeur->prenom : null,
        ]);
    }
}
