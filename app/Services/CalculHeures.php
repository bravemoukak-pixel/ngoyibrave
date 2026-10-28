<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CalculHeures
{
    /**
     * Répartit les heures pointées entre cours et TD.
     * $pointages : pointages du professeur (triés par horodatage)
     * $tdProf    : séances TD du professeur (colonnes jour, creneau_horaire_id), sans doublon
     * $creneaux  : tous les créneaux horaires, indexés par id
     */
    public static function repartir(Collection $pointages, Collection $tdProf, Collection $creneaux): array
    {
        $jours = [1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 7 => 'dimanche'];

        $secondes = 0;
        $secondesTd = 0;
        $arrivee = null;

        foreach ($pointages as $p) {
            if ($p->type === 'arrivee') {
                $arrivee = $p->horodatage;
            } elseif ($p->type === 'depart' && $arrivee) {
                $duree = $arrivee->diffInSeconds($p->horodatage);
                $secondes += $duree;

                $tdDuPointage = 0;

                foreach ($tdProf->where('jour', $jours[$arrivee->isoWeekday()]) as $seance) {
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

                $secondesTd += min($tdDuPointage, $duree);
                $arrivee = null;
            }
        }

        $heures = round($secondes / 3600, 2);
        $heuresTd = round($secondesTd / 3600, 2);

        return [
            'heures' => $heures,
            'heures_td' => $heuresTd,
            'heures_cours' => round($heures - $heuresTd, 2),
        ];
    }
}
