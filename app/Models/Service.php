<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    // Définir la table associée au modèle
    protected $table = 'services';

    // Indiquer les champs qui sont assignables en masse
    protected $fillable = [
        'libelle',
        'description',
        'image',
        'amount_min',
        'etablissement_id',
        'created_by',
    ];

    // Si vous utilisez un champ pour l'auto-incrémentation d'id qui n'est pas un entier
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Si vous voulez définir des dates pour les timestamps
    protected $dates = ['created_at', 'updated_at'];

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