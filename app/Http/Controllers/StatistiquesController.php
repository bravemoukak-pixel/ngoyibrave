<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatistiquesController extends Controller
{

    public function college(Request $request)
    {
        $trimestre = (int) $request->query('trimestre', 1);
        $anneeScolaire = $request->query('annee_scolaire', $this->anneeScolaireActuelle());

        $nomsClasses = ['6ᵉ 1', '6ᵉ 2', '5ᵉ', '4ᵉ', '3ᵉ'];

        $classes = Classe::whereIn('nom', $nomsClasses)->get()->keyBy('nom');

        $lignes = [];
        foreach ($nomsClasses as $index => $nom) {
            $classe = $classes->get($nom);
            if (!$classe) continue;

            $estDerniereClasse = $index === count($nomsClasses) - 1;
            $libelle = ($estDerniereClasse && $trimestre === 3) ? 'BEPC BLANC' : strtoupper($nom);

            $lignes[] = $this->calculerLigne($classe, $trimestre, $anneeScolaire, $libelle);
        }

        $totaux = $this->calculerTotaux($lignes);

        $groupesMatieres = [
            ['Orthographe', 'Rédaction'],
            ['Mathématiques', 'Anglais'],
            ['SVT', 'Physique-Chimie'],
            ['Histoire-Géo', 'EPS'],
            ['FHR', 'Musique', 'ECMP'],
        ];

        $tableauxMatieres = [];
        foreach ($groupesMatieres as $groupe) {
            $tableauxMatieres[] = $this->calculerTableauMatieres($groupe, $nomsClasses, $classes, $trimestre, $anneeScolaire);
        }

        $tableauAdmisRedoublants = $this->calculerTableauAdmisRedoublants($nomsClasses, $classes, $trimestre, $anneeScolaire);
        $tableauCompare = $this->calculerTableauCompare($nomsClasses, $classes, $anneeScolaire);

        return Inertia::render('Statistiques/College', [
            'trimestre' => $trimestre,
            'anneeScolaire' => $anneeScolaire,
            'lignes' => $lignes,
            'totaux' => $totaux,
            'tableauxMatieres' => $tableauxMatieres,
            'tableauAdmisRedoublants' => $tableauAdmisRedoublants,
            'tableauCompare' => $tableauCompare,
        ]);
    }
    private function calculerLigne(Classe $classe, int $trimestre, string $anneeScolaire, string $libelle): array
    {
        $eleves = $classe->eleves;
        $cle = NoteController::cleCoefficients($classe->nom);
        $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];

        $inscritsT = $eleves->count();
        $inscritsF = $eleves->where('sexe', 'F')->count();

        $presentsT = 0;
        $presentsF = 0;
        $admisT = 0;
        $admisF = 0;
        $echouesT = 0;
        $echouesF = 0;

        foreach ($eleves as $eleve) {
            $aUneNote = $eleve->notes()
                ->where('trimestre', $trimestre)
                ->where('annee_scolaire', $anneeScolaire)
                ->exists();

            if (!$aUneNote) continue;

            $presentsT++;
            if ($eleve->sexe === 'F') $presentsF++;

            $sommeTotal = 0;
            $sommeCoef = 0;
            foreach ($coefficients as $matiere => $coef) {
                $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
                if ($moyenne !== null) {
                    $sommeTotal += $moyenne * $coef;
                    $sommeCoef += $coef;
                }
            }

            $moyenneGenerale = $sommeCoef > 0 ? $sommeTotal / $sommeCoef : null;

            if ($moyenneGenerale !== null && $moyenneGenerale >= 10) {
                $admisT++;
                if ($eleve->sexe === 'F') $admisF++;
            } elseif ($moyenneGenerale !== null) {
                $echouesT++;
                if ($eleve->sexe === 'F') $echouesF++;
            }
        }

        return [
            'libelle' => $libelle,
            'inscrits' => ['t' => $inscritsT, 'df' => $inscritsF],
            'presents' => ['t' => $presentsT, 'df' => $presentsF],
            'admis' => ['t' => $admisT, 'df' => $admisF],
            'echoues' => ['t' => $echouesT, 'df' => $echouesF],
        ];
    }

    private function calculerTotaux(array $lignes): array
    {
        $totaux = [
            'inscrits' => ['t' => 0, 'df' => 0],
            'presents' => ['t' => 0, 'df' => 0],
            'admis' => ['t' => 0, 'df' => 0],
            'echoues' => ['t' => 0, 'df' => 0],
        ];

        foreach ($lignes as $ligne) {
            foreach (['inscrits', 'presents', 'admis', 'echoues'] as $cle) {
                $totaux[$cle]['t'] += $ligne[$cle]['t'];
                $totaux[$cle]['df'] += $ligne[$cle]['df'];
            }
        }

        return $totaux;
    }

    private function calculerTableauMatieres(array $matieres, array $nomsClasses, $classes, int $trimestre, string $anneeScolaire): array
    {
        $lignesParMatiere = [];

        foreach ($nomsClasses as $index => $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            $estDerniereClasse = $index === count($nomsClasses) - 1;
            $libelle = ($estDerniereClasse && $trimestre === 3) ? 'BEPC BLANC' : strtoupper($nomClasse);

            $eleves = $classe->eleves;
            $donneesMatieres = [];

            foreach ($matieres as $matiere) {
                $admisT = 0;
                $admisF = 0;
                $echouesT = 0;
                $echouesF = 0;

                foreach ($eleves as $eleve) {
                    $aUneNote = $eleve->notes()
                        ->where('trimestre', $trimestre)
                        ->where('annee_scolaire', $anneeScolaire)
                        ->exists();

                    if (!$aUneNote) continue;

                    $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
                    if ($moyenne === null) continue;

                    if ($moyenne >= 10) {
                        $admisT++;
                        if ($eleve->sexe === 'F') $admisF++;
                    } else {
                        $echouesT++;
                        if ($eleve->sexe === 'F') $echouesF++;
                    }
                }

                $donneesMatieres[$matiere] = [
                    'admis' => ['t' => $admisT, 'df' => $admisF],
                    'echoues' => ['t' => $echouesT, 'df' => $echouesF],
                ];
            }

            $lignesParMatiere[] = [
                'libelle' => $libelle,
                'matieres' => $donneesMatieres,
            ];
        }

        return [
            'matieres' => $matieres,
            'lignes' => $lignesParMatiere,
        ];
    }

    private function calculerTableauAdmisRedoublants(array $nomsClasses, $classes, int $trimestre, string $anneeScolaire): array
    {
        $lignes = [];

        foreach ($nomsClasses as $index => $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            $estDerniereClasse = $index === count($nomsClasses) - 1;
            $libelle = ($estDerniereClasse && $trimestre === 3) ? 'BEPC BLANC' : strtoupper($nomClasse);

            $cle = NoteController::cleCoefficients($classe->nom);
            $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];

            $eleves = $classe->eleves;

            $inscritsT = $eleves->count();
            $inscritsF = $eleves->where('sexe', 'F')->count();

            $presentsT = 0;
            $presentsF = 0;
            $admisT = 0;
            $admisF = 0;
            $redoublantsT = 0;
            $redoublantsF = 0;
            $exclusT = 0;
            $exclusF = 0;
            $abandonsT = 0;
            $abandonsF = 0;

            foreach ($eleves as $eleve) {
                $aUneNoteCeTrimestre = $eleve->notes()
                    ->where('trimestre', $trimestre)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->exists();

                $aUneNoteAnterieure = $eleve->notes()
                    ->where('trimestre', '<', $trimestre)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->exists();

                if (!$aUneNoteCeTrimestre) {
                    if ($trimestre > 1 && $aUneNoteAnterieure) {
                        $abandonsT++;
                        if ($eleve->sexe === 'F') $abandonsF++;
                    }
                    continue;
                }

                $presentsT++;
                if ($eleve->sexe === 'F') $presentsF++;

                $sommeTotal = 0;
                $sommeCoef = 0;
                foreach ($coefficients as $matiere => $coef) {
                    $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
                    if ($moyenne !== null) {
                        $sommeTotal += $moyenne * $coef;
                        $sommeCoef += $coef;
                    }
                }

                $moyenneGenerale = $sommeCoef > 0 ? $sommeTotal / $sommeCoef : null;
                if ($moyenneGenerale === null) continue;

                if ($moyenneGenerale >= 10) {
                    $admisT++;
                    if ($eleve->sexe === 'F') $admisF++;
                } elseif ($moyenneGenerale >= 5) {
                    $redoublantsT++;
                    if ($eleve->sexe === 'F') $redoublantsF++;
                } else {
                    $exclusT++;
                    if ($eleve->sexe === 'F') $exclusF++;
                }
            }

            $lignes[] = [
                'libelle' => $libelle,
                'inscrits' => ['t' => $inscritsT, 'df' => $inscritsF],
                'presents' => ['t' => $presentsT, 'df' => $presentsF],
                'admis' => ['t' => $admisT, 'df' => $admisF],
                'redoublants' => ['t' => $redoublantsT, 'df' => $redoublantsF],
                'exclus' => ['t' => $exclusT, 'df' => $exclusF],
                'abandons' => ['t' => $abandonsT, 'df' => $abandonsF],
            ];
        }

        $totaux = [
            'inscrits' => ['t' => 0, 'df' => 0],
            'presents' => ['t' => 0, 'df' => 0],
            'admis' => ['t' => 0, 'df' => 0],
            'redoublants' => ['t' => 0, 'df' => 0],
            'exclus' => ['t' => 0, 'df' => 0],
            'abandons' => ['t' => 0, 'df' => 0],
        ];

        foreach ($lignes as $ligne) {
            foreach (['inscrits', 'presents', 'admis', 'redoublants', 'exclus', 'abandons'] as $cle) {
                $totaux[$cle]['t'] += $ligne[$cle]['t'];
                $totaux[$cle]['df'] += $ligne[$cle]['df'];
            }
        }

        return ['lignes' => $lignes, 'totaux' => $totaux];
    }

    private function calculerTableauCompare(array $nomsClasses, $classes, string $anneeScolaire): array
    {
        $lignes = [];
        $totauxParTrimestre = [1 => null, 2 => null, 3 => null];

        $lignesParTrimestre = [1 => [], 2 => [], 3 => []];

        foreach ($nomsClasses as $index => $nom) {
            $classe = $classes->get($nom);
            if (!$classe) continue;

            $ligneCombinee = ['libelle' => strtoupper($nom)];

            foreach ([1, 2, 3] as $t) {
                $donnees = $this->calculerLigne($classe, $t, $anneeScolaire, strtoupper($nom));
                $ligneCombinee['t' . $t] = $donnees;
                $lignesParTrimestre[$t][] = $donnees;
            }

            $lignes[] = $ligneCombinee;
        }

        foreach ([1, 2, 3] as $t) {
            $totauxParTrimestre[$t] = $this->calculerTotaux($lignesParTrimestre[$t]);
        }

        return [
            'lignes' => $lignes,
            'totaux' => $totauxParTrimestre,
        ];
    }

    private function anneeScolaireActuelle(): string
    {
        $mois = now()->month;
        $annee = now()->year;

        if ($mois >= 10) return $annee . '-' . ($annee + 1);
        if ($mois <= 6) return ($annee - 1) . '-' . $annee;
        return $annee . '-' . ($annee + 1);
    }
    public function lycee(Request $request)
    {
        $trimestre = (int) $request->query('trimestre', 1);
        $anneeScolaire = $request->query('annee_scolaire', $this->anneeScolaireActuelle());

        $nomsClasses = ['Seconde A', 'Seconde C', 'Première A', 'Première S', 'Terminale A', 'Terminale C', 'Terminale D'];
        $classesTerminales = ['Terminale A', 'Terminale C', 'Terminale D'];

        $classes = Classe::whereIn('nom', $nomsClasses)->get()->keyBy('nom');

        $lignes = [];
        foreach ($nomsClasses as $nom) {
            $classe = $classes->get($nom);
            if (!$classe) continue;

            $estTerminale = in_array($nom, $classesTerminales);
            $libelle = ($estTerminale && $trimestre === 3)
                ? 'BAC BLANC ' . substr($nom, -1)
                : strtoupper($nom);

            $lignes[] = $this->calculerLigne($classe, $trimestre, $anneeScolaire, $libelle);
        }

        $totaux = $this->calculerTotaux($lignes);

        $matieres = ['Français', 'Histoire-Géographie', 'Philosophie', 'Anglais', 'Mathématiques', 'SVT', 'Espagnol', 'EPS'];
        $tableauMatieres = $this->calculerTableauMatieresLycee($matieres, $nomsClasses, $classesTerminales, $classes, $trimestre, $anneeScolaire);

        $tableauAdmisRedoublants = $this->calculerTableauAdmisRedoublantsLycee($nomsClasses, $classesTerminales, $classes, $trimestre, $anneeScolaire);

        return Inertia::render('Statistiques/Lycee', [
            'trimestre' => $trimestre,
            'anneeScolaire' => $anneeScolaire,
            'lignes' => $lignes,
            'totaux' => $totaux,
            'tableauMatieres' => $tableauMatieres,
            'tableauAdmisRedoublants' => $tableauAdmisRedoublants,
        ]);
    }

    private function calculerTableauMatieresLycee(array $matieres, array $nomsClasses, array $classesTerminales, $classes, int $trimestre, string $anneeScolaire): array
    {
        $lignesParMatiere = [];

        foreach ($nomsClasses as $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            $estTerminale = in_array($nomClasse, $classesTerminales);
            $libelle = ($estTerminale && $trimestre === 3)
                ? 'BAC BLANC ' . substr($nomClasse, -1)
                : strtoupper($nomClasse);

            $cle = NoteController::cleCoefficients($classe->nom);
            $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];

            $eleves = $classe->eleves;
            $donneesMatieres = [];

            foreach ($matieres as $matiere) {
                if (!array_key_exists($matiere, $coefficients)) {
                    $donneesMatieres[$matiere] = null;
                    continue;
                }

                $admisT = 0;
                $admisF = 0;
                $echouesT = 0;
                $echouesF = 0;

                foreach ($eleves as $eleve) {
                    $aUneNote = $eleve->notes()
                        ->where('trimestre', $trimestre)
                        ->where('annee_scolaire', $anneeScolaire)
                        ->exists();

                    if (!$aUneNote) continue;

                    $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
                    if ($moyenne === null) continue;

                    if ($moyenne >= 10) {
                        $admisT++;
                        if ($eleve->sexe === 'F') $admisF++;
                    } else {
                        $echouesT++;
                        if ($eleve->sexe === 'F') $echouesF++;
                    }
                }

                $donneesMatieres[$matiere] = [
                    'admis' => ['t' => $admisT, 'df' => $admisF],
                    'echoues' => ['t' => $echouesT, 'df' => $echouesF],
                ];
            }

            $lignesParMatiere[] = [
                'libelle' => $libelle,
                'matieres' => $donneesMatieres,
            ];
        }

        return [
            'matieres' => $matieres,
            'lignes' => $lignesParMatiere,
        ];
    }
    private function calculerTableauAdmisRedoublantsLycee(array $nomsClasses, array $classesTerminales, $classes, int $trimestre, string $anneeScolaire): array
    {
        $lignes = [];

        foreach ($nomsClasses as $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            $estTerminale = in_array($nomClasse, $classesTerminales);
            $libelle = ($estTerminale && $trimestre === 3)
                ? 'BAC BLANC ' . substr($nomClasse, -1)
                : strtoupper($nomClasse);

            $cle = NoteController::cleCoefficients($classe->nom);
            $coefficients = NoteController::COEFFICIENTS[$cle] ?? [];

            $eleves = $classe->eleves;

            $inscritsT = $eleves->count();
            $inscritsF = $eleves->where('sexe', 'F')->count();

            $presentsT = 0;
            $presentsF = 0;
            $admisT = 0;
            $admisF = 0;
            $redoublantsT = 0;
            $redoublantsF = 0;
            $exclusT = 0;
            $exclusF = 0;
            $abandonsT = 0;
            $abandonsF = 0;

            foreach ($eleves as $eleve) {
                $aUneNoteCeTrimestre = $eleve->notes()
                    ->where('trimestre', $trimestre)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->exists();

                $aUneNoteAnterieure = $eleve->notes()
                    ->where('trimestre', '<', $trimestre)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->exists();

                if (!$aUneNoteCeTrimestre) {
                    if ($trimestre > 1 && $aUneNoteAnterieure) {
                        $abandonsT++;
                        if ($eleve->sexe === 'F') $abandonsF++;
                    }
                    continue;
                }

                $presentsT++;
                if ($eleve->sexe === 'F') $presentsF++;

                $sommeTotal = 0;
                $sommeCoef = 0;
                foreach ($coefficients as $matiere => $coef) {
                    $moyenne = $eleve->moyenne($matiere, $trimestre, $anneeScolaire);
                    if ($moyenne !== null) {
                        $sommeTotal += $moyenne * $coef;
                        $sommeCoef += $coef;
                    }
                }

                $moyenneGenerale = $sommeCoef > 0 ? $sommeTotal / $sommeCoef : null;
                if ($moyenneGenerale === null) continue;

                if ($moyenneGenerale >= 10) {
                    $admisT++;
                    if ($eleve->sexe === 'F') $admisF++;
                } elseif ($moyenneGenerale >= 5) {
                    $redoublantsT++;
                    if ($eleve->sexe === 'F') $redoublantsF++;
                } else {
                    $exclusT++;
                    if ($eleve->sexe === 'F') $exclusF++;
                }
            }

            $lignes[] = [
                'libelle' => $libelle,
                'inscrits' => ['t' => $inscritsT, 'df' => $inscritsF],
                'presents' => ['t' => $presentsT, 'df' => $presentsF],
                'admis' => ['t' => $admisT, 'df' => $admisF],
                'redoublants' => ['t' => $redoublantsT, 'df' => $redoublantsF],
                'exclus' => ['t' => $exclusT, 'df' => $exclusF],
                'abandons' => ['t' => $abandonsT, 'df' => $abandonsF],
            ];
        }

        $totaux = [
            'inscrits' => ['t' => 0, 'df' => 0],
            'presents' => ['t' => 0, 'df' => 0],
            'admis' => ['t' => 0, 'df' => 0],
            'redoublants' => ['t' => 0, 'df' => 0],
            'exclus' => ['t' => 0, 'df' => 0],
            'abandons' => ['t' => 0, 'df' => 0],
        ];

        foreach ($lignes as $ligne) {
            foreach (['inscrits', 'presents', 'admis', 'redoublants', 'exclus', 'abandons'] as $cle) {
                $totaux[$cle]['t'] += $ligne[$cle]['t'];
                $totaux[$cle]['df'] += $ligne[$cle]['df'];
            }
        }

        return ['lignes' => $lignes, 'totaux' => $totaux];
    }
    public function repartitionAge()
    {
        $anneeScolaire = $this->anneeScolaireActuelle();
        $dateReference = \Carbon\Carbon::parse(explode('-', $anneeScolaire)[0] . '-10-01');

        $nomsCollege = ['6ᵉ 1', '6ᵉ 2', '5ᵉ', '4ᵉ', '3ᵉ'];
        $nomsLycee = ['Seconde A', 'Seconde C', 'Première A', 'Première S', 'Terminale A', 'Terminale C', 'Terminale D'];

        $college = $this->calculerRepartitionAge($nomsCollege, $dateReference);
        $lycee = $this->calculerRepartitionAge($nomsLycee, $dateReference);

        return Inertia::render('Statistiques/RepartitionAge', [
            'anneeScolaire' => $anneeScolaire,
            'college' => $college,
            'lycee' => $lycee,
        ]);
    }

    private function calculerRepartitionAge(array $nomsClasses, \Carbon\Carbon $dateReference): array
    {
        $classes = Classe::whereIn('nom', $nomsClasses)->with('eleves')->get()->keyBy('nom');

        $donnees = []; // [age][classeNom] = ['t' => n, 'f' => n]
        $ageMin = null;
        $ageMax = null;

        foreach ($nomsClasses as $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            foreach ($classe->eleves as $eleve) {
                if (!$eleve->date_naissance) continue;

                $age = (int) $eleve->date_naissance->diffInYears($dateReference);

                // Ignore les âges manifestement incorrects (donnée à corriger à la source)
                if ($age < 10 || $age > 30) continue;

                if ($ageMin === null || $age < $ageMin) $ageMin = $age;
                if ($ageMax === null || $age > $ageMax) $ageMax = $age;

                if (!isset($donnees[$age])) $donnees[$age] = [];
                if (!isset($donnees[$age][$nomClasse])) $donnees[$age][$nomClasse] = ['t' => 0, 'f' => 0];

                $donnees[$age][$nomClasse]['t']++;
                if ($eleve->sexe === 'F') $donnees[$age][$nomClasse]['f']++;
            }
        }

        $lignes = [];
        if ($ageMin !== null) {
            for ($age = $ageMin; $age <= $ageMax; $age++) {
                $ligne = ['age' => $age, 'classes' => [], 'totalT' => 0, 'totalF' => 0];

                foreach ($nomsClasses as $nomClasse) {
                    $valeurs = $donnees[$age][$nomClasse] ?? ['t' => 0, 'f' => 0];
                    $ligne['classes'][$nomClasse] = $valeurs;
                    $ligne['totalT'] += $valeurs['t'];
                    $ligne['totalF'] += $valeurs['f'];
                }

                $lignes[] = $ligne;
            }
        }

        return [
            'classes' => $nomsClasses,
            'lignes' => $lignes,
        ];
    }
    public function evolution()
    {
        $anneeScolaire = $this->anneeScolaireActuelle();

        $nomsCollege = ['6ᵉ 1', '6ᵉ 2', '5ᵉ', '4ᵉ', '3ᵉ'];
        $nomsLycee = ['Seconde A', 'Seconde C', 'Première A', 'Première S', 'Terminale A', 'Terminale C', 'Terminale D'];

        $college = $this->calculerEvolution($nomsCollege, $anneeScolaire);
        $lycee = $this->calculerEvolution($nomsLycee, $anneeScolaire);

        return Inertia::render('Statistiques/Evolution', [
            'anneeScolaire' => $anneeScolaire,
            'college' => $college,
            'lycee' => $lycee,
        ]);
    }

    private function calculerEvolution(array $nomsClasses, string $anneeScolaire): array
    {
        $classes = Classe::whereIn('nom', $nomsClasses)->get()->keyBy('nom');
        $series = [];

        foreach ($nomsClasses as $nomClasse) {
            $classe = $classes->get($nomClasse);
            if (!$classe) continue;

            $tauxParTrimestre = [];
            for ($t = 1; $t <= 3; $t++) {
                $ligne = $this->calculerLigne($classe, $t, $anneeScolaire, $nomClasse);
                $taux = $ligne['presents']['t'] > 0
                    ? round(($ligne['admis']['t'] / $ligne['presents']['t']) * 100, 1)
                    : 0;
                $tauxParTrimestre[] = $taux;
            }

            $series[] = [
                'nom' => $nomClasse,
                'valeurs' => $tauxParTrimestre,
            ];
        }

        return $series;
    }
}
