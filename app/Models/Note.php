<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'eleve_id', 'matiere', 'trimestre', 'type_evaluation', 'note', 'note_sur', 'commentaire',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}