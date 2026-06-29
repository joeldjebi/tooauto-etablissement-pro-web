<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Abonnement_pro;

class Forfait_pro extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle', 
        'description', 
        'prix', 
        'duree_jours',
        'statut'
    ];

    public function abonnement_pros()
    {
        return $this->hasMany(Abonnement_pro::class, 'forfait_pro_id');
    }
}