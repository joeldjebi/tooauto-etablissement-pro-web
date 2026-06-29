<?php

namespace App\Http\Controllers;

use App\Models\Abonnement_pro;
use App\Models\Etablissement;
use App\Models\Forfait_pro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AbonnementProController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des abonnements';
        $data['menu'] ='abonnements';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $data['etablissement'] = Etablissement::where('professionnel_id', $data['user']->id)
        ->orWhere('professionnel_id', $data['user']->created_by)
        ->first();

        if (empty($data['etablissement'] )) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }

        $data['abonnementPros'] = Abonnement_pro::where('etablissement_id', $data['etablissement']->id)
        ->with(['etablissement', 'forfait'])
        ->get();
        $data['forfaits'] = Forfait_pro::all();
    

        $data['abonnement'] = Abonnement_pro::where('etablissement_id', $data['etablissement']->id)->first();

        // Initialiser la variable de jours restants
        $daysRemaining = null;

        if ($data['abonnement']) {
            // Calculer le nombre de jours restants
            $today = Carbon::today();
            $dateFin = Carbon::parse($data['abonnement']->date_fin);

            // Calculer la différence en jours
            $daysRemaining = $today->diffInDays($dateFin, false);

            // Si le nombre de jours est négatif, l'abonnement est expiré
            $data['daysRemaining'] = $daysRemaining > 0 ? $daysRemaining : 0;
        }

        return view('abonnement.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function imprimerRecu($id)
    {
        // Récupérer l'abonnement spécifique par ID
        $abonnement = Abonnement_pro::with('etablissement', 'forfait')->find($id);
        // dd($abonnement);
        
        // Vérifier si l'abonnement existe
        if (!$abonnement) {
            return redirect()->back()->with('message', 'Abonnement non trouvé!');
        }
    
        // Informations sur l'entreprise
        $entreprise = [
            'nom' => env('ENTREPRISE_NOM', 'TOOAUTO PRO'),
            'adresse' => env('ENTREPRISE_ADRESSE', 'Abidjan, Côte d\'Ivoire'),
            'telephone' => env('ENTREPRISE_TELEPHONE', '+225 XX XX XX XX'),
            'email' => env('ENTREPRISE_EMAIL', 'contact@tooauto.com'),
            'logo' => env('ENTREPRISE_LOGO', 'images/logo/logo.png'),
        ];
    
        // Retourner une vue imprimable avec les données de l'abonnement et de l'entreprise
        return view('abonnement.recu_abonnement', compact('abonnement', 'entreprise'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Abonnement_pro $abonnement_pro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Abonnement_pro $abonnement_pro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Abonnement_pro $abonnement_pro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Abonnement_pro $abonnement_pro)
    {
        //
    }
}