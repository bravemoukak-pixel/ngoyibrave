<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementScolarite extends Model
{
    use HasFactory;

    protected $table = 'paiements_scolarite';

    protected $fillable = ['parent_eleve_id', 'mois', 'montant'];

    protected $casts = [
        'mois' => 'date',
    ];

    public function parentEleve()
    {
        return $this->belongsTo(ParentEleve::class, 'parent_eleve_id');
    }
}
