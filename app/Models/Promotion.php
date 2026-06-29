<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'mobile',
        'date_debut',
        'date_fin',
        'image',
        'statut',
        'description',
        'etablissement_id',
        'created_by',
    ];

    // Définir la relation avec le modèle Etablissement
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }

    // Définir la relation avec le modèle User pour le champ created_by
    public function createdBy()
    {
        return $this->belongsTo(Professionnel::class, 'created_by');
    }
}