<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Http\Controllers\NoteController;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuiviNotesController extends Controller
{
    public function index(Request $request)
    {
        $eleveId = $request->query('eleve_id');
        $trimestre = (int) $request->query('trimestre', 1);
        $utilisateur = $request->user();

        $elevesQuery = Eleve::orderBy('nom');
        if ($utilisateur->role === 'primaire') {
            $elevesQuery->whereHas('classe.niveau', fn($q) => $q->where('titre', 'Primaire'));
        }
        $eleves = $elevesQuery->get(['id', 'nom', 'prenom']);

        $suivi = null;

        $estPrimaire = false;

        if ($eleveId) {
            $suivi = $this->genererSuivi($eleveId, $trimestre);
            $eleveVerif = Eleve::with('classe.niveau')->findOrFail($eleveId);
            if ($utilisateur->role === 'primaire' && $eleveVerif->classe->niveau->titre !== 'Primaire') {
                abort(403);
            }
            $estPrimaire = NoteController::estPrimaire($suivi['eleve']['classe']);
        }
        return Inertia::render('SuiviNotes/Index', [
            'eleves' => $eleves,
            'eleveId' => $eleveId,
            'trimestre' => $trimestre,
            'suivi' => $suivi,
            'estPrimaire' => $estPrimaire, // nouvelle ligne
        ]);
    }

    private function genererSuivi($eleveId, $trimestre)
    {
        $eleve = Eleve::with('classe')->findOrFail($eleveId);
        $cle = NoteController::cleCoefficients($eleve->classe->nom);
        $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];
        $estPrimaire = NoteController::estPrimaire($eleve->classe->nom);

        $lignes = [];
        $sommeCoefD1 = 0;
        $sommeD1 = 0;
        $sommeCoefD2 = 0;
        $sommeD2 = 0;
        $sommeCoefCompo = 0;
        $sommeCompo = 0;

        foreach ($coefficients as $matiere => $coef) {
            $notes = $eleve->notes()
                ->where('matiere', $matiere)
                ->where('trimestre', $trimestre)
                ->get();

            $d1 = $notes->firstWhere('type_evaluation', 'Devoir 1')?->note;
            $d2 = $notes->firstWhere('type_evaluation', 'Devoir 2')?->note;
            $compo = $notes->firstWhere('type_evaluation', 'Composition')?->note;

            if ($d1 !== null && $coef > 0) {
                $sommeD1 += $estPrimaire ? $d1 : $d1 * $coef;
                $sommeCoefD1 += $coef;
            }
            if ($d2 !== null && $coef > 0) {
                $sommeD2 += $estPrimaire ? $d2 : $d2 * $coef;
                $sommeCoefD2 += $coef;
            }
            if ($compo !== null && $coef > 0) {
                $sommeCompo += $estPrimaire ? $compo : $compo * $coef;
                $sommeCoefCompo += $coef;
            }

            $lignes[] = [
                'matiere' => $matiere,
                'coef' => $coef,
                'd1' => $d1,
                'd2' => $d2,
                'composition' => $compo,
            ];
        }

        return [
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'classe' => $eleve->classe->nom,
            ],
            'trimestre' => $trimestre,
            'lignes' => $lignes,
            'moyenneD1' => $sommeCoefD1 > 0 ? round($sommeD1 / $sommeCoefD1, 2) : null,
            'moyenneD2' => $sommeCoefD2 > 0 ? round($sommeD2 / $sommeCoefD2, 2) : null,
            'moyenneCompo' => $sommeCoefCompo > 0 ? round($sommeCompo / $sommeCoefCompo, 2) : null,
        ];
    }

    public function classe(Request $request)
    {
        $classeId = $request->query('classe_id');
        $trimestre = (int) $request->query('trimestre', 1);
        $utilisateur = $request->user();

        $classes = \App\Models\Classe::with('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau_id']);
        if ($utilisateur->role === 'primaire') {
            $classes = $classes->filter(fn($c) => $c->niveau->titre === 'Primaire')->values();
        }

        $eleves = null;
        $matieres = [];
        $coefficients = [];
        $classeSelectionnee = null;

        if ($classeId) {
            $classeSelectionnee = \App\Models\Classe::with('niveau')->findOrFail($classeId);

            if ($utilisateur->role === 'primaire' && $classeSelectionnee->niveau->titre !== 'Primaire') {
                abort(403);
            }
            $cle = NoteController::cleCoefficients($classeSelectionnee->nom);
            $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];
            $matieres = array_keys($coefficients);

            $eleves = $classeSelectionnee->eleves()->orderBy('nom')->get()->map(function ($eleve) use ($trimestre) {
                $suivi = $this->genererSuivi($eleve->id, $trimestre);

                $notesD1 = [];
                $notesD2 = [];
                $notesCompo = [];
                foreach ($suivi['lignes'] as $ligne) {
                    $notesD1[$ligne['matiere']] = $ligne['d1'];
                    $notesD2[$ligne['matiere']] = $ligne['d2'];
                    $notesCompo[$ligne['matiere']] = $ligne['composition'];
                }

                return [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'notesD1' => $notesD1,
                    'notesD2' => $notesD2,
                    'notesCompo' => $notesCompo,
                    'moyenneD1' => $suivi['moyenneD1'],
                    'moyenneD2' => $suivi['moyenneD2'],
                    'moyenneCompo' => $suivi['moyenneCompo'],
                ];
            });
        }

        return Inertia::render('SuiviNotes/Classe', [
            'classes' => $classes,
            'classeId' => $classeId,
            'classeNom' => $classeSelectionnee?->nom,
            'trimestre' => $trimestre,
            'matieres' => $matieres,
            'coefficients' => $coefficients,
            'eleves' => $eleves,
            'estPrimaire' => $classeSelectionnee ? \App\Http\Controllers\NoteController::estPrimaire($classeSelectionnee->nom) : false,
        ]);
    }
}
