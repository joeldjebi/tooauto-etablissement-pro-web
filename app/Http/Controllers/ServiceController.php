<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des services';
        $data['menu'] ='services';

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

        $data['services'] = Service::where('etablissement_id', $data['etablissement']->id)->with('etablissement')->get();

        return view('service.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount_min' => 'nullable|string|max:255',
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

        $imagePath = null;
    
        if($request->file('image')) {
            $image = $request->file('image');
            $imageName = 'image-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('services/image'), $imageName);
            $imagePath = $imageName;
        }

        Service::create([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'image' => $imagePath,
            'amount_min' => $request->amount_min,
            'etablissement_id' => $etablissement->id,
            'created_by' => Auth::id(),
        ]);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Service créé avec succès.");
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
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
            'amount_min' => 'nullable|string|max:255',
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
    
        // Recherche du service
        $service = Service::find($id);
        if (empty($service)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Service introuvable.");
            return back();
        }
    
        // Gestion de l'image
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'image-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('services/image'), $imageName);
            $service->image = $imageName;
        }
    
        // Mise à jour des champs
        $service->update([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'amount_min' => $request->amount_min,
            'etablissement_id' => $etablissement->id,
            'created_by' => Auth::id(),
        ]);
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "Service mis à jour avec succès.");
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
    
        $service = Service::find($id);
    
        if (empty($service)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Service introuvable.");
            return back();
        }
    
        // Supprime l'image associée, si elle existe
        if ($service->image && file_exists(public_path('services/image/' . $service->image))) {
            unlink(public_path('services/image/' . $service->image));
        }
    
        $service->delete();
    
        session()->flash('type', 'alert-success');
        session()->flash('message', "Service supprimé avec succès.");
        return back();
    }
    
}