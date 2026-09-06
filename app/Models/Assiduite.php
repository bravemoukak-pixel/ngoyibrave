<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assiduite extends Model
{
    protected $fillable = [
        'eleve_id', 'trimestre',
        'absences_justifiees', 'absences_injustifiees',
        'retards_justifies', 'retards_injustifies',
        'avertissements', 'blame',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}