<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'niveau_id', 'code_matricule'];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class);
    }

    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class, 'classe_professeur');
    }
}
