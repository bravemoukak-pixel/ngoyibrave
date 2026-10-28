<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\NoteController;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Niveau;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AdminNiveauController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\AssiduiteController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\ParentEleveController;
use App\Http\Controllers\PaieController;
use App\Models\Professeur;
use App\Models\ParentEleve;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\SuiviNotesController;
use App\Http\Controllers\CarteScolaireController;
use App\Http\Controllers\MatriculeExamenController;
use App\Http\Controllers\ReleveViergeController;
use App\Http\Controllers\SuiviPaiementController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\PassageClasseController;
use App\Http\Controllers\DiplomeController;
use App\Http\Controllers\HistoriqueClasseController;
use App\Http\Controllers\SauvegardeController;
use App\Http\Controllers\EspaceParentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $totalEleves = Eleve::count();
        $elevesCollege = Eleve::whereHas('classe.niveau', fn($q) => $q->where('titre', 'Collège'))->count();
        $elevesLycee = Eleve::whereHas('classe.niveau', fn($q) => $q->where('titre', 'like', 'Lycée%'))->count();
        $elevesPrimaire = Eleve::whereHas('classe.niveau', fn($q) => $q->where('titre', 'Primaire'))->count();

        $fillesCollege = Eleve::where('sexe', 'F')->whereHas('classe.niveau', fn($q) => $q->where('titre', 'Collège'))->count();
        $fillesLycee = Eleve::where('sexe', 'F')->whereHas('classe.niveau', fn($q) => $q->where('titre', 'like', 'Lycée%'))->count();
        $fillesPrimaire = Eleve::where('sexe', 'F')->whereHas('classe.niveau', fn($q) => $q->where('titre', 'Primaire'))->count();

        $professeursCollege = Professeur::whereHas('classes.niveau', fn($q) => $q->where('titre', 'Collège'))->count();
        $professeursLycee = Professeur::whereHas('classes.niveau', fn($q) => $q->where('titre', 'like', 'Lycée%'))->count();
        $professeursPrimaire = Professeur::whereHas('classes.niveau', fn($q) => $q->where('titre', 'Primaire'))->count();

        $effectifsParClasse = Classe::with('niveau')
            ->withCount('eleves')
            ->withCount(['eleves as filles_count' => fn($q) => $q->where('sexe', 'F')])
            ->orderBy('nom')
            ->get()
            ->map(function ($c) {
                $titre = $c->niveau->titre;
                $categorie = match (true) {
                    $titre === 'Primaire' => 'Primaire',
                    $titre === 'Collège' => 'Collège',
                    str_starts_with($titre, 'Lycée') => 'Lycée',
                    default => $titre,
                };

                return [
                    'nom' => $c->nom,
                    'niveau' => $categorie,
                    'effectif' => $c->eleves_count,
                    'filles' => $c->filles_count,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalEleves' => $totalEleves,
                'filles' => Eleve::where('sexe', 'F')->count(),
                'elevesCollege' => $elevesCollege,
                'elevesLycee' => $elevesLycee,
                'professeursPrimaire' => $professeursPrimaire,
                'elevesPrimaire' => $elevesPrimaire,
                'fillesCollege' => $fillesCollege,
                'fillesLycee' => $fillesLycee,
                'fillesPrimaire' => $fillesPrimaire,
                'totalClasses' => Classe::count(),
                'totalNiveaux' => Niveau::count(),
                'totalProfesseurs' => Professeur::count(),
                'professeursCollege' => $professeursCollege,
                'professeursLycee' => $professeursLycee,
                'totalParents' => ParentEleve::count(),
                'effectifsParClasse' => $effectifsParClasse,
            ],
        ]);
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Rubriques Secrétaire (+ Admin automatiquement)
    Route::middleware(['role:secretaire,direction'])->group(function () {

        Route::get('/niveaux/{niveau}/inscription', [EleveController::class, 'create'])->name('eleves.create');
        Route::post('/eleves', [EleveController::class, 'store'])->name('eleves.store');

        Route::get('/professeurs', [ProfesseurController::class, 'index'])->name('professeurs.index');
        Route::get('/professeurs/annuaire', [ProfesseurController::class, 'annuaire'])->name('professeurs.annuaire');
        Route::get('/professeurs/create', [ProfesseurController::class, 'create'])->name('professeurs.create');
        Route::post('/professeurs', [ProfesseurController::class, 'store'])->name('professeurs.store');
        Route::get('/eleves/{eleve}/edit', [EleveController::class, 'edit'])->name('eleves.edit');
        Route::put('/eleves/{eleve}', [EleveController::class, 'update'])->name('eleves.update');
        Route::delete('/eleves/{eleve}', [EleveController::class, 'destroy'])->name('eleves.destroy');
        Route::get('/professeurs/{professeur}/edit', [ProfesseurController::class, 'edit'])->name('professeurs.edit');
        Route::put('/professeurs/{professeur}', [ProfesseurController::class, 'update'])->name('professeurs.update');
        Route::delete('/professeurs/{professeur}', [ProfesseurController::class, 'destroy'])->name('professeurs.destroy');
        Route::post('/professeurs/{professeur}/regenerer-pin', [ProfesseurController::class, 'regenererPin'])->name('professeurs.regenerer-pin');
        Route::get('/admin/niveaux', [AdminNiveauController::class, 'index'])->name('admin.niveaux');
        Route::post('/admin/niveaux', [AdminNiveauController::class, 'storeNiveau'])->name('admin.niveaux.store');
        Route::post('/admin/classes', [AdminNiveauController::class, 'storeClasse'])->name('admin.classes.store');
        Route::put('/admin/classes/{classe}', [AdminNiveauController::class, 'updateClasse'])->name('admin.classes.update');
        Route::delete('/admin/classes/{classe}', [AdminNiveauController::class, 'destroyClasse'])->name('admin.classes.destroy');
        Route::delete('/admin/niveaux/{niveau}', [AdminNiveauController::class, 'destroyNiveau'])->name('admin.niveaux.destroy');
        Route::put('/admin/niveaux/{niveau}/montant', [AdminNiveauController::class, 'updateMontant'])->name('admin.niveaux.montant');
        Route::get('/classes/{classe}/assiduites', [AssiduiteController::class, 'saisie'])->name('assiduites.saisie');
        Route::post('/assiduites', [AssiduiteController::class, 'enregistrer'])->name('assiduites.enregistrer');
        Route::get('/parents', [ParentEleveController::class, 'index'])->name('parents.index');
        Route::get('/parents/create', [ParentEleveController::class, 'create'])->name('parents.create');
        Route::post('/parents', [ParentEleveController::class, 'store'])->name('parents.store');
        Route::get('/parents/{parent}/edit', [ParentEleveController::class, 'edit'])->name('parents.edit');
        Route::put('/parents/{parent}', [ParentEleveController::class, 'update'])->name('parents.update');
        Route::delete('/parents/{parent}', [ParentEleveController::class, 'destroy'])->name('parents.destroy');
        Route::get('/classes/{classe}/cartes-scolaires', [CarteScolaireController::class, 'index'])->name('cartes-scolaires.index');
        Route::get('/classes/{classe}/matricules-examen', [MatriculeExamenController::class, 'saisie'])->name('matricules-examen.saisie');
        Route::post('/matricules-examen', [MatriculeExamenController::class, 'enregistrer'])->name('matricules-examen.enregistrer');
        Route::get('/emplois-du-temps/classe/{classe}', [EmploiDuTempsController::class, 'classe'])->name('emplois-du-temps.classe');
        Route::post('/emplois-du-temps', [EmploiDuTempsController::class, 'store'])->name('emplois-du-temps.store');
        Route::delete('/emplois-du-temps/{emploiDuTemps}', [EmploiDuTempsController::class, 'destroy'])->name('emplois-du-temps.destroy');
        Route::get('/emplois-du-temps/professeur/{professeur}', [EmploiDuTempsController::class, 'professeur'])->name('emplois-du-temps.professeur');
        Route::get('/passage-classe/{classe}', [PassageClasseController::class, 'index'])->name('passage-classe.index');
        Route::post('/passage-classe/{classe}', [PassageClasseController::class, 'valider'])->name('passage-classe.valider');
        Route::get('/diplomes/{classe}', [DiplomeController::class, 'index'])->name('diplomes.index');
        Route::get('/historique-classes', [HistoriqueClasseController::class, 'index'])->name('historique-classes.index');
        Route::get('/emplois-du-temps/general/college', [EmploiDuTempsController::class, 'generalCollege'])->name('emplois-du-temps.general.college');
        Route::get('/emplois-du-temps/general/lycee', [EmploiDuTempsController::class, 'generalLycee'])->name('emplois-du-temps.general.lycee');
        Route::get('/emplois-du-temps/general/complet', [\App\Http\Controllers\EmploiDuTempsController::class, 'generalComplet'])->name('emplois-du-temps.general.complet');
        Route::get('/statistiques/college', [\App\Http\Controllers\StatistiquesController::class, 'college'])->name('statistiques.college');
        Route::get('/statistiques/lycee', [\App\Http\Controllers\StatistiquesController::class, 'lycee'])->name('statistiques.lycee');
        Route::get('/statistiques/repartition-age', [\App\Http\Controllers\StatistiquesController::class, 'repartitionAge'])->name('statistiques.repartition-age');
        Route::get('/statistiques/evolution', [\App\Http\Controllers\StatistiquesController::class, 'evolution'])->name('statistiques.evolution');
        Route::get('/parents/par-niveau', [\App\Http\Controllers\ParentEleveController::class, 'parNiveau'])->name('parents.par-niveau');
    });
    // à ajouter juste après le groupe 'secretaire' :
    Route::middleware(['role:secretaire,primaire,direction'])->group(function () {
        Route::get('/niveaux', [NiveauController::class, 'index'])->name('niveaux.index');
        Route::get('/niveaux/{niveau}/eleves', [NiveauController::class, 'eleves'])->name('niveaux.eleves');
        Route::get('/classes/{classe}/eleves', [ClasseController::class, 'eleves'])->name('classes.eleves');
        Route::get('/classes/{classe}/notes', [NoteController::class, 'saisie'])->name('notes.saisie');
        Route::post('/notes', [NoteController::class, 'enregistrer'])->name('notes.enregistrer');
        Route::get('/eleves/{eleve}/bulletin', [BulletinController::class, 'show'])->name('bulletin.show');
        Route::get('/suivi-notes', [SuiviNotesController::class, 'index'])->name('suivi-notes.index');
        Route::get('/suivi-notes/classe', [SuiviNotesController::class, 'classe'])->name('suivi-notes.classe');
        Route::get('/classes/{classe}/releve-vierge', [ReleveViergeController::class, 'index'])->name('releve-vierge.index');
    });

    // Rubriques Comptable (+ Admin automatiquement)
    Route::middleware(['role:comptable,direction'])->group(function () {
        Route::get('/pointages/recap', [PointageController::class, 'recap'])->name('pointages.recap');
        Route::get('/pointages/gestion', [PointageController::class, 'gestion'])->name('pointages.gestion');
        Route::post('/pointages', [PointageController::class, 'ajouter'])->name('pointages.ajouter');
        Route::put('/pointages/{pointage}', [PointageController::class, 'modifier'])->name('pointages.modifier');
        Route::delete('/pointages/{pointage}', [PointageController::class, 'supprimer'])->name('pointages.supprimer');
        Route::get('/paie', [PaieController::class, 'index'])->name('paie.index');
        Route::post('/paie/lignes', [PaieController::class, 'ajouterLigne'])->name('paie.lignes.ajouter');
        Route::delete('/paie/lignes/{ligne}', [PaieController::class, 'supprimerLigne'])->name('paie.lignes.supprimer');
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/{parent}', [PaiementController::class, 'show'])->name('paiements.show');
        Route::post('/paiements', [PaiementController::class, 'ajouterPaiement'])->name('paiements.ajouter');
        Route::delete('/paiements/{paiement}', [PaiementController::class, 'supprimerPaiement'])->name('paiements.supprimer');
        Route::get('/suivi-paiements', [SuiviPaiementController::class, 'index'])->name('suivi-paiements.index');
        Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');
    });
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/sauvegarde/exporter', [SauvegardeController::class, 'exporter'])->name('sauvegarde.exporter');
    });
    Route::middleware(['role:parent'])->group(function () {
        Route::get('/espace-parent', [EspaceParentController::class, 'index'])->name('espace-parent.index');
        Route::get('/espace-parent/eleves/{eleve}/bulletin', [EspaceParentController::class, 'bulletin'])->name('espace-parent.bulletin');
    });
});

// Accessible sans connexion admin — pointage des professeurs via leur PIN
Route::get('/pointage', [PointageController::class, 'index'])->name('pointage.index');
Route::post('/pointage', [PointageController::class, 'enregistrer'])->name('pointage.enregistrer');
