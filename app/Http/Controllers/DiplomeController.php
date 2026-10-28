<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiplomeController extends Controller
{
    private array $classesExclues = ['cm2', '3e'];

   public function index(Classe $classe)
{
    $classe->load('niveau');
    $estLycee = str_starts_with($classe->niveau->titre, 'Lycée');
    $nomNormalise = NoteController::cleCoefficients($classe->nom);
        if (in_array($nomNormalise, $this->classesExclues) || str_starts_with(mb_strtolower($classe->nom), 'terminale')) {
            abort(403, "Les diplômes de cette classe sont délivrés par la Direction des Examens et Concours.");
        }

        $anneeScolaire = $this->anneeScolaireActuelle();
        $coefficients = NoteController::COEFFICIENTS[$nomNormalise] ?? [];

        $classement = $classe->eleves()->get()->map(function ($eleve) use ($coefficients, $anneeScolaire) {
            $moyenne = $this->moyenneAnnuelleEleve($eleve, $coefficients, $anneeScolaire);
            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'sexe' => $eleve->sexe,
                'moyenneAnnuelle' => $moyenne,
            ];
        })
            ->filter(fn($e) => $e['moyenneAnnuelle'] !== null)
            ->sortByDesc('moyenneAnnuelle')
            ->values()
            ->take(5)
            ->map(function ($eleve, $index) {
                $eleve['rang'] = $index + 1;
                $eleve['mention'] = $this->mention($eleve['moyenneAnnuelle']);
                $eleve['phraseEncouragement'] = $this->phraseEncouragement($eleve['mention']);
                return $eleve;
            });

        return Inertia::render('Diplomes/Index', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'anneeScolaire' => $anneeScolaire,
            'eleves' => $classement->values(),
            'estLycee' => $estLycee
        ]);
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

    private function anneeScolaireActuelle(): string
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
    private function phraseEncouragement($mention)
    {
        return match ($mention) {
            'Excellent' => "Pour son travail exceptionnel, sa rigueur et son excellence tout au long de l'année scolaire.",
            'Très bien' => "Pour son sérieux, sa persévérance et ses très bons résultats tout au long de l'année scolaire.",
            'Assez bien' => "Pour ses efforts constants et ses bons résultats tout au long de l'année scolaire.",
            default => "Pour ses efforts et son engagement tout au long de l'année scolaire.",
        };
    }
}
