<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Etablissement;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ArticleController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des articles';
        $data['menu'] ='articles';

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

        $data['articles'] = Article::where('etablissement_id', $data['etablissement']->id)
            ->with('etablissement')
            ->get()
            ->map(function ($article) {
                $article->image_url = $this->articleImageUrl($article->image);

                return $article;
            });

        return view('article.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount' => 'nullable|string|max:255',
        ]);

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

        $imagePath = null;
    
        if($request->file('image')) {
            $imagePath = $this->wasabiService->uploadFile(
                $request->file('image'),
                config('wasabi.article_image_directory', 'articles/image'),
                'article'
            );
        }

        Article::create([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'image' => $imagePath,
            'amount' => $request->amount,
            'etablissement_id' => $data['etablissement']->id,
            'created_by' => Auth::id(),
        ]);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Article créé avec succès.");
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount' => 'nullable|string|max:255',
        ]);
    
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
    
        // Recherche du article
        $article = Article::find($id);
        if (empty($article)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "article introuvable.");
            return back();
        }
    
        // Gestion de l'image
        if($request->hasFile('image')) {
            $this->deleteArticleImage($article->image);

            $article->image = $this->wasabiService->uploadFile(
                $request->file('image'),
                config('wasabi.article_image_directory', 'articles/image'),
                'article'
            );
        }
    
        // Mise à jour des champs
        $article->update([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'amount' => $request->amount,
            'etablissement_id' => $etablissement->id,
            'created_by' => Auth::id(),
        ]);
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "Article mis à jour avec succès.");
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
    
        $article = Article::find($id);
    
        if (empty($article)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "article introuvable.");
            return back();
        }
    
        $this->deleteArticleImage($article->image);
    
        $article->delete();
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "article supprimé avec succès.");
        return back();
    }

    protected function articleImageUrl(?string $path): ?string
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

        return asset('articles/image/' . $path);
    }

    protected function deleteArticleImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_contains($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            $this->wasabiService->deleteFile($path);
            return;
        }

        $legacyPath = public_path('articles/image/' . $path);

        if (file_exists($legacyPath)) {
            unlink($legacyPath);
        }
    }
}
