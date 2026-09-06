<?php

namespace App\Http\Controllers;

use App\Models\Assiduite;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssiduiteController extends Controller
{
    public function saisie(Classe $classe, Request $request)
    {
        $trimestre = (int) $request->query('trimestre', 1);
        $eleves = $classe->eleves()->orderBy('nom')->get();

        $donnees = $eleves->map(function ($eleve) use ($trimestre) {
            $a = $eleve->assiduites()->where('trimestre', $trimestre)->first();

            return [
                'eleve_id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'absences_justifiees' => $a->absences_justifiees ?? 0,
                'absences_injustifiees' => $a->absences_injustifiees ?? 0,
                'retards_justifies' => $a->retards_justifies ?? 0,
                'retards_injustifies' => $a->retards_injustifies ?? 0,
                'avertissements' => $a->avertissements ?? '',
                'blame' => $a->blame ?? '',
            ];
        });

        return Inertia::render('Assiduites/Saisie', [
            'classe' => ['id' => $classe->id, 'nom' => $classe->nom],
            'trimestre' => $trimestre,
            'donnees' => $donnees,
        ]);
    }

    public function enregistrer(Request $request)
    {
        $data = $request->validate([
            'trimestre' => 'required|integer|between:1,3',
            'lignes' => 'required|array',
            'lignes.*.eleve_id' => 'required|exists:eleves,id',
            'lignes.*.absences_justifiees' => 'nullable|integer|min:0',
            'lignes.*.absences_injustifiees' => 'nullable|integer|min:0',
            'lignes.*.retards_justifies' => 'nullable|integer|min:0',
            'lignes.*.retards_injustifies' => 'nullable|integer|min:0',
            'lignes.*.avertissements' => 'nullable|string',
            'lignes.*.blame' => 'nullable|string',
        ]);

        foreach ($data['lignes'] as $ligne) {
            Assiduite::updateOrCreate(
                ['eleve_id' => $ligne['eleve_id'], 'trimestre' => $data['trimestre']],
                [
                    'absences_justifiees' => $ligne['absences_justifiees'] ?? 0,
                    'absences_injustifiees' => $ligne['absences_injustifiees'] ?? 0,
                    'retards_justifies' => $ligne['retards_justifies'] ?? 0,
                    'retards_injustifies' => $ligne['retards_injustifies'] ?? 0,
                    'avertissements' => $ligne['avertissements'] ?? null,
                    'blame' => $ligne['blame'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Assiduité enregistrée.');
    }
}