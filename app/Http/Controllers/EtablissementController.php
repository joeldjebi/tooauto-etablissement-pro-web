<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Commune;
use App\Models\Type_de_prestation;
use App\Models\Forfait_pro;
use App\Models\Type_etablissement;
use App\Models\Abonnement_pro;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EtablissementController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] ='Enregistrez votre établissement';
        $data['menu'] ='etablissement';

        $data['pays'] = Pays::all();
        $data['villes'] = Ville::all();
        $data['communes'] = Commune::all();
        $data['typeEtablissements'] = Type_etablissement::all();
        $data['typeDePrestations'] = Type_de_prestation::all();

        $data['user'] = Auth::user();

        return view('etablissement.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeEtablissement(Request $request)
    {
        // dd($request->all());
        // Validation de la requête d'enregistrement de l'établissement
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:etablissements,mobile',
            'email' => 'required|email|max:255|unique:etablissements,email',
            'adresse' => 'required|string|max:255',
            'adresse_map' => 'nullable|string|max:255',
            'longitude' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
            'pays_id' => 'required|integer|exists:pays,id',
            'ville_id' => 'required|integer|exists:villes,id',
            'commune_id' => 'required|integer|exists:communes,id',
            'type_etablissement_id' => 'required|integer|exists:type_etablissements,id',
            'specialite' => 'required|string',
            'service_mobile' => 'required|string',
            'description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
            'is_whatsapp' => 'required|string',
            'mobile_fix' => 'nullable|string',
            'type_de_prestation' => 'required|array',
            'type_de_prestation.*' => 'integer',
        ]);
        if (Etablissement::where('mobile', $request->mobile)->exists()) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Le numéro de téléphone est déjà utilisé.");
            return back();
        }

        DB::beginTransaction();
        try {

            // Conversion des données en tableau d'entiers
            $typeDePrestation = array_map('intval', $request->type_de_prestation);
            $type_de_prestations = json_encode($typeDePrestation, JSON_THROW_ON_ERROR);

            $user = Auth::user();

            // Gestion des images
            $logoPath = $coverPath = null;

            if($request->file('logo')) {
                $logoPath = $this->wasabiService->uploadFile(
                    $request->file('logo'),
                    config('wasabi.etablissement_logo_directory', 'etablissement/logo'),
                    'logo'
                );
            }

            if($request->file('cover')) {
                $coverPath = $this->wasabiService->uploadFile(
                    $request->file('cover'),
                    config('wasabi.etablissement_cover_directory', 'etablissement/cover'),
                    'cover'
                );
            }

            // $categorieService = 0;
            // if ($request->type_etablissement == 1) {
            //     $categorieService = 3;
            // }elseif ($request->type_etablissement == 2 || $request->type_etablissement == 3 || $request->type_etablissement == 4) {
            //     $categorieService = 2;
            // }elseif ($request->type_etablissement == 5) {
            //     $categorieService = 5;
            // }
            $typeEtablissementToCategorie = [
                1 => 3,
                2 => 2,
                3 => 2,
                4 => 2,
                5 => 5,
                6 => 2,
                7 => 2,
            ];

            $categorieService = $typeEtablissementToCategorie[$request->type_etablissement] ?? 0;


            // Création de l'établissement
            $etablissement = Etablissement::create([
                'name' => html_entity_decode($request->name),
                'mobile' => html_entity_decode($request->mobile),
                'email' => html_entity_decode($request->email),
                'description' => html_entity_decode($request->description),
                'logo' => $logoPath,
                'cover' => $coverPath,
                'adresse' => html_entity_decode($request->adresse),
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'professionnel_id' => $user->id,
                'pays_id' => $request->pays_id,
                'ville_id' => $request->ville_id,
                'commune_id' => $request->commune_id,
                'type_etablissement_id' => $request->type_etablissement_id,
                'specialite' => $request->specialite,
                'categorie_service_id' => $categorieService,
                'statut' => 1,
                'is_whatsapp' => $request->is_whatsapp,
                'mobile_fix' => $request->mobile_fix,
                'type_de_prestations' => $type_de_prestations,
                'service_mobile' => $request->service_mobile,
            ]);

            // Récupération du forfait "free" et création de l'abonnement
            $forfaitFree = Forfait_pro::where('nom', 'Free')->first();
            if (!$forfaitFree) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Le forfait 'Free' est introuvable.");
                return back();
            }

            $dateDebut = Carbon::now();
            $dateFin = $dateDebut->copy()->addMonths($forfaitFree->duree);

            Abonnement_pro::create([
                'etablissement_id' => $etablissement->id,
                'forfait_pro_id' => $forfaitFree->id,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
            ]);

            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Établissement créé avec succès avec un abonnement Free.");
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la création de l'établissement : " . $e->getMessage());
            return back();
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Etablissement $etablissement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $data['title'] ='Modifier votre établissement';
        $data['menu'] ='etablissement-edit';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }
        if ($data['user']->role != '01') {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Votre profil ne vous permet pas mettre a jour les paramètres de l'établissement.");
            return back();
        }

        $data['etablissement'] = Etablissement::where('professionnel_id', $data['user']->id)->first();

        if (empty($data['etablissement'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }

        $data['pays'] = Pays::all();
        $data['villes'] = Ville::all();
        $data['communes'] = Commune::all();
        $data['typeEtablissements'] = Type_etablissement::all();
        $data['typeDePrestations'] = Type_de_prestation::all();
        $data['logoUrl'] = $this->etablissementImageUrl($data['etablissement']->logo, 'etablissement/logo');
        $data['coverUrl'] = $this->etablissementImageUrl($data['etablissement']->cover, 'etablissement/cover');

        return view('etablissement.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, $id)
    {
        // Récupération de l'établissement
        $etablissement = Etablissement::find($id);
        if (!$etablissement) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Établissement introuvable.");
            return back();
        }

        // Validation de la requête d'enregistrement de l'établissement
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:etablissements,mobile,' . $id . ',id',
            'email' => 'required|email|max:255|unique:etablissements,email,' . $id . ',id',
            'adresse' => 'required|string|max:255',
            'adresse_map' => 'nullable|string|max:255',
            'longitude' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
            'pays_id' => 'required|integer|exists:pays,id',
            'ville_id' => 'required|integer|exists:villes,id',
            'commune_id' => 'required|integer|exists:communes,id',
            'type_etablissement_id' => 'required|integer|exists:type_etablissements,id',
            'specialite' => 'required|string',
            'description' => 'required|string',
            'is_whatsapp' => 'required|string',
            'mobile_fix' => 'nullable|string',
            'service_mobile' => 'required|string',
            'type_de_prestation' => 'required|array',
            'type_de_prestation.*' => 'integer',
            'logo' => $etablissement->logo ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048' : 'required|image|mimes:jpeg,png,jpg,gif|max:8048',
            'cover' => $etablissement->cover ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048' : 'required|image|mimes:jpeg,png,jpg,gif|max:8048',
        ]);

        // Conversion des données en tableau d'entiers
        $typeDePrestation = array_map('intval', $request->type_de_prestation);
        $type_de_prestations = json_encode($typeDePrestation, JSON_THROW_ON_ERROR);

        DB::beginTransaction();
        try {
            // Gestion des images
            $logoPath = $etablissement->logo;
            $coverPath = $etablissement->cover;

            if ($request->file('logo')) {
                if ($etablissement->logo) {
                    $this->deleteEtablissementImage($etablissement->logo, 'etablissement/logo');
                }

                $logoPath = $this->wasabiService->uploadFile(
                    $request->file('logo'),
                    config('wasabi.etablissement_logo_directory', 'etablissement/logo'),
                    'logo'
                );
            }

            if ($request->file('cover')) {
                if ($etablissement->cover) {
                    $this->deleteEtablissementImage($etablissement->cover, 'etablissement/cover');
                }

                $coverPath = $this->wasabiService->uploadFile(
                    $request->file('cover'),
                    config('wasabi.etablissement_cover_directory', 'etablissement/cover'),
                    'cover'
                );
            }

            // Mise à jour de l'établissement
            $etablissement->update([
                'name' => html_entity_decode($request->name),
                'mobile' => html_entity_decode($request->mobile),
                'email' => html_entity_decode($request->email),
                'description' => html_entity_decode($request->description),
                'logo' => $logoPath,
                'cover' => $coverPath,
                'adresse' => html_entity_decode($request->adresse),
                'adresse_map' => html_entity_decode($request->adresse_map),
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'pays_id' => $request->pays_id,
                'ville_id' => $request->ville_id,
                'commune_id' => $request->commune_id,
                'is_whatsapp' => $request->is_whatsapp,
                'mobile_fix' => $request->mobile_fix,
                'type_etablissement_id' => $request->type_etablissement_id,
                'specialite' => html_entity_decode($request->specialite),
                'statut' => 1,
                'service_mobile' => $request->service_mobile,
                'type_de_prestations' => $type_de_prestations,
            ]);

            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Établissement mis à jour avec succès.");
            return back();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la mise à jour de l'établissement : " . $e->getMessage());
            return back();
        }
    }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etablissement $etablissement)
    {
        //
    }

    protected function etablissementImageUrl(?string $path, string $legacyDirectory): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_contains($path, '/')) {
            return $this->wasabiService->temporaryUrl($path);
        }

        return asset(trim($legacyDirectory, '/') . '/' . $path);
    }

    protected function deleteEtablissementImage(?string $path, string $legacyDirectory): void
    {
        if (empty($path)) {
            return;
        }

        if (str_contains($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            $this->wasabiService->deleteFile($path);
            return;
        }

        $legacyPath = public_path(trim($legacyDirectory, '/') . '/' . $path);

        if (file_exists($legacyPath)) {
            unlink($legacyPath);
        }
    }
}
