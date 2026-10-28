<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\PaiementScolarite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EspaceParentController extends Controller
{
    public function index(Request $request)
    {
        $parent = $request->user()->parentEleve;

        $enfants = $parent->eleves()->with('classe.niveau')->get()->map(fn($eleve) => [
            'id' => $eleve->id,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'classe' => $eleve->classe->nom,
        ]);

        return Inertia::render('EspaceParent/Index', [
            'enfants' => $enfants,
        ]);
    }

    public function bulletin(Eleve $eleve, Request $request)
    {
        $parent = $request->user()->parentEleve;

        if ($eleve->parent_eleve_id !== $parent->id) {
            abort(403, "Cet élève n'est pas rattaché à votre compte.");
        }

        $trimestre = (int) $request->query('trimestre', 1);
        $estPrimaireOuCollege = !str_contains(mb_strtolower($eleve->classe->niveau->titre), 'lycée');
        $anneeScolaire = Eleve::anneeScolaireActuelle();
        [$anneeDebut, $anneeFin] = explode('-', $anneeScolaire);

        $moisRequis = match ($trimestre) {
            1 => ["{$anneeDebut}-10", "{$anneeDebut}-11", "{$anneeDebut}-12"],
            2 => ["{$anneeFin}-01", "{$anneeFin}-02", "{$anneeFin}-03"],
            3 => $estPrimaireOuCollege ? ["{$anneeFin}-04"] : ["{$anneeFin}-04", "{$anneeFin}-05"],
            default => [],
        };

        $moisPayes = PaiementScolarite::where('parent_eleve_id', $parent->id)
            ->get()
            ->map(fn($p) => $p->mois->format('Y-m'))
            ->toArray();

        $accesAutorise = collect($moisRequis)->every(fn($mois) => in_array($mois, $moisPayes));

        if (!$accesAutorise) {
            return Inertia::render('EspaceParent/AccesRefuse', [
                'eleve' => ['nom' => $eleve->nom, 'prenom' => $eleve->prenom],
                'trimestre' => $trimestre,
                'moisRequis' => $moisRequis,
            ]);
        }

        return app(BulletinController::class)->show($eleve, $request);
    }
}
