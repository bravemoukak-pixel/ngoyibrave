<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = ['matricule', 'nom', 'prenom', 'sexe', 'date_naissance', 'lieu_naissance', 'adresse', 'classe_id', 'parent_eleve_id', 'montant_annuel'];
    protected $casts = [
        'date_naissance' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($eleve) {
            $classe = \App\Models\Classe::with('niveau')->find($eleve->classe_id);
            $estLycee = $classe && $classe->niveau && str_starts_with($classe->niveau->titre, 'Lycée');
            $code = $classe->code_matricule ?? 'X';
            $annee = now()->format('y');

            if (!$estLycee) {
                $prefixe = 'DP' . $annee . $code;
                $max = self::where('classe_id', $eleve->classe_id)
                    ->where('matricule', 'like', $prefixe . '%')
                    ->get()
                    ->map(fn($e) => (int) substr($e->matricule, -2))
                    ->max() ?? 0;
                $eleve->matricule = $prefixe . str_pad($max + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $prefixe = 'DP' . $code . $annee;
                $max = self::where('classe_id', $eleve->classe_id)
                    ->where('matricule', 'like', $prefixe . '%')
                    ->get()
                    ->map(fn($e) => (int) substr($e->matricule, -3))
                    ->max() ?? 0;
                $eleve->matricule = $prefixe . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public static function infoMatriculeClasse($nomClasse)
    {
        $nom = mb_strtolower(trim($nomClasse));
        $nom = str_replace(['é', 'è', 'ê', 'ᵉ'], 'e', $nom);

        $codes = [
            '6e' => ['type' => 'college', 'code' => '6'],
            '5e' => ['type' => 'college', 'code' => '5'],
            '4e' => ['type' => 'college', 'code' => '4'],
            '3e' => ['type' => 'college', 'code' => '3'],
            'seconde a' => ['type' => 'lycee', 'code' => 'SA'],
            'seconde c' => ['type' => 'lycee', 'code' => 'SC'],
            'premiere a' => ['type' => 'lycee', 'code' => 'PA'],
            'premiere c' => ['type' => 'lycee', 'code' => 'PC'],
            'premiere d' => ['type' => 'lycee', 'code' => 'PD'],
            'terminale a' => ['type' => 'lycee', 'code' => 'TA'],
            'terminale c' => ['type' => 'lycee', 'code' => 'TC'],
            'terminale d' => ['type' => 'lycee', 'code' => 'TD'],
        ];

        return $codes[$nom] ?? ['type' => 'college', 'code' => 'X'];
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
    public function parentEleve()
    {
        return $this->belongsTo(ParentEleve::class, 'parent_eleve_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function assiduites()
    {
        return $this->hasMany(Assiduite::class);
    }

    public function moyenne($matiere, $trimestre)
    {
        $notes = $this->notes()
            ->where('matiere', $matiere)
            ->where('trimestre', $trimestre)
            ->get();

        $d1 = $notes->firstWhere('type_evaluation', 'Devoir 1')?->note;
        $d2 = $notes->firstWhere('type_evaluation', 'Devoir 2')?->note;
        $composition = $notes->firstWhere('type_evaluation', 'Composition')?->note;

        if (\App\Http\Controllers\NoteController::estPrimaire($this->classe->nom)) {
            $valeurs = collect([$d1, $d2, $composition])->filter(fn($v) => $v !== null);
            return $valeurs->isNotEmpty() ? round($valeurs->avg(), 2) : null;
        }

        // Collège / Lycée : logique inchangée
        $devoirs = collect([$d1, $d2])->filter(fn($v) => $v !== null);
        $moyenneDevoirs = $devoirs->isNotEmpty() ? $devoirs->avg() : null;

        if ($moyenneDevoirs === null && $composition === null) {
            return null;
        }
        if ($moyenneDevoirs === null) {
            return round($composition, 2);
        }
        if ($composition === null) {
            return round($moyenneDevoirs, 2);
        }
        return round(($moyenneDevoirs + $composition) / 2, 2);
    }
    public function moyenneEvaluationPrimaire($typeEvaluation, $trimestre, $coefficients)
    {
        $notes = $this->notes()
            ->where('trimestre', $trimestre)
            ->where('type_evaluation', $typeEvaluation)
            ->get();

        $sommeNotes = 0;
        $sommeCoef = 0;

        foreach ($coefficients as $matiere => $coef) {
            $note = $notes->firstWhere('matiere', $matiere);
            if ($note !== null) {
                $sommeNotes += $note->note;
                $sommeCoef += $coef;
            }
        }

        return $sommeCoef > 0 ? round($sommeNotes / $sommeCoef, 2) : null;
    }

    public function moyenneTrimestriellePrimaire($trimestre, $coefficients)
    {
        $m1 = $this->moyenneEvaluationPrimaire('Devoir 1', $trimestre, $coefficients);
        $m2 = $this->moyenneEvaluationPrimaire('Devoir 2', $trimestre, $coefficients);
        $m3 = $this->moyenneEvaluationPrimaire('Composition', $trimestre, $coefficients);

        $valeurs = collect([$m1, $m2, $m3])->filter(fn($m) => $m !== null);

        return $valeurs->isNotEmpty() ? round($valeurs->avg(), 2) : null;
    }
}
