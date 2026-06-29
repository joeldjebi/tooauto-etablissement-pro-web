<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usager extends Model
{
    use HasFactory;

    // Définir la relation avec le modèle annonce
    public function annonce()
    {
        return $this->belongsTo(Annonce::class, 'annonce_id');
    }

}