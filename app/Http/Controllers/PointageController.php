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
    public function recap(Request $request)
    {
        $periode = $request->query('periode', 'jour'); // jour, semaine, mois
        $date = $request->query('date', now()->toDateString());

        $reference = \Carbon\Carbon::parse($date);

        $debut = match ($periode) {
            'semaine' => $reference->copy()->startOfWeek(),
            'mois' => $reference->copy()->startOfMonth(),
            default => $reference->copy()->startOfDay(),
        };

        $fin = match ($periode) {
            'semaine' => $reference->copy()->endOfWeek(),
            'mois' => $reference->copy()->endOfMonth(),
            default => $reference->copy()->endOfDay(),
        };

        $parametres = \App\Models\Parametre::first();

        $professeurs = Professeur::with(['classes.niveau', 'pointages' => function ($q) use ($debut, $fin) {
            $q->whereBetween('horodatage', [$debut, $fin])->orderBy('horodatage');
        }])
            ->whereHas('classes.niveau', fn($q) => $q->whereIn('titre', ['Collège']))
            ->orWhereHas('classes.niveau', fn($q) => $q->where('titre', 'like', 'Lycée%'))
            ->orderBy('nom')
            ->get();
        // Séances de TD de tous les professeurs, groupées par professeur
        $emploisTd = \App\Models\EmploiDuTemps::where('type_seance', 'td')
            ->get(['professeur_id', 'jour', 'creneau_horaire_id'])
            ->groupBy('professeur_id');

        $creneaux = \App\Models\CreneauHoraire::all()->keyBy('id');

        $jours = [1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 7 => 'dimanche'];

        $resultats = $professeurs->map(function ($prof) use ($parametres, $emploisTd, $creneaux, $jours) {
            $estLycee = $prof->classes->contains(fn($c) => str_starts_with($c->niveau->titre, 'Lycée'));
            $taux = $estLycee ? $parametres->taux_horaire_lycee : $parametres->taux_horaire_college;

            // TD du professeur, sans doublon (une même séance peut concerner plusieurs classes)
            $tdProf = ($emploisTd[$prof->id] ?? collect())->unique(fn($e) => $e->jour . '-' . $e->creneau_horaire_id);

            $secondes = 0;
            $secondesTd = 0;
            $arrivee = null;

            foreach ($prof->pointages as $p) {
                if ($p->type === 'arrivee') {
                    $arrivee = $p->horodatage;
                } elseif ($p->type === 'depart' && $arrivee) {
                    $duree = $arrivee->diffInSeconds($p->horodatage);
                    $secondes += $duree;

                    $jourNom = $jours[$arrivee->isoWeekday()];
                    $tdDuPointage = 0;

                    foreach ($tdProf->where('jour', $jourNom) as $seance) {
                        $creneau = $creneaux[$seance->creneau_horaire_id] ?? null;
                        if (!$creneau) continue;

                        $debutSeance = $arrivee->copy()->setTimeFromTimeString($creneau->heure_debut);
                        $finSeance = $arrivee->copy()->setTimeFromTimeString($creneau->heure_fin);

                        $recoupement = min($finSeance->timestamp, $p->horodatage->timestamp)
                            - max($debutSeance->timestamp, $arrivee->timestamp);

                        if ($recoupement > 0) {
                            $tdDuPointage += $recoupement;
                        }
                    }

                    // Le TD ne peut jamais dépasser la durée du pointage
                    $secondesTd += min($tdDuPointage, $duree);
                    $arrivee = null;
                }
            }

            $heures = round($secondes / 3600, 2);
            $heuresTd = round($secondesTd / 3600, 2);
            $heuresCours = round($heures - $heuresTd, 2);
            $montant = round($heures * $taux, 2);
            $datesTravaillees = $prof->pointages
                ->pluck('horodatage')
                ->map(fn($h) => $h->format('d/m'))
                ->unique()
                ->values();

            return [
                'id' => $prof->id,
                'nom' => $prof->nom,
                'prenom' => $prof->prenom,
                'niveau' => $estLycee ? 'Lycée' : 'Collège',
                'dates' => $datesTravaillees->implode(', '),
                'heures_cours' => $heuresCours,
                'heures_td' => $heuresTd,
                'heures' => $heures, // total = cours + TD
                'taux' => $taux,
                'montant' => $montant,

            ];
        });

        return Inertia::render('Pointages/Recap', [
            'professeurs' => $resultats,
            'periode' => $periode,
            'date' => $date,
        ]);
    }

    public function gestion(Request $request)
    {
        $professeurId = $request->query('professeur_id');
        $date = $request->query('date', now()->toDateString());

        $professeurs = \App\Models\Professeur::orderBy('nom')->get(['id', 'nom', 'prenom']);

        $pointages = [];
        if ($professeurId) {
            $pointages = Pointage::where('professeur_id', $professeurId)
                ->whereDate('horodatage', $date)
                ->orderBy('horodatage')
                ->get();
        }

        return Inertia::render('Pointages/Gestion', [
            'professeurs' => $professeurs,
            'professeurId' => $professeurId,
            'date' => $date,
            'pointages' => $pointages,
        ]);
    }

    public function ajouter(Request $request)
    {
        $data = $request->validate([
            'professeur_id' => 'required|exists:professeurs,id',
            'type' => 'required|in:arrivee,depart',
            'date' => 'required|date',
            'heure' => 'required',
        ]);

        Pointage::create([
            'professeur_id' => $data['professeur_id'],
            'type' => $data['type'],
            'horodatage' => $data['date'] . ' ' . $data['heure'],
        ]);

        return back()->with('success', 'Pointage ajouté avec succès.');
    }

    public function modifier(Request $request, Pointage $pointage)
    {
        $data = $request->validate([
            'type' => 'required|in:arrivee,depart',
            'heure' => 'required',
        ]);

        $pointage->update([
            'type' => $data['type'],
            'horodatage' => $pointage->horodatage->format('Y-m-d') . ' ' . $data['heure'],
        ]);

        return back()->with('success', 'Pointage modifié avec succès.');
    }

    public function supprimer(Pointage $pointage)
    {
        $pointage->delete();

        return back()->with('success', 'Pointage supprimé avec succès.');
    }
}
