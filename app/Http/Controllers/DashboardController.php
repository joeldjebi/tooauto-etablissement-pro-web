<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professionnel;
use App\Models\Annonce_etablissement;
use App\Models\Article;
use App\Models\Usager;
use App\Models\Service;
use App\Models\Annonce;
use App\Models\Etablissement;
use App\Services\WasabiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {}

    /**
     * Display a listing of the resource.
     */
    /*public function dashboard()
    {
        $data['title'] = 'Tableau de bord';
        $data['menu'] = 'dashboard';
    
        // Récupérer l'utilisateur connecté
        $data["user"] = Professionnel::where([
            'id' => auth()->user()->id,
            'role' => '01'
        ])->first();
    
        $data['etablissement'] = Etablissement::where('professionnel_id', $data['user']->id)
            ->orWhere('professionnel_id', $data['user']->created_by)
            ->first();
    
        if (empty($data['etablissement'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }
    
        // Vérification de l'existence de l'établissement de l'utilisateur
        $professionnel = auth()->user(); 
    
        // Vérification de l'existence de l'établissement de l'utilisateur
        $etablissement = $professionnel->etablissement ?: Etablissement::where('professionnel_id', $professionnel->created_by)
            ->orWhere('professionnel_id', $professionnel->id)
            ->first();
    
        if (!$etablissement) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }
    
        // Récupérer les articles liés à l'établissement
        $data['articles'] = $etablissement->articles()->get(); // Relation directe depuis l'établissement
        $data['articleCount'] = $data['articles']->count(); // Compter les articles directement
    
        // Récupérer les services associés à l'établissement
        $data['serviceCount'] = $etablissement->services()->count();
    
		if ($etablissement) {
			// Récupérer les annonces visibles pour cet établissement
			$data['annonces'] = Annonce::query()
				->where('type_etablissement_id', $etablissement->type_etablissement_id) // Correspondance des types d'établissement
				->where('statut', 1) // Statut actif
				->whereHas('etablissements', function ($query) use ($etablissement) {
					// Filtrer les annonces associées à cet établissement
					$query->where('etablissement_id', $etablissement->id)
						  ->where('is_visible', true); // Filtrer uniquement les annonces visibles
				})
				->with('usager') // Charger la relation 'usager'
				->get();

			// Si aucune annonce n'est associée à l'établissement
			if ($data['annonces']->isEmpty()) {
				// Récupérer toutes les annonces
				$annonces = Annonce::all();

				// Associer chaque annonce à l'établissement
				foreach ($annonces as $annonce) {
					DB::table('annonce_etablissements')->insert([
						'etablissement_id' => $etablissement->id,
						'annonce_id' => $annonce->id,
						'is_visible' => true, // Annonce visible par défaut
						'created_at' => now(),
						'updated_at' => now(),
					]);
				}

				// Recharger les annonces après l'insertion
				$data['annonces'] = Annonce::query()
					->where('type_etablissement_id', $etablissement->type_etablissement_id)
					->where('statut', 1)
					->whereHas('etablissements', function ($query) use ($etablissement) {
						$query->where('etablissement_id', $etablissement->id)
							  ->where('is_visible', true);
					})
					->with('usager')
					->get();
			}
		} else {
			$data['annonces'] = collect(); // Retourner une collection vide si aucun établissement n'est trouvé
		}
    
        $data['annonceCount'] = $data['annonces']->count();
    
        // Retourner la vue avec les données
        return view('dashboard', $data);
    }*/
	
	public function dashboard()
	{
		$data['title'] = 'Tableau de bord';
		$data['menu'] = 'dashboard';

		// Récupérer l'utilisateur connecté
		$user = auth()->user();
		$data["user"] = Professionnel::where([
			'id' => $user->id,
			'role' => '01'
		])->first();

		// Vérification et récupération de l'établissement (solution unifiée)
		$etablissement = null;

		// Récupérer via la relation directe si elle existe
		if (method_exists($user, 'etablissement') && $user->etablissement) {
			$etablissement = $user->etablissement;
		} else {
			// Sinon, rechercher dans la base de données
			$etablissement = Etablissement::where('professionnel_id', $user->id)
				->orWhere('professionnel_id', $user->created_by)
				->first();
		}

		// Stocker l'établissement dans les données
		$data['etablissement'] = $etablissement;

		// Vérifier si l'établissement existe
		if (!$etablissement) {
			session()->flash('type', 'alert-danger');
			session()->flash('message', "L'établissement est introuvable.");
			return back();
		}

		// Récupérer les articles liés à l'établissement
		$data['articles'] = $etablissement->articles()->get();
		$data['articleCount'] = $data['articles']->count();

		// Récupérer les services associés à l'établissement
		$data['serviceCount'] = $etablissement->services()->count();

		// Récupérer les annonces visibles pour cet établissement
		$data['annonces'] = Annonce::query()
			->where('type_etablissement_id', $etablissement->type_etablissement_id)
			->where('statut', 1)
			// ->whereHas('etablissements', function ($query) use ($etablissement) {
			// 	$query->where('etablissement_id', $etablissement->id)
			// 		  ->where('is_visible', true);
			// })
			->with('usager')
            ->orderBy('created_at', 'desc')
			->get();

		$data['annonces'] = $this->attachAnnonceImageUrls($data['annonces']);

		// dd($data['annonces']);

		// Si aucune annonce n'est associée à l'établissement
		if ($data['annonces']->isEmpty()) {
			// Récupérer toutes les annonces
			$annonces = Annonce::all();

			// Associer chaque annonce à l'établissement
			foreach ($annonces as $annonce) {
				DB::table('annonce_etablissements')->insert([
					'etablissement_id' => $etablissement->id,
					'annonce_id' => $annonce->id,
					'is_visible' => true,
					'created_at' => now(),
					'updated_at' => now(),
				]);
			}

			// Recharger les annonces après l'insertion
			$data['annonces'] = Annonce::query()
				->where('type_etablissement_id', $etablissement->type_etablissement_id)
				->where('statut', 1)
				->whereHas('etablissements', function ($query) use ($etablissement) {
					$query->where('etablissement_id', $etablissement->id)
						  ->where('is_visible', true);
				})
				->with('usager')
                ->orderBy('created_at', 'desc')
				->get();

			$data['annonces'] = $this->attachAnnonceImageUrls($data['annonces']);
		}

		$data['annonceCount'] = $data['annonces']->count();

        // dd($data['annonces']);

		// Retourner la vue avec les données
		return view('dashboard', $data);
	}
    


    // Masquer une annonce pour un établissement
    public function hideAnnonce(Request $request)
    {
        $etablissementId = $request->etablissement_id;
        $annonceId = $request->annonce_id;

        // Mettre à jour la visibilité de l'annonce
        DB::table('annonce_etablissements')
            ->where('etablissement_id', $etablissementId)
            ->where('annonce_id', $annonceId)
            ->update(['is_visible' => false]);

        return response()->json(['success' => true]);
    }

    protected function attachAnnonceImageUrls($annonces)
    {
        return $annonces->map(function ($annonce) {
            $annonce->image_url = $this->annonceImageUrl($annonce);

            return $annonce;
        });
    }

    protected function annonceImageUrl($annonce): ?string
    {
        if (!$annonce || empty($annonce->image)) {
            return null;
        }

        $image = trim((string) $annonce->image);

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (str_contains($image, '/')) {
            try {
                return $this->wasabiService->temporaryUrl($image) ?: $this->wasabiPublicUrl($image);
            } catch (\Throwable $e) {
                return $this->wasabiPublicUrl($image);
            }
        }

        $baseUrl = empty($annonce->gestionnaire_de_flotte_id)
            ? env('URL_API_USAGER')
            : env('URL_API_FLOTTE');

        return rtrim((string) $baseUrl, '/') . '/images/annonce/' . ltrim($image, '/');
    }

    protected function wasabiPublicUrl(string $path): string
    {
        return rtrim((string) config('wasabi.url'), '/') . '/' . ltrim($path, '/');
    }


    /**
     * Display a listing of the resource.
     */
    public function profil()
    {
        $data['title'] ='Mon profil';
        $data['menu'] ='profil';

        $data["user"] = Professionnel::where([
            'id' => auth()->user()->id,
            // 'role' => '01'
        ])->first();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }
        
        return view('profil.index',$data);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nom' => 'required|min:3',
            'prenoms' => 'required|min:3',
            'email' => 'required',
        ]);


        $user = Professionnel::where([
            'id' => auth()->user()->id,
        ])->first();

        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }

        $user->nom = html_entity_decode($request->nom);
        $user->prenoms = html_entity_decode($request->prenoms);
        $user->email = html_entity_decode($request->email);
      

        if($user->save()){
			session()->flash('type', 'alert-success');
			session()->flash('message', 'Modifiée avec succès');
			return back();
		}else{
			session()->flash('type', 'alert-danger');
			session()->flash('message', 'Erreur veuillez réessayer');
			return back();
		}
    }

           /**
     * mise à jour du mot de passe
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatepassword(Request $request)
    {
        // Validation des champs
        $request->validate([
            'oldpassword' => 'required|string|max:255',
            'newpassword' => 'required|string|min:6|max:255',  
            'confirmpassword' => 'required|same:newpassword',  
        ], [
            'oldpassword.required' => 'L\'ancien mot de passe est requis.',
            'oldpassword.string' => 'L\'ancien mot de passe doit être une chaîne de caractères.',
            'newpassword.required' => 'Le nouveau mot de passe est requis.',
            'newpassword.string' => 'Le nouveau mot de passe doit être une chaîne de caractères.',
            'newpassword.min' => 'Le nouveau mot de passe doit contenir au moins 6 caractères.',
            'confirmpassword.required' => 'La confirmation du mot de passe est requise.',
            'confirmpassword.same' => 'Le mot de passe de confirmation doit correspondre au nouveau mot de passe.',
        ]);
    
        // Récupérer l'utilisateur authentifié
        $user = professionnel::where('id', auth::user()->id)->first();
        
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Utilisateur introuvable');
            return back()->withInput($request->all());
        }
    
        // Vérification du mot de passe actuel
        if (Hash::check($request->oldpassword, $user->password)) {
            // Mettre à jour le mot de passe
            $user->password = Hash::make($request->newpassword);
            $user->save();
    
            session()->flash('type', 'alert-success');
            session()->flash('message', 'Mot de passe modifié avec succès');
            return back();
        }
    
        // Si le mot de passe actuel ne correspond pas
        session()->flash('type', 'alert-danger');
        session()->flash('message', 'Ancien mot de passe incorrect');
        return back()->withInput($request->all());
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/');
    }

     // Méthode pour basculer la visibilité de l'annonce pour un établissement
     public function toggleVisibility($annonceId)
     {
         $etablissement = auth()->user()->etablissement;  // Récupère l'établissement de l'utilisateur connecté
         $annonce = Annonce::findOrFail($annonceId);
 
         // Vérifie si l'annonce est déjà associée à cet établissement
         $pivot = $etablissement->annonces()->where('annonces.id', $annonceId)->first();
 
         // Si l'annonce existe déjà, bascule la visibilité
         if ($pivot) {
             $isVisible = !$pivot->pivot->is_visible;
             $etablissement->annonces()->updateExistingPivot($annonceId, ['is_visible' => $isVisible]);
         }
 
         return redirect()->back()->with('success', 'Visibilité de l\'annonce mise à jour.');
     }

}
