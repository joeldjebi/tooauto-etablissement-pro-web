<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Etablissement;

class CheckEtablissement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {
    //     $user = Auth::user();
    
    //     // Vérifie si l'utilisateur doit enregistrer un établissement et s'il est sur une autre page que `etablissement.create`
    //     if ($user->role === '01' && !Etablissement::where('professionnel_id', $user->id)->exists() && !$request->routeIs('etablissement.create')) {
    //         session()->flash('type', 'alert-danger');
    //         session()->flash('message', "Veuillez enregistrer votre établissement.");
    //         return redirect()->route('etablissement.create');
    //     }
    
    //     return $next($request);
    // }
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === '01' && !$request->routeIs(['etablissement.create', 'etablissement.store'])) {
            // Vérifie si l'utilisateur n'a pas encore d'établissement
            $hasEtablissement = Etablissement::where('professionnel_id', $user->id)->exists();
            
            if (!$hasEtablissement) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Veuillez enregistrer votre établissement.");
                return redirect()->route('etablissement.create');
            }
        }

        return $next($request);
    }

    
    
}