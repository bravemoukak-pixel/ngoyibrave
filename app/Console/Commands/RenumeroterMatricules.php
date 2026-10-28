<?php

namespace App\Console\Commands;

use App\Models\Classe;
use App\Models\Eleve;
use Illuminate\Console\Command;

class RenumeroterMatricules extends Command
{
    protected $signature = 'matricules:renumeroter {--dry-run : Affiche les changements sans les appliquer}';
    protected $description = 'Renumérote les matricules de tous les élèves selon l\'ordre alphabétique (nom, prénom) au sein de chaque classe';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $annee = now()->format('y');

        $classes = Classe::with('niveau')->get();
        $totalModifies = 0;

        foreach ($classes as $classe) {
            $estLycee = $classe->niveau && str_starts_with($classe->niveau->titre, 'Lycée');
            $code = $classe->code_matricule ?? 'X';

            $eleves = Eleve::where('classe_id', $classe->id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();

            if ($eleves->isEmpty()) continue;

            if (!$estLycee) {
                $prefixe = 'DP' . $annee . $code;
                $longueur = 2;
            } else {
                $prefixe = 'DP' . $code . $annee;
                $longueur = 3;
            }

            $this->info("Classe : {$classe->nom} ({$eleves->count()} élèves)");

            $changements = [];
            foreach ($eleves as $index => $eleve) {
                $nouveauNumero = str_pad($index + 1, $longueur, '0', STR_PAD_LEFT);
                $nouveauMatricule = $prefixe . $nouveauNumero;

                if ($eleve->matricule !== $nouveauMatricule) {
                    $this->line("  {$eleve->nom} {$eleve->prenom} : {$eleve->matricule} -> {$nouveauMatricule}");
                    $totalModifies++;
                    $changements[] = ['eleve' => $eleve, 'nouveau' => $nouveauMatricule];
                }
            }

            if (!$dryRun && !empty($changements)) {
                // Passe 1 : matricules temporaires pour éviter les collisions
                foreach ($changements as $i => $c) {
                    $c['eleve']->matricule = 'TEMP_' . $c['eleve']->id . '_' . $i;
                    $c['eleve']->saveQuietly();
                }

                // Passe 2 : matricules définitifs
                foreach ($changements as $c) {
                    $c['eleve']->matricule = $c['nouveau'];
                    $c['eleve']->saveQuietly();
                }
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Mode simulation : {$totalModifies} matricule(s) seraient modifiés. Relance sans --dry-run pour appliquer.");
        } else {
            $this->info("Terminé : {$totalModifies} matricule(s) modifiés.");
        }

        return 0;
    }
}
