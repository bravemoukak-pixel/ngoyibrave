<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueScolarite extends Model
{
    protected $table = 'historique_scolarite';

    protected $fillable = ['eleve_id', 'classe_id', 'annee_scolaire'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}
