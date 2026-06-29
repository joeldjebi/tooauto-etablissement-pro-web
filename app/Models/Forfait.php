<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forfait extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'duree', 'prix'];

    public function abonnement_pros()
    {
        return $this->hasMany(Abonnement_pro::class);
    }
}