<?php

namespace App\Http\Controllers;

use App\Models\Forfait_pro;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ForfaitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des forfaits';
        $data['menu'] ='forfaits';

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

        $data['forfaits'] = Forfait_pro::where('statut', 1)->get();

        return view('forfait.index', $data);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Forfait_pro $forfait)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Forfait_pro $forfait)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Forfait_pro $forfait)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Forfait_pro $forfait)
    {
        //
    }
}