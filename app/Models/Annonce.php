<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    // Définir la relation avec le modèle usager
    public function usager()
    {
        return $this->belongsTo(Usager::class, 'usager_id');
    }

	public function etablissements()
	{
		return $this->belongsToMany(Etablissement::class, 'annonce_etablissements')
			->withPivot('is_visible', 'created_at', 'updated_at'); // Ajouter les colonnes nécessaires
	}

}