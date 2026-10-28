<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploiDuTemps extends Model
{
    protected $table = 'emplois_du_temps';

    protected $fillable = [
        'jour',
        'creneau_horaire_id',
        'classe_id',
        'professeur_id',
        'matiere',
        'type_seance',
    ];

    public function creneauHoraire()
    {
        return $this->belongsTo(CreneauHoraire::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }
    private function anneeScolaire(): string
    {
        $mois = now()->month;
        $anneeActuelle = now()->year;

        if ($mois >= 10) {
            return $anneeActuelle . '-' . ($anneeActuelle + 1);
        } elseif ($mois <= 6) {
            return ($anneeActuelle - 1) . '-' . $anneeActuelle;
        } else {
            return $anneeActuelle . '-' . ($anneeActuelle + 1);
        }
    }
}
