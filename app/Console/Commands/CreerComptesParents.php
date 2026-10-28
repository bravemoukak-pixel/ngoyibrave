<?php

namespace App\Console\Commands;

use App\Models\ParentEleve;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreerComptesParents extends Command
{
    protected $signature = 'parents:creer-comptes';
    protected $description = 'Crée un compte de connexion pour chaque parent qui n\'en a pas encore';

    public function handle()
    {
        $parents = ParentEleve::whereDoesntHave('user')->get();

        if ($parents->isEmpty()) {
            $this->info('Tous les parents ont déjà un compte.');
            return;
        }

        foreach ($parents as $parent) {
            if (empty($parent->telephone)) {
                $this->warn("Parent #{$parent->id} ({$parent->nom} {$parent->prenom}) n'a pas de téléphone, ignoré.");
                continue;
            }

            $motDePasse = substr(preg_replace('/\D/', '', $parent->telephone), -4);

            User::create([
                'name' => $parent->nom . ' ' . $parent->prenom,
                'email' => $parent->telephone,
                'password' => Hash::make($motDePasse),
                'role' => 'parent',
                'parent_eleve_id' => $parent->id,
                'email_verified_at' => now(),
            ]);

            $this->info("Compte créé pour {$parent->nom} {$parent->prenom} — identifiant: {$parent->telephone}, mot de passe: {$motDePasse}");
        }

        $this->info('Terminé.');
    }
}
