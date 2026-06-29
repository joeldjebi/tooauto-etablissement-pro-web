<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Etablissement;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des promotions';
        $data['menu'] ='promotions';

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

        $data['promotions'] = Promotion::where('etablissement_id', $data['etablissement']->id)
            ->with('etablissement')
            ->get()
            ->map(function ($promotion) {
                $promotion->image_url = $this->promotionImageUrl($promotion->image);

                return $promotion;
            });

        return view('promotion.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:200',
            'mobile' => 'required|string|max:20',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // image, types, taille max
            // 'statut' => 'required|in:0,1',
            'description' => 'nullable|string',
        ]);

        // Si la validation échoue, rediriger l'utilisateur avec les erreurs
        if ($validator->fails()) {
            return redirect()->route('promotion.index')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $etablissement = Etablissement::where('professionnel_id', $data['user']->id)
                        ->orWhere('professionnel_id', $data['user']->created_by)
                        ->first();
    
        if (empty($etablissement)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }

        $imagePath = null;
    
        if($request->file('image')) {
            $imagePath = $this->wasabiService->uploadFile(
                $request->file('image'),
                config('wasabi.promotion_image_directory', 'promotions/image'),
                'promotion'
            );
        }

        // Si la validation réussit, on crée la promotion
        Promotion::create([
            'libelle' => $request->libelle,
            'mobile' => $request->mobile,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'image' => $imagePath,
            // 'statut' => $request->statut,
            'description' => $request->description,
            'etablissement_id' => $etablissement->id,
            'created_by' => auth()->id(), // Assurez-vous d'être connecté et que l'ID utilisateur est récupéré
        ]);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Promotion créé avec succès.");
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:200',
            'mobile' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'description' => 'nullable|string',
        ]);
    
        // Si la validation échoue, rediriger l'utilisateur avec les erreurs
        if ($validator->fails()) {
            return redirect()->route('promotion.index', $id)
                        ->withErrors($validator)
                        ->withInput();
        }
    
        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }
    
        $etablissement = Etablissement::where('professionnel_id', $data['user']->id)
                        ->orWhere('professionnel_id', $data['user']->created_by)
                        ->first();
    
        if (empty($etablissement)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'établissement est introuvable.");
            return back();
        }
    
        // Recherche du service
        $promotion = Promotion::findOrFail($id);
        if (empty($promotion)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Promotion introuvable.");
            return back();
        }
    
        // Gestion de l'image (si un fichier est soumis)
        if ($request->hasFile('image')) {
            // Vérifier si le fichier est bien une image
            $image = $request->file('image');
            if (!$image->isValid()) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "L'image téléchargée n'est pas valide.");
                return back();
            }
        
            // Validation de l'extension et de la taille
            $validator = Validator::make($request->all(), [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            if ($validator->fails()) {
                return redirect()->route('promotion.index', $id)
                            ->withErrors($validator)
                            ->withInput();
            }
        
            $this->deletePromotionImage($promotion->image);

            $promotion->image = $this->wasabiService->uploadFile(
                $image,
                config('wasabi.promotion_image_directory', 'promotions/image'),
                'promotion'
            );
        }
        
    
        // Mise à jour de la promotion
        $promotion->update([
            'libelle' => $request->libelle,
            'mobile' => $request->mobile,
            'description' => $request->description,
            // N'oubliez pas de sauvegarder l'image si elle existe
            'image' => $promotion->image, 
        ]);
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "Promotion mise à jour avec succès.");
        return back();
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }
    
        $promotion = Promotion::findOrFail($id);
    
        if (empty($promotion)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Promotion introuvable.");
            return back();
        }
    
        $this->deletePromotionImage($promotion->image);
    
        $promotion->delete();
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "Promotion supprimé avec succès.");
        return back();
    }

    protected function promotionImageUrl(?string $path): ?string
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

        return asset('promotions/image/' . $path);
    }

    protected function deletePromotionImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_contains($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            $this->wasabiService->deleteFile($path);
            return;
        }

        $legacyPath = public_path('promotions/image/' . $path);

        if (file_exists($legacyPath)) {
            unlink($legacyPath);
        }
    }
    
}
