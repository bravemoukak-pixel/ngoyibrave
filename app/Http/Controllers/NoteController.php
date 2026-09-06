<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    const TYPES = ['Devoir 1', 'Devoir 2', 'Composition'];

    const MATIERES = [
        'Français',
        'Mathématiques',
        'SVT',
        'Physique-Chimie',
        'Anglais',
        'Histoire-Géographie',
        'EPS',
        'Philosophie',
        'Espagnol',
        'FHR',
        'Orthographe',
        'Rédaction',
        'Musique',
        'Dictée',
        'Question',
        'Écriture',
        'Calcul mental',
        'Calcul écrit',
        'Lecture',
        'Questions de cours',
        'Poésie',
        'Art plastique',
    ];

    const COEFFICIENTS = [
        'college_general' => [
            'Orthographe' => 1,
            'Rédaction' => 1,
            'Mathématiques' => 1,
            'SVT' => 1,
            'Physique-Chimie' => 1,
            'Anglais' => 1,
            'Histoire-Géographie' => 1,
            'EPS' => 1,
            'FHR' => 1,
            'Musique' => 1,
        ],
        '3e' => [
            'Orthographe' => 2,
            'Rédaction' => 2,
            'Histoire-Géographie' => 2,
            'Anglais' => 2,
            'Mathématiques' => 4,
            'Physique-Chimie' => 2,
            'SVT' => 2,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'seconde_a' => [
            'Français' => 4,
            'Philosophie' => 2,
            'Histoire-Géographie' => 4,
            'Anglais' => 4,
            'Espagnol' => 3,
            'Mathématiques' => 2,
            'SVT' => 2,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'seconde_c' => [
            'Français' => 3,
            'Philosophie' => 2,
            'Histoire-Géographie' => 2,
            'Anglais' => 2,
            'Mathématiques' => 4,
            'Physique-Chimie' => 5,
            'SVT' => 4,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'premiere_a' => [
            'Français' => 4,
            'Philosophie' => 4,
            'Histoire-Géographie' => 3,
            'Anglais' => 3,
            'Espagnol' => 4,
            'Mathématiques' => 2,
            'SVT' => 2,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'premiere_cd' => [
            'Français' => 3,
            'Philosophie' => 2,
            'Histoire-Géographie' => 2,
            'Anglais' => 2,
            'Mathématiques' => 5,
            'Physique-Chimie' => 5,
            'SVT' => 5,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'terminale_a' => [
            'Français' => 3,
            'Philosophie' => 4,
            'Histoire-Géographie' => 4,
            'Anglais' => 4,
            'Espagnol' => 4,
            'Mathématiques' => 3,
            'SVT' => 2,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'terminale_c' => [
            'Français' => 3,
            'Philosophie' => 3,
            'Histoire-Géographie' => 3,
            'Anglais' => 3,
            'Mathématiques' => 5,
            'Physique-Chimie' => 5,
            'SVT' => 4,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'terminale_d' => [
            'Français' => 3,
            'Philosophie' => 3,
            'Histoire-Géographie' => 3,
            'Anglais' => 3,
            'Mathématiques' => 4,
            'Physique-Chimie' => 5,
            'SVT' => 5,
            'FHR' => 2,
            'EPS' => 2,
        ],
        'cp1' => [
            'Dictée' => 1,
            'Question' => 1,
            'Français' => 1,
            'Écriture' => 1,
            'Calcul mental' => 1,
            'Calcul écrit' => 1,
            'Lecture' => 1,
            'Questions de cours' => 1,
            'Poésie' => 1,
            'Art plastique' => 1,
            'FHR' => 1,
        ],
        'cp2' => [
            'Dictée' => 1,
            'Question' => 1,
            'Français' => 1,
            'Écriture' => 1,
            'Calcul mental' => 1,
            'Calcul écrit' => 1,
            'Lecture' => 1,
            'Questions de cours' => 1,
            'Poésie' => 1,
            'Art plastique' => 1,
            'FHR' => 1,
        ],
        'ce1' => [
            'Dictée' => 1,
            'Question' => 1,
            'Français' => 1,
            'Écriture' => 1,
            'Calcul mental' => 1,
            'Calcul écrit' => 2,
            'Lecture' => 1,
            'Questions de cours' => 1,
            'Poésie' => 1,
            'Art plastique' => 1,
            'FHR' => 1,
        ],
        'ce2' => [
            'Dictée' => 1,
            'Question' => 1,
            'Français' => 1,
            'Écriture' => 1,
            'Calcul mental' => 1,
            'Calcul écrit' => 2,
            'Lecture' => 1,
            'Questions de cours' => 1,
            'Poésie' => 1,
            'Art plastique' => 1,
            'FHR' => 1,
        ],
        'cm1' => [
            'Dictée' => 1,
            'Question' => 1,
            'Français' => 1,
            'Écriture' => 1,
            'Calcul mental' => 1,
            'Calcul écrit' => 2,
            'Lecture' => 1,
            'Questions de cours' => 2,
            'Poésie' => 1,
            'Art plastique' => 1,
            'FHR' => 1,
        ],
        'cm2' => [
            'Dictée' => 2,
            'Question' => 2,
            'Français' => 2,
            'Écriture' => 2,
            'Calcul mental' => 2,
            'Calcul écrit' => 2,
            'Lecture' => 2,
            'Questions de cours' => 2,
            'Poésie' => 2,
            'Art plastique' => 2,
            'FHR' => 2,
        ],
    ];
    public static function cleCoefficients(string $nomClasse): string
    {
        $nom = mb_strtolower(trim($nomClasse));
        $nom = str_replace(['é', 'è', 'ê', 'ᵉ'], 'e', $nom);

        // Retire un éventuel suffixe de groupe (ex: "6e 1" -> "6e", "seconde a 2" -> "seconde a")
        $nomBase = preg_replace('/\s+\S+$/', '', $nom);

        foreach ([$nom, $nomBase] as $candidat) {
            if (in_array($candidat, ['6e', '5e', '4e'])) {
                return 'college_general';
            }

            if (in_array($candidat, ['premiere c', 'premiere d', 'premiere s',])) {
                return 'premiere_cd';
            }

            $cle = str_replace(' ', '_', $candidat);
            if (array_key_exists($cle, self::COEFFICIENTS)) {
                return $cle;
            }
        }

        return str_replace(' ', '_', $nom);
    }

    public function saisie(Classe $classe, Request $request)
    {
        if ($request->user()->role === 'primaire' && $classe->niveau->titre !== 'Primaire') {
            abort(403, "Vous n'avez pas accès à cette classe.");
        }

        $matiere = $request->query('matiere', self::MATIERES[0]);
        $matiere = $request->query('matiere', self::MATIERES[0]);
        $trimestre = (int) $request->query('trimestre', 1);

        $eleves = $classe->eleves()->orderBy('nom')->get();

        $notesExistantes = Note::whereIn('eleve_id', $eleves->pluck('id'))
            ->where('matiere', $matiere)
            ->where('trimestre', $trimestre)
            ->get()
            ->groupBy('eleve_id');

        $donnees = $eleves->map(function ($eleve) use ($notesExistantes) {
            $notesEleve = $notesExistantes->get($eleve->id, collect());

            $parType = [];
            foreach (self::TYPES as $type) {
                $note = $notesEleve->firstWhere('type_evaluation', $type);
                $parType[$type] = $note ? $note->note : null;
            }

            return [
                'eleve_id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'notes' => $parType,
            ];
        });

        return Inertia::render('Notes/Saisie', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'matiere' => $matiere,
            'trimestre' => $trimestre,
            'matieres' => self::MATIERES,
            'types' => self::TYPES,
            'donnees' => $donnees,
        ]);
    }

    public function enregistrer(Request $request)
    {
        $data = $request->validate([
            'matiere' => 'required|string',
            'trimestre' => 'required|integer|between:1,3',
            'notes' => 'required|array',
            'notes.*.eleve_id' => 'required|exists:eleves,id',
            'notes.*.type_evaluation' => 'required|string',
            'notes.*.note' => 'nullable|numeric|min:0|max:20',
        ]);

        foreach ($data['notes'] as $n) {
            if ($n['note'] === null) {
                Note::where('eleve_id', $n['eleve_id'])
                    ->where('matiere', $data['matiere'])
                    ->where('trimestre', $data['trimestre'])
                    ->where('type_evaluation', $n['type_evaluation'])
                    ->delete();
                continue;
            }

            Note::updateOrCreate(
                [
                    'eleve_id' => $n['eleve_id'],
                    'matiere' => $data['matiere'],
                    'trimestre' => $data['trimestre'],
                    'type_evaluation' => $n['type_evaluation'],
                ],
                ['note' => $n['note']]
            );
        }

        return back()->with('success', 'Notes enregistrées.');
    }
    public static function estPrimaire(string $nomClasse): bool
    {
        $cle = self::cleCoefficients($nomClasse);
        return in_array($cle, ['cp1', 'cp2', 'ce1', 'ce2', 'cm1', 'cm2']);
    }

    public static function libellesEvaluation(string $nomClasse): array
    {
        if (self::estPrimaire($nomClasse)) {
            return [
                'Devoir 1' => 'Comp. 1',
                'Devoir 2' => 'Comp. 2',
                'Composition' => 'Composition',
            ];
        }

        return [
            'Devoir 1' => 'Devoir 1',
            'Devoir 2' => 'Devoir 2',
            'Composition' => 'Composition',
        ];
    }
}
