<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'matiere',
        'telephone',
        'email',
        'pin',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'classe_professeur');
    }
    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }
    public function lignesPaie()
    {
        return $this->hasMany(LignePaie::class);
    }
    protected static function booted()
    {
        static::creating(function ($professeur) {
            if (empty($professeur->pin)) {
                $professeur->pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
