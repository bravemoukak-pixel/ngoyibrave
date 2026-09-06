<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\LignePaie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaieController extends Controller
{
    public function index(Request $request)
    {
        $professeurId = $request->query('professeur_id');
        $mois = $request->query('mois', now()->format('Y-m'));

        $professeurs = Professeur::orderBy('nom')->get(['id', 'nom', 'prenom']);

        $bulletin = null;

        if ($professeurId) {
            $bulletin = $this->genererBulletin($professeurId, $mois);
        }

        return Inertia::render('Paie/Bulletin', [
            'professeurs' => $professeurs,
            'professeurId' => $professeurId,
            'mois' => $mois,
            'bulletin' => $bulletin,
        ]);
    }

    private function genererBulletin($professeurId, $mois)
    {
        $professeur = Professeur::with('classes.niveau')->findOrFail($professeurId);

        $debut = \Carbon\Carbon::parse($mois . '-01')->startOfMonth();
        $fin = $debut->copy()->endOfMonth();

        $parametres = \App\Models\Parametre::first();
        $estLycee = $professeur->classes->contains(fn($c) => str_starts_with($c->niveau->titre, 'Lycée'));
        $taux = $estLycee ? $parametres->taux_horaire_lycee : $parametres->taux_horaire_college;

        $pointages = $professeur->pointages()
            ->whereBetween('horodatage', [$debut, $fin])
            ->orderBy('horodatage')
            ->get();

        $secondes = 0;
        $arrivee = null;

        foreach ($pointages as $p) {
            if ($p->type === 'arrivee') {
                $arrivee = $p->horodatage;
            } elseif ($p->type === 'depart' && $arrivee) {
                $secondes += $arrivee->diffInSeconds($p->horodatage);
                $arrivee = null;
            }
        }

        $heures = round($secondes / 3600, 2);
        $salaireBrut = round($heures * $taux, 2);

        $lignes = LignePaie::where('professeur_id', $professeurId)
            ->whereYear('mois', $debut->year)
            ->whereMonth('mois', $debut->month)
            ->get();

        $totalPrimes = $lignes->where('type', 'prime')->sum('montant');
        $totalDeductions = $lignes->where('type', 'deduction')->sum('montant');
        $netAPayer = round($salaireBrut + $totalPrimes - $totalDeductions, 2);

        return [
            'professeur' => [
                'id' => $professeur->id,
                'nom' => $professeur->nom,
                'prenom' => $professeur->prenom,
                'matiere' => $professeur->matiere,
                'niveau' => $estLycee ? 'Lycée' : 'Collège',
            ],
            'mois' => $debut->locale('fr')->translatedFormat('F Y'),
            'heures' => $heures,
            'taux' => $taux,
            'salaireBrut' => $salaireBrut,
            'lignes' => $lignes,
            'totalPrimes' => $totalPrimes,
            'totalDeductions' => $totalDeductions,
            'netAPayer' => $netAPayer,
        ];
    }

    public function ajouterLigne(Request $request)
    {
        $data = $request->validate([
            'professeur_id' => 'required|exists:professeurs,id',
            'mois' => 'required',
            'type' => 'required|in:prime,deduction',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
        ]);

        LignePaie::create([
            'professeur_id' => $data['professeur_id'],
            'mois' => $data['mois'] . '-01',
            'type' => $data['type'],
            'libelle' => $data['libelle'],
            'montant' => $data['montant'],
        ]);

        return back()->with('success', 'Ligne ajoutée avec succès.');
    }

    public function supprimerLigne(LignePaie $ligne)
    {
        $ligne->delete();

        return back()->with('success', 'Ligne supprimée avec succès.');
    }
}
