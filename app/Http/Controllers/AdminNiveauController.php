<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminNiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::with('classes')->get();

        return Inertia::render('Admin/Niveaux', [
            'niveaux' => $niveaux,
        ]);
    }

    public function storeNiveau(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'couleur' => 'required|in:blue,green,purple,red',
            'icone' => 'required|string|max:10',
        ]);

        Niveau::create($data);

        return back()->with('success', 'Niveau ajouté.');
    }

    public function storeClasse(Request $request)
    {
        $data = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'nom' => 'required|string|max:255',
            'code_matricule' => 'required|string|max:5',
        ]);

        Classe::create($data);

        return back()->with('success', 'Classe ajoutée.');
    }

    public function destroyClasse(Classe $classe)
    {
        $classe->delete();

        return back()->with('success', 'Classe supprimée.');
    }
    public function updateMontant(Request $request, Niveau $niveau)
    {
        $data = $request->validate([
            'montant_annuel_defaut' => 'nullable|numeric|min:0',
        ]);

        $niveau->update($data);

        return back()->with('success', 'Montant annuel par défaut mis à jour.');
    }
    public function destroyNiveau(Niveau $niveau)
    {
        $niveau->delete();

        return back()->with('success', 'Niveau supprimé.');
    }
    public function updateClasse(Request $request, Classe $classe)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'code_matricule' => 'nullable|string|max:10',
        ]);

        $classe->update($data);

        return back()->with('success', 'Classe modifiée.');
    }
}
