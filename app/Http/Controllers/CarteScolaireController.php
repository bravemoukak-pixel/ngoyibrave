<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Inertia\Inertia;

class CarteScolaireController extends Controller
{
    const CLASSES_AUTORISEES = ['CM2', 'Terminale A', 'Terminale C', 'Terminale D'];

    public function index(Classe $classe)
    {
        $classe->load('niveau');
        $nom = $classe->nom;
        $prefixesAutorises = ['CM2', '3ᵉ', 'Terminale A', 'Terminale C', 'Terminale D'];
        $autorisee = collect($prefixesAutorises)->contains(fn($prefixe) => str_starts_with($nom, $prefixe));
        $estLycee = str_starts_with($classe->niveau->titre, 'Lycée');

        if (!$autorisee) {
            abort(403, "Les cartes scolaires ne sont disponibles que pour CM2, 3e et Terminale.");
        }

        $eleves = $classe->eleves()->orderBy('nom')->get()->map(fn($eleve) => [
            'id' => $eleve->id,
            'matricule' => $eleve->matricule_examen,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'sexe' => $eleve->sexe,
            'date_naissance' => $eleve->date_naissance->format('d/m/Y'),
            'lieu_naissance' => $eleve->lieu_naissance,
            'adresse' => $eleve->adresse,
            'photo' => $eleve->photo,
        ]);

        return Inertia::render('CartesScolaires', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'anneeScolaire' => $this->anneeScolaire(),
            'eleves' => $eleves,
            'estLycee' => $estLycee,
        ]);
    }

    private function anneeScolaire()
    {
        $mois = now()->month;
        $annee = now()->year;

        if ($mois >= 10) return $annee . '-' . ($annee + 1);
        if ($mois <= 6) return ($annee - 1) . '-' . $annee;
        return $annee . '-' . ($annee + 1);
    }
}
