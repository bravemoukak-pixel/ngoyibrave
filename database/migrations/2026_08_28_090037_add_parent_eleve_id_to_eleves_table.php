<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentEleve extends Model
{
    use HasFactory;

    protected $table = 'parent_eleves';

    protected $fillable = [
        'nom',
        'prenom',
        'fonction',
        'lien_parente',
        'telephone',
    ];

    public function eleves()
    {
        return $this->hasMany(Eleve::class);
    }
}
