<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PointageController extends Controller
{
    public function index()
    {
        return Inertia::render('Pointages/Pointer', [
            'professeurs' => Professeur::orderBy('nom')->get(['id', 'nom', 'prenom']),
        ]);
    }

    public function enregistrer(Request $request)
    {
        $data = $request->validate([
            'professeur_id' => 'required|exists:professeurs,id',
            'pin' => 'required|string|size:4',
        ]);

        $professeur = Professeur::findOrFail($data['professeur_id']);

        if ($professeur->pin !== $data['pin']) {
            return back()->withErrors(['pin' => 'Code PIN incorrect.']);
        }

        $dernierPointage = $professeur->pointages()->latest('horodatage')->first();
        $prochainType = ($dernierPointage && $dernierPointage->type === 'arrivee') ? 'depart' : 'arrivee';

        Pointage::create([
            'professeur_id' => $professeur->id,
            'type' => $prochainType,
            'horodatage' => now(),
        ]);

        $message = $prochainType === 'arrivee'
            ? "Arrivée enregistrée pour {$professeur->prenom} {$professeur->nom} à " . now()->format('H:i')
            : "Départ enregistré pour {$professeur->prenom} {$professeur->nom} à " . now()->format('H:i');

        return back()->with('success', $message);
    }
}
