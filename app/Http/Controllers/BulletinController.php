<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BulletinArchive;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class BulletinController extends Controller
{
    public function show(Eleve $eleve, Request $request)
    {
        $trimestre = (int) $request->query('trimestre', 1);
        $anneeScolaireActuelle = $this->anneeScolaire();
        $anneeScolaire = $request->query('annee_scolaire', $anneeScolaireActuelle);
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
        $estLycee = str_starts_with($classe->niveau->titre, 'Lycée');

        foreach ($coefficients as $matiere => $coef) {
            $notes = $eleve->notes()
                ->where('matiere', $matiere)
                ->where('trimestre', $trimestre)
                ->where('annee_scolaire', $anneeScolaire)
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
                $totalBrut = ($mc !== null && $compo !== null) ? round($mc + $compo, 2) : null;
            }
            $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
            $totalPondere = $moyenne !== null ? round($estPrimaire ? $moyenne : $moyenne * $coef, 2) : null;

            if ($totalPondere !== null) {
                $sommeTotal += $totalPondere;
                $sommeCoef += $coef;
            }

            $rang = null;
            if ($moyenne !== null) {
                $classement = $elevesClasse
                    ->map(fn($e) => ['id' => $e->id, 'moyenne' => $e->moyenne($matiere, $trimestre, $anneeScolaire)])
                    ->filter(fn($m) => $m['moyenne'] !== null)
                    ->sortByDesc('moyenne')
                    ->values();

                $position = $classement->search(fn($m) => $m['id'] === $eleve->id);
                $rang = $position !== false ? $position + 1 : null;
            }

            $estPrimaire = NoteController::estPrimaire($classe->nom);

            $professeur = $estPrimaire
                ? Professeur::whereHas('classes', fn($q) => $q->where('classes.id', $classe->id))->first()
                : Professeur::where(function ($q) use ($matiere) {
                    $q->where('matiere', 'like', '%' . $matiere . '%');
                })
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
                'rang' => $rang ? $this->ordinal($rang, $eleve->sexe) : '—',
                'appreciation' => $this->appreciation($moyenne),
                'professeur' => $professeur ? $professeur->nom . ' ' . $professeur->prenom : '—',
            ];
        }

        $moyenneGenerale = $sommeCoef > 0 ? round($sommeTotal / $sommeCoef, 2) : null;

        $moyennesPondereesClasse = $elevesClasse->map(function ($e) use ($coefficients, $trimestre, $estPrimaire, $anneeScolaire) {
            $st = 0;
            $sc = 0;
            foreach ($coefficients as $matiere => $coef) {
                $m = $e->moyenne($matiere, $trimestre, $anneeScolaire);
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
            $rangGeneral = $position !== false ? $this->ordinal($position + 1, $eleve->sexe) : '—';
        }

        $moyennePlusHaute = $moyennesPondereesClasse->max('moyenne');
        $moyennePlusBasse = $moyennesPondereesClasse->min('moyenne');

        $moyennesParMatiere = [];
        foreach ($coefficients as $matiere => $coef) {
            $notesMatiere = $elevesClasse
                ->map(fn($e) => $e->moyenne($matiere, $trimestre, $anneeScolaire))
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
            $moyenneAnnuelle = $this->moyenneAnnuelleEleve($eleve, $coefficients, $anneeScolaire);

            $moyennesAnnuellesClasse = $elevesClasse->map(function ($e) use ($coefficients, $anneeScolaire) {
                return ['id' => $e->id, 'moyenne' => $this->moyenneAnnuelleEleve($e, $coefficients, $anneeScolaire)];
            })->filter(fn($m) => $m['moyenne'] !== null)->values();

            if ($moyenneAnnuelle !== null) {
                $classementAnnuel = $moyennesAnnuellesClasse->sortByDesc('moyenne')->values();
                $position = $classementAnnuel->search(fn($m) => $m['id'] === $eleve->id);
                $rangAnnuel = $position !== false ? $this->ordinal($position + 1, $eleve->sexe) : '—';
            }

            $moyennePlusHauteAnnuelle = $moyennesAnnuellesClasse->max('moyenne');
            $moyennePlusBasseAnnuelle = $moyennesAnnuellesClasse->min('moyenne');
            $appreciationTravailAnnuelle = $this->appreciationTravail($moyenneAnnuelle);
            $mentionAnnuelle = $this->mention($moyenneAnnuelle);
        }

        $assiduite = $eleve->assiduites()->where('trimestre', $trimestre)->where('annee_scolaire', $anneeScolaire)->first();
        $trimestreEnLettres = ['1' => 'premier', '2' => 'deuxième', '3' => 'troisième'][$trimestre] ?? $trimestre;

        $decisionAnnuelle = null;
        if ($trimestre === 3 && $moyenneAnnuelle !== null) {
            $decisionAnnuelle = $moyenneAnnuelle >= 10 ? 'PASSE EN CLASSE SUPÉRIEURE' : 'REDOUBLE LA CLASSE';
        }

        $donneesPdf = [
            'eleve' => $eleve,
            'classe' => $classe,
            'estPrimaire' => $estPrimaire,
            'trimestre' => $trimestre,
            'trimestreEnLettres' => $trimestreEnLettres,
            'anneeScolaire' => $anneeScolaireActuelle,
            'lignes' => $lignes,
            'totalMC' => collect($lignes)->sum('mc'),
            'totalCoef' => collect($lignes)->sum('coefficient'),
            'totalTotal' => collect($lignes)->sum('totalBrut'),
            'moyenneGenerale' => $moyenneGenerale,
            'moyenneAnnuelle' => $moyenneAnnuelle,
            'moyenneControleEleve' => $moyenneControleEleve,
            'moyennePlusHaute' => $moyennePlusHaute,
            'moyennePlusBasse' => $moyennePlusBasse,
            'effectifClasse' => $elevesClasse->count(),
            'appreciationTravail' => $this->appreciationTravail($moyenneGenerale),
            'mention' => $this->mention($moyenneGenerale),
            'rangAnnuel' => $rangAnnuel,
            'moyennePlusHauteAnnuelle' => $moyennePlusHauteAnnuelle,
            'moyennePlusBasseAnnuelle' => $moyennePlusBasseAnnuelle,
            'mentionAnnuelle' => $mentionAnnuelle,
            'decisionAnnuelle' => $decisionAnnuelle,
            'assiduite' => [
                'absences_justifiees' => $assiduite->absences_justifiees ?? 0,
                'absences_injustifiees' => $assiduite->absences_injustifiees ?? 0,
                'retards_justifies' => $assiduite->retards_justifies ?? 0,
                'retards_injustifies' => $assiduite->retards_injustifies ?? 0,
                'avertissements' => $assiduite->avertissements ?? '—',
                'blame' => $assiduite->blame ?? '—',
            ],
        ];

        $archive = $this->genererOuRecupererArchive($eleve, $trimestre, $anneeScolaire, $donneesPdf);

        return Inertia::render('Bulletins/Show', [
            'eleve' => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'sexe' => $eleve->sexe,
                'date_naissance' => $eleve->date_naissance?->format('d/m/Y'),
                'lieu_naissance' => $eleve->lieu_naissance,
            ],
            'dateSignature' => $archive->date_signature,
            'urlArchive' => Storage::url($archive->chemin_fichier),
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'trimestre' => $trimestre,
            'estPrimaire' => $estPrimaire,
            'estLycee' => $estLycee,
            'anneeScolaire' => $anneeScolaire,
            'anneesDisponibles' => $this->anneesDisponibles($eleve),
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
            'mention' => $this->mention($moyenneGenerale),
            'appreciationTravail' => $this->appreciationTravail($moyenneGenerale),
            'effectifClasse' => $elevesClasse->count(),
            'moyenneClasse' => $moyenneClasse,
            'moyennePlusHaute' => $moyennePlusHaute,
            'moyennePlusBasse' => $moyennePlusBasse,
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

    private function ordinal($rang, $sexe = null)
    {
        if ($rang === 1) {
            return $sexe === 'F' ? '1ère' : '1er';
        }
        return $rang . 'ème';
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
    private function genererOuRecupererArchive(Eleve $eleve, int $trimestre, string $anneeScolaire, array $donnees): BulletinArchive
    {
        $archive = BulletinArchive::where('eleve_id', $eleve->id)
            ->where('trimestre', $trimestre)
            ->where('annee_scolaire', $anneeScolaire)
            ->first();

        if ($archive) {
            return $archive;
        }

        $dateSignature = now()->format('d/m/Y');

        $pdf = Pdf::loadView('pdf.bulletin', array_merge($donnees, [
            'dateSignature' => $dateSignature,
        ]));

        $nomFichier = 'bulletins/' . $eleve->id . '_T' . $trimestre . '_' . str_replace('/', '-', $anneeScolaire) . '_' . time() . '.pdf';
        Storage::disk('public')->put($nomFichier, $pdf->output());

        return BulletinArchive::create([
            'eleve_id' => $eleve->id,
            'trimestre' => $trimestre,
            'annee_scolaire' => $anneeScolaire,
            'chemin_fichier' => $nomFichier,
            'date_signature' => $dateSignature,
        ]);
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
    private function anneesDisponibles(Eleve $eleve): array
    {
        $annees = $eleve->notes()->distinct()->pluck('annee_scolaire')->filter()->values()->toArray();
        $actuelle = $this->anneeScolaire();

        if (!in_array($actuelle, $annees)) {
            $annees[] = $actuelle;
        }

        rsort($annees);

        return $annees;
    }

    private function moyenneAnnuelleEleve($eleve, $coefficients, $anneeScolaire)
    {
        $estPrimaire = NoteController::estPrimaire($eleve->classe->nom);
        $sommeTrimestres = 0;
        $trimestresValides = 0;

        for ($t = 1; $t <= 3; $t++) {
            $sT = 0;
            $sC = 0;
            foreach ($coefficients as $matiere => $coef) {
                $m = $eleve->moyenne($matiere, $t, $anneeScolaire);
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
