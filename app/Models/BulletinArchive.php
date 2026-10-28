<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinArchive extends Model
{
    protected $table = 'bulletins_archives';

    protected $fillable = ['eleve_id', 'trimestre', 'annee_scolaire', 'chemin_fichier', 'date_signature'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}
