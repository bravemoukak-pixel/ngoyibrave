<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\HistoriqueScolarite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PassageClasseController extends Controller
{
    public function index(Classe $classe)
    {
        $classe->load('classeSuivante', 'niveau');
        $anneeActuelle = $this->anneeScolaireActuelle();
        $coefficients = NoteController::COEFFICIENTS[NoteController::cleCoefficients($classe->nom)] ?? [];
        $estExamenSansAutoMapping = $classe->classe_suivante_id === null && mb_strtolower($classe->nom) === '3e';

        $classesLycee = $estExamenSansAutoMapping
            ? Classe::whereHas('niveau', fn($q) => $q->where('titre', 'like', 'Lycée%'))->orderBy('nom')->get(['id', 'nom'])
            : [];

        $eleves = $classe->eleves()->orderBy('nom')->get()->map(function ($eleve) use ($coefficients, $anneeActuelle) {
            $moyenneAnnuelle = $this->moyenneAnnuelleEleve($eleve, $coefficients, $anneeActuelle);
            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'moyenneAnnuelle' => $moyenneAnnuelle,
                'moyenneManquante' => $moyenneAnnuelle === null,
                'passe' => $moyenneAnnuelle !== null && $moyenneAnnuelle >= 10,
            ];
        });
        return Inertia::render('PassageClasse/Index', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'classeSuivante' => $classe->classeSuivante ? ['id' => $classe->classeSuivante->id, 'nom' => $classe->classeSuivante->nom] : null,
            'estTerminale' => mb_strtolower($classe->nom) === 'terminale a' || str_starts_with(mb_strtolower($classe->nom), 'terminale'),
            'necessiteChoixFiliere' => $estExamenSansAutoMapping,
            'classesLycee' => $classesLycee,
            'eleves' => $eleves,
            'anneeActuelle' => $anneeActuelle,
            'anneeSuivante' => $this->anneeScolaireSuivante($anneeActuelle),
        ]);
    }

    public function valider(Request $request, Classe $classe)
    {
        $data = $request->validate([
            'decisions' => 'required|array',
            'decisions.*.eleve_id' => 'required|exists:eleves,id',
            'decisions.*.classe_id' => 'nullable|exists:classes,id',
            'decisions.*.diplome' => 'boolean',
        ]);

        $anneeActuelle = $this->anneeScolaireActuelle();
        $anneeSuivante = $this->anneeScolaireSuivante($anneeActuelle);

        foreach ($data['decisions'] as $decision) {
            $eleve = Eleve::find($decision['eleve_id']);

            if (!empty($decision['diplome'])) {
                $eleve->update(['statut' => 'diplome']);
                continue;
            }

            $nouvelleClasseId = $decision['classe_id'] ?? $eleve->classe_id;
            $eleve->update(['classe_id' => $nouvelleClasseId]);

            HistoriqueScolarite::updateOrCreate(
                ['eleve_id' => $eleve->id, 'annee_scolaire' => $anneeSuivante],
                ['classe_id' => $nouvelleClasseId]
            );
        }

        return redirect()->route('niveaux.index')->with('success', 'Passage de classe validé pour ' . $classe->nom . '.');
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

    private function anneeScolaireSuivante(string $anneeActuelle): string
    {
        [$debut, $fin] = explode('-', $anneeActuelle);
        return ($debut + 1) . '-' . ($fin + 1);
    }
}
