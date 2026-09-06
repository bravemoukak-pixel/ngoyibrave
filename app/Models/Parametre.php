<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = ['taux_horaire_college', 'taux_horaire_lycee', 'telephone_ecole_1', 'telephone_ecole_2'];
}
