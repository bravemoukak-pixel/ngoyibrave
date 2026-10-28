<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreneauHoraire extends Model
{
    protected $table = 'creneaux_horaires'; // nom exact de la table (pluriel irrégulier)

    protected $fillable = ['heure_debut', 'heure_fin', 'libelle', 'ordre', 'type', 'grille'];

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    public function getLibelleFormateAttribute(): string
    {
        $debut = \Carbon\Carbon::parse($this->heure_debut)->format('H:i');
        $fin = \Carbon\Carbon::parse($this->heure_fin)->format('H:i');
        return "{$debut} - {$fin}";
    }
}
