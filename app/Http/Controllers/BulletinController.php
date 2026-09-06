<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BulletinController extends Controller
{
    public function show(Eleve $eleve, Request $request)
    {
        $trimestre = (int) $request->query('trimestre', 1);
        $classe = $eleve->classe;
        if ($request->user()->role === 'primaire' && $classe->niveau->titre !== 'Primaire') {
            abort(403, "Vous n'avez pas accès à ce bulletin.");
        }
        $cle = NoteController::cleCoefficients($classe->nom);
        $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];

        $elevesClasse = $classe->eleves;

        $lignes = [];
        $sommeTotal = 0;
        $sommeCoef = 0;
        $estPrimaire = NoteController::estPrimaire($classe->nom);

        foreach ($coefficients as $matiere => $coef) {
            $notes = $eleve->notes()
                ->where('matiere', $matiere)
                ->where('trimestre', $trimestre)
                ->get();

            $d1 = $notes->firstWhere('type_evaluation', 'Devoir 1')?->note;
            $d2 = $notes->firstWhere('type_evaluation', 'Devoir 2')?->note;
            $compo = $notes->firstWhere('type_evaluation', 'Composition')?->note;

            if ($estPrimaire) {
                $valeursDisponibles = collect([$d1, $d2, $compo])->filter(fn($v) => $v !== null);
                $mc = null;
                $totalBrut = $valeursDisponibles->isNotEmpty() ? round($valeursDisponibles->sum(), 2) : null;
            } else {
                if ($d1 !== null && $d2 !== null) {
                    $mc = round(($d1 + $d2) / 2, 2);
                } elseif ($d1 !== null) {
                    $mc = round((float) $d1, 2);
                } elseif ($d2 !== null) {
                    $mc = round((float) $d2, 2);
                } else {
                    $mc = null;
                }
                $totalBrut = null;
            }
            $moyenne = $eleve->moyenne($matiere, $trimestre);
            $totalPondere = $moyenne !== null ? round($estPrimaire ? $moyenne : $moyenne * $coef, 2) : null;

            if ($totalPondere !== null) {
                $sommeTotal += $totalPondere;
                $sommeCoef += $coef;
            }

            $rang = null;
            if ($moyenne !== null) {
                $classement = $elevesClasse
                    ->map(fn($e) => ['id' => $e->id, 'moyenne' => $e->moyenne($matiere, $trimestre)])
                    ->filter(fn($m) => $m['moyenne'] !== null)
                    ->sortByDesc('moyenne')
                    ->values();

                $position = $classement->search(fn($m) => $m['id'] === $eleve->id);
                $rang = $position !== false ? $position + 1 : null;
            }

            $estPrimaire = NoteController::estPrimaire($classe->nom);

            $professeur = $estPrimaire
                ? Professeur::whereHas('classes', fn($q) => $q->where('classes.id', $classe->id))->first()
                : Professeur::where('matiere', $matiere)
                ->whereHas('classes', fn($q) => $q->where('classes.id', $classe->id))
                ->first();
            $lignes[] = [
                'matiere' => $matiere,
                'd1' => $d1,
                'd2' => $d2,
                'mc' => $mc,
                'totalBrut' => $totalBrut,
                'composition' => $compo,
                'coefficient' => $coef,
                'total' => $totalPondere,
                'moyenne' => $moyenne,
                'rang' => $rang ? $this->ordinal($rang) : '—',
                'appreciation' => $this->appreciation($moyenne),
                'professeur' => $professeur ? $professeur->nom . ' ' . $professeur->prenom : '—',
            ];
        }

        $moyenneGenerale = $sommeCoef > 0 ? round($sommeTotal / $sommeCoef, 2) : null;

        $moyennesPondereesClasse = $elevesClasse->map(function ($e) use ($coefficients, $trimestre, $estPrimaire) {
            $st = 0;
            $sc = 0;
            foreach ($coefficients as $matiere => $coef) {
                $m = $e->moyenne($matiere, $trimestre);
                if ($m !== null) {
                    $st += $estPrimaire ? $m : $m * $coef;
                    $sc += $coef;
                }
            }
            return ['id' => $e->id, 'moyenne' => $sc > 0 ? round($st / $sc, 2) : null];
        })->filter(fn($m) => $m['moyenne'] !== null)->values();

        $rangGeneral = null;
        if ($moyenneGenerale !== null) {
            $classementGeneral = $moyennesPondereesClasse->sortByDesc('moyenne')->values();
            $position = $classementGeneral->search(fn($m) => $m['id'] === $eleve->id);
            $rangGeneral = $position !== false ? $this->ordinal($position + 1) : '—';
        }

        $moyennePlusHaute = $moyennesPondereesClasse->max('moyenne');
        $moyennePlusBasse = $moyennesPondereesClasse->min('moyenne');

        $moyennesParMatiere = [];
        foreach ($coefficients as $matiere => $coef) {
            $notesMatiere = $elevesClasse
                ->map(fn($e) => $e->moyenne($matiere, $trimestre))
                ->filter(fn($m) => $m !== null);

            if ($notesMatiere->isNotEmpty()) {
                $moyennesParMatiere[] = $notesMatiere->avg();
            }
        }
        $moyenneClasse = count($moyennesParMatiere) > 0
            ? round(array_sum($moyennesParMatiere) / count($moyennesParMatiere), 2)
            : null;

        $mcValides = collect($lignes)->pluck('mc')->filter(fn($m) => $m !== null);
        $moyenneControleEleve = $mcValides->isNotEmpty() ? round($mcValides->avg(), 2) : null;

        $moyenneAnnuelle = null;
        $rangAnnuel = null;
        $moyennePlusHauteAnnuelle = null;
        $moyennePlusBasseAnnuelle = null;
        $appreciationTravailAnnuelle = null;
        $mentionAnnuelle = null;

        if ($trimestre === 3) {
            if ($estPrimaire) {
                $moyenneAnnuelle = $this->moyenneAnnuelleEleve($eleve, $coefficients);

                $moyennesAnnuellesClasse = $elevesClasse->map(function ($e) use ($coefficients) {
                    return ['id' => $e->id, 'moyenne' => $this->moyenneAnnuelleEleve($e, $coefficients)];
                })->filter(fn($m) => $m['moyenne'] !== null)->values();
            } else {
                $moyenneAnnuelle = $this->moyenneAnnuelleEleve($eleve, $coefficients);

                $moyennesAnnuellesClasse = $elevesClasse->map(function ($e) use ($coefficients) {
                    return ['id' => $e->id, 'moyenne' => $this->moyenneAnnuelleEleve($e, $coefficients)];
                })->filter(fn($m) => $m['moyenne'] !== null)->values();
            }
            if ($moyenneAnnuelle !== null) {
                $classementAnnuel = $moyennesAnnuellesClasse->sortByDesc('moyenne')->values();
                $position = $classementAnnuel->search(fn($m) => $m['id'] === $eleve->id);
                $rangAnnuel = $position !== false ? $this->ordinal($position + 1) : '—';
            }

            $moyennePlusHauteAnnuelle = $moyennesAnnuellesClasse->max('moyenne');
            $moyennePlusBasseAnnuelle = $moyennesAnnuellesClasse->min('moyenne');
            $appreciationTravailAnnuelle = $this->appreciationTravail($moyenneAnnuelle);
            $mentionAnnuelle = $this->mention($moyenneAnnuelle);
        }

        $assiduite = $eleve->assiduites()->where('trimestre', $trimestre)->first();

        return Inertia::render('Bulletins/Show', [
            'eleve' => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'sexe' => $eleve->sexe,
            ],
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'estPrimaire' => $estPrimaire,
            'anneeScolaire' => $this->anneeScolaire(),
            'lignes' => $lignes,
            'moyenneGenerale' => $moyenneGenerale,
            'moyenneAnnuelle' => $moyenneAnnuelle,
            'rangAnnuel' => $rangAnnuel,
            'moyennePlusHauteAnnuelle' => $moyennePlusHauteAnnuelle,
            'moyennePlusBasseAnnuelle' => $moyennePlusBasseAnnuelle,
            'appreciationTravailAnnuelle' => $appreciationTravailAnnuelle,
            'mentionAnnuelle' => $mentionAnnuelle,
            'moyenneControleEleve' => $moyenneControleEleve,
            'rangGeneral' => $rangGeneral,
            'rangAnnuel' => $rangAnnuel,
            'mention' => $this->mention($moyenneGenerale),
            'mentionAnnuelle' => $mentionAnnuelle,
            'appreciationTravail' => $this->appreciationTravail($moyenneGenerale),
            'appreciationTravailAnnuelle' => $appreciationTravailAnnuelle,
            'effectifClasse' => $elevesClasse->count(),
            'moyenneClasse' => $moyenneClasse,
            'moyennePlusHaute' => $moyennePlusHaute,
            'moyennePlusBasse' => $moyennePlusBasse,
            'moyennePlusHauteAnnuelle' => $moyennePlusHauteAnnuelle,
            'moyennePlusBasseAnnuelle' => $moyennePlusBasseAnnuelle,
            'assiduite' => [
                'absences_justifiees' => $assiduite->absences_justifiees ?? 0,
                'absences_injustifiees' => $assiduite->absences_injustifiees ?? 0,
                'retards_justifies' => $assiduite->retards_justifies ?? 0,
                'retards_injustifies' => $assiduite->retards_injustifies ?? 0,
                'avertissements' => $assiduite->avertissements ?? '—',
                'blame' => $assiduite->blame ?? '—',
            ],
        ]);
    }

    private function ordinal($rang)
    {
        return $rang === 1 ? '1er' : $rang . 'ème';
    }

    private function appreciation($moyenne)
    {
        if ($moyenne === null) return '—';
        if ($moyenne >= 16) return 'Très bien';
        if ($moyenne >= 14) return 'Bien';
        if ($moyenne >= 12) return 'Assez bien';
        if ($moyenne >= 10) return 'Passable';
        if ($moyenne >= 8) return 'Insuffisant';
        return 'Faible';
    }

    private function appreciationTravail($moyenne)
    {
        if ($moyenne === null) return '—';
        if ($moyenne >= 16) return 'T.BIEN';
        if ($moyenne >= 14) return 'BIEN';
        if ($moyenne >= 12) return 'ABIEN';
        if ($moyenne >= 11) return 'PASSABLE';
        if ($moyenne >= 10) return 'MOYEN';
        if ($moyenne >= 8) return 'INSUFFISANT';
        if ($moyenne >= 6) return 'FAIBLE';
        if ($moyenne >= 4) return 'MAUVAIS';
        return "Un effort s'impose";
    }
    private function mention($moyenne)
    {
        if ($moyenne === null) return '—';
        if ($moyenne >= 16) return 'Excellent';
        if ($moyenne >= 14) return 'Très bien';
        if ($moyenne >= 12) return 'Assez bien';
        if ($moyenne >= 11) return 'Passable';
        if ($moyenne >= 10) return 'Moyen';
        return 'Insuffisant';
    }

    private function anneeScolaire()
    {
        $mois = now()->month;
        $annee = now()->year;

        if ($mois >= 10) {
            return $annee . '-' . ($annee + 1);
        }

        if ($mois <= 6) {
            return ($annee - 1) . '-' . $annee;
        }

        return $annee . '-' . ($annee + 1);
    }

    private function moyenneAnnuelleEleve($eleve, $coefficients)
    {
        $estPrimaire = NoteController::estPrimaire($eleve->classe->nom);
        $sommeTrimestres = 0;
        $trimestresValides = 0;

        for ($t = 1; $t <= 3; $t++) {
            $sT = 0;
            $sC = 0;
            foreach ($coefficients as $matiere => $coef) {
                $m = $eleve->moyenne($matiere, $t);
                if ($m !== null) {
                    $sT += $estPrimaire ? $m : $m * $coef;
                    $sC += $coef;
                }
            }
            if ($sC > 0) {
                $sommeTrimestres += $sT / $sC;
                $trimestresValides++;
            }
        }

        return $trimestresValides > 0 ? round($sommeTrimestres / 3, 2) : null;
    }
}
