<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LignePaie extends Model
{
    use HasFactory;

    protected $table = 'lignes_paie';

    protected $fillable = ['professeur_id', 'mois', 'type', 'libelle', 'montant'];

    protected $casts = [
        'mois' => 'date',
    ];

    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }
}
