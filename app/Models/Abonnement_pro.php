<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement_pro extends Model
{
    use HasFactory;

    protected $fillable = ['etablissement_id', 'forfait_id', 'date_debut', 'date_fin', 'forfait_pro_id'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function forfait()
    {
        return $this->belongsTo(Forfait_pro::class, 'forfait_pro_id');
    }
}