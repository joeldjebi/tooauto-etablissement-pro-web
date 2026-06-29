<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;

    protected $table = 'etablissements';

    // Définition des champs remplissables
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'description',
        'logo',
        'cover',
        'adresse',
        'adresse_map',
        'longitude',
        'latitude',
        'professionnel_id',
        'pays_id',
        'ville_id',
        'commune_id',
        'statut',
        'type_etablissement_id',
        'specialite',
        'categorie_service_id',
        'is_whatsapp',
        'mobile_fix',
		'type_de_prestations',
		'service_mobile'
    ];

    // Relation avec le modèle Pays
    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    // Relation avec le modèle Ville
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    // Relation avec le modèle Commune
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    // Relation avec l'utilisateur (professionnel)
    public function professionnel()
    {
        return $this->belongsTo(Professionnel::class, 'professionnel_id');
    }

    public function abonnement_pros()
    {
        return $this->hasMany(Abonnement_pro::class);
    }

    protected static function booted()
    {
        static::created(function ($etablissement) {
            $forfaitGratuit = Forfait::where('nom', 'Gratuit')->first();
            if ($forfaitGratuit) {
                $dateDebut = now();
                $dateFin = now()->addMonths($forfaitGratuit->duree);

                $etablissement->abonnement_pros()->create([
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'forfait_id' => $forfaitGratuit->id,
                ]);
            }
        });
    }

    public function annonces()
    {
        return $this->belongsToMany(Annonce::class, 'annonce_etablissements')
                    ->withPivot('is_visible')
                    ->withTimestamps();
    }

    // Définir la relation avec le modèle Article
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

     // Si vous avez aussi une relation avec les services, vous pouvez faire pareil
     public function services()
     {
         return $this->hasMany(Service::class);
     }

     
 
}