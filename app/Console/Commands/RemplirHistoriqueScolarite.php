<?php

namespace App\Console\Commands;

use App\Models\Eleve;
use App\Models\HistoriqueScolarite;
use Illuminate\Console\Command;

class RemplirHistoriqueScolarite extends Command
{
    protected $signature = 'historique:remplir';
    protected $description = 'Crée rétroactivement les entrées manquantes dans historique_scolarite pour tous les élèves actuellement inscrits';

    public function handle()
    {
        $anneeScolaire = Eleve::anneeScolaireActuelle();
        $eleves = Eleve::whereNotNull('classe_id')->get();

        $crees = 0;
        $dejaExistants = 0;

        foreach ($eleves as $eleve) {
            $existe = HistoriqueScolarite::where('eleve_id', $eleve->id)
                ->where('annee_scolaire', $anneeScolaire)
                ->exists();

            if ($existe) {
                $dejaExistants++;
                continue;
            }

            HistoriqueScolarite::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $eleve->classe_id,
                'annee_scolaire' => $anneeScolaire,
            ]);

            $crees++;
        }

        $this->info("Terminé. {$crees} entrées créées, {$dejaExistants} existaient déjà pour l'année {$anneeScolaire}.");

        return 0;
    }
}
