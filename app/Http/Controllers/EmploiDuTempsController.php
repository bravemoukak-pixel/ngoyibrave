<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\CreneauHoraire;
use App\Models\EmploiDuTemps;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EmploiDuTempsController extends Controller
{
    protected array $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

    public function classe(Classe $classe)
    {
        $classe->load('niveau');
        $estPrimaire = strtolower($classe->niveau->titre) === 'primaire';

        if ($estPrimaire) {
            $creneaux = [
                'primaire_a' => CreneauHoraire::where('grille', 'primaire_a')->orderBy('ordre')->get(),
                'primaire_b' => CreneauHoraire::where('grille', 'primaire_b')->orderBy('ordre')->get(),
            ];
        } else {
            $creneaux = [
                'standard' => CreneauHoraire::where('grille', 'standard')->orderBy('ordre')->get(),
            ];
        }

        $emplois = EmploiDuTemps::where('classe_id', $classe->id)->get();
        return Inertia::render('EmploisDuTemps/Classe', [
            'classe' => $classe,
            'estPrimaire' => $estPrimaire,
            'jours' => $this->jours,
            'creneaux' => $creneaux,
            'emplois' => $emplois,
            'professeurs' => Professeur::orderBy('nom')->get(['id', 'nom', 'prenom']),
            'anneeScolaire' => $this->anneeScolaire(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jour' => 'required|in:' . implode(',', $this->jours),
            'creneau_horaire_id' => 'required|exists:creneaux_horaires,id',
            'classe_id' => 'required|exists:classes,id',
            'professeur_id' => 'nullable|exists:professeurs,id',
            'matiere' => 'required|string|max:255',
            'type_seance' => 'required|in:cours,td',
        ]);

        if (!empty($data['professeur_id'])) {
            $conflitProf = EmploiDuTemps::where('jour', $data['jour'])
                ->where('creneau_horaire_id', $data['creneau_horaire_id'])
                ->where('professeur_id', $data['professeur_id'])
                ->where('classe_id', '!=', $data['classe_id'])
                ->with('classe')
                ->first();

            if ($conflitProf) {
                $memeCoursCommun = $conflitProf->matiere === $data['matiere']
                    && $conflitProf->type_seance === $data['type_seance'];

                if (!$memeCoursCommun) {
                    throw ValidationException::withMessages([
                        'professeur_id' => "Ce professeur est déjà occupé avec la classe {$conflitProf->classe->nom} sur ce créneau (matière ou type différent).",
                    ]);
                }
            }
        }

        EmploiDuTemps::updateOrCreate(
            [
                'jour' => $data['jour'],
                'creneau_horaire_id' => $data['creneau_horaire_id'],
                'classe_id' => $data['classe_id'],
            ],
            [
                'professeur_id' => $data['professeur_id'],
                'matiere' => $data['matiere'],
                'type_seance' => $data['type_seance'],
            ]
        );

        return back()->with('success', 'Emploi du temps mis à jour.');
    }

    public function destroy(EmploiDuTemps $emploiDuTemps)
    {
        $emploiDuTemps->delete();

        return back()->with('success', 'Case supprimée.');
    }

    public function professeur(Professeur $professeur)
    {
        $creneauxParGrille = [
            'standard' => CreneauHoraire::where('grille', 'standard')->orderBy('ordre')->get(),
            'primaire_a' => CreneauHoraire::where('grille', 'primaire_a')->orderBy('ordre')->get(),
            'primaire_b' => CreneauHoraire::where('grille', 'primaire_b')->orderBy('ordre')->get(),
        ];

        $emplois = EmploiDuTemps::where('professeur_id', $professeur->id)
            ->with(['classe', 'creneauHoraire'])
            ->get();

        // On ne garde que les grilles où ce professeur a effectivement des cours
        $grillesUtilisees = $emplois->pluck('creneauHoraire.grille')->unique();

        return Inertia::render('EmploisDuTemps/Professeur', [
            'professeur' => $professeur,
            'jours' => $this->jours,
            'creneaux' => collect($creneauxParGrille)->only($grillesUtilisees->isEmpty() ? ['standard'] : $grillesUtilisees->toArray()),
            'emplois' => $emplois,
            'anneeScolaire' => $this->anneeScolaire(),
        ]);
    }
    public function generalCollege()
    {
        $donnees = $this->donneesGenerales('Collège', [
            '6e1',
            '6e2',
            '5e',
            '4e',
            '3e',
        ]);

        return Inertia::render('EmploisDuTemps/General', $donnees);
    }

    public function generalLycee()
    {
        $donnees = $this->donneesGenerales('Lycée', [
            'Seconde A',
            'Seconde C',
            'Première A',
            'Première S',
            'Terminale A',
            'Terminale D',
            'Terminale C',
        ]);

        return Inertia::render('EmploisDuTemps/General', $donnees);
    }

    public function generalComplet()
    {
        $college = $this->donneesGenerales('Collège', [
            '6e1',
            '6e2',
            '5e',
            '4e',
            '3e',
        ]);

        $lycee = $this->donneesGenerales('Lycée', [
            'Seconde A',
            'Seconde C',
            'Première A',
            'Première S',
            'Terminale A',
            'Terminale D',
            'Terminale C',
        ]);

        return Inertia::render('EmploisDuTemps/GeneralComplet', [
            'college' => $college,
            'lycee' => $lycee,
            'anneeScolaire' => $this->anneeScolaire(),
        ]);
    }

    private function donneesGenerales(string $niveauTitre, array $ordreClasses): array
    {
        $classes = Classe::whereHas('niveau', fn($q) => $q->where('titre', 'like', $niveauTitre . '%'))
            ->get()
            ->sortBy(function ($c) use ($ordreClasses) {
                $position = array_search($c->nom, $ordreClasses);
                return $position === false ? 999 : $position;
            })
            ->values();

        $creneaux = CreneauHoraire::where('grille', 'standard')->orderBy('ordre')->get();

        $emplois = EmploiDuTemps::whereIn('classe_id', $classes->pluck('id'))->get();

        return [
            'titre' => $niveauTitre,
            'classes' => $classes->map(fn($c) => ['id' => $c->id, 'nom' => $c->nom]),
            'jours' => $this->jours,
            'creneaux' => $creneaux,
            'emplois' => $emplois,
            'anneeScolaire' => $this->anneeScolaire(),
        ];
    }

    private function anneeScolaire(): string
    {
        $mois = now()->month;
        $anneeActuelle = now()->year;

        if ($mois >= 10) {
            return $anneeActuelle . '-' . ($anneeActuelle + 1);
        } elseif ($mois <= 6) {
            return ($anneeActuelle - 1) . '-' . $anneeActuelle;
        } else {
            return $anneeActuelle . '-' . ($anneeActuelle + 1);
        }
    }
}
