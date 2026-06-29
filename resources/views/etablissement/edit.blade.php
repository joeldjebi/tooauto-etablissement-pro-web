@php
    $tableTypePrestation = json_decode($etablissement->type_de_prestations, true);
@endphp


@include('layouts.header')
@include('layouts.menu')
<div class="page-wrapper">
    <div class="content">
        @include('layouts.fileariane')
        @if(session()->has("message"))
            <div style="padding: 10px" class="alert {{session()->get('type')}}">{{ session()->get('message') }} </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body add-product pb-0">
                <form action="{{ route('etablissement.update', ['id' => $etablissement->id ]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Type d'établissement</label>
                                <select name="type_etablissement_id" id="" class="form-control">
                                    @foreach ($typeEtablissements as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $etablissement->type_etablissement_id ? 'selected' : '' }}>{{ $item->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="specialite">Spécialités</label>
                                <input type="text" class="form-control" name="specialite" value="{{ $etablissement->specialite }}">
                                <span class="form-text text-muted">Séparez vos spécialités par des virgules si vous en avez plusieurs.</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group input-select">
                                <label for="name">Type de prestation</label>
                                <select name="type_de_prestation[]" multiple class="selectpicker" data-live-search="true">
                                    @foreach ($typeDePrestations as $item)
                                        <option value="{{ $item->id }}"
                                            {{ is_array($tableTypePrestation) && in_array($item->id, $tableTypePrestation) ? 'selected' : '' }}>
                                            {{ $item->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Nom de l'établissement</label>
                                <input type="text" name="name" class="form-control" required value="{{ $etablissement->name }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mobile">Numero Mobile</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-default">+225</span>
                                      <input type="text" class="form-control" name="mobile" value="{{ $etablissement->mobile }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mobile">Ce numéro mobile est Whatsapp ?</label>
                                <input type="radio" checked name="is_whatsapp" value="0"> Non
                                <input type="radio" name="is_whatsapp" value="1"> Oui
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mobile">Numero fixe</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-default">+225</span>
                                      <input type="text" class="form-control" name="mobile_fix" value="{{ $etablissement->mobile_fix }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" required value="{{ $etablissement->email }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="adresse">Situation géographique</label>
                                <input type="text" name="adresse" class="form-control" required value="{{ $etablissement->adresse }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="adresse">Adresse Map
                                    <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="L'adresse Map est utilisée pour localiser un lieu sur Google Maps à l'aide de la latitude et de la longitude."></i>
                                </label>
                                <input type="text" name="adresse_map" readonly class="form-control" required value="{{ $etablissement->adresse_map }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" id="longitude" name="longitude" class="form-control" readonly required value="{{ $etablissement->longitude }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" id="latitude" name="latitude" class="form-control" readonly required value="{{ $etablissement->latitude }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" onclick="getLocation()" class="btn btn-primary mt-4" style="margin-top: 27px">Obtenir Coordonnées</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pays_id">Pays </label>
                                <select class="form-control" name="pays_id" id="">
                                    @foreach ($pays as $item)
                                        <option value="{{ $item->id }}" {{ $etablissement->pays_id == $item->id ? 'selected' : '' }}>{{ $item->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ville_id">Ville </label>
                                <select class="form-control" name="ville_id" id="">
                                    @foreach ($villes as $item)
                                        <option value="{{ $item->id }}" {{ $etablissement->ville_id == $item->id ? 'selected' : '' }}>{{ $item->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commune_id">Commune</label>
                                <select class="form-control" name="commune_id" id="">
                                    @foreach ($communes as $item)
                                        <option value="{{ $item->id }}" {{ $etablissement->commune_id == $item->id ? 'selected' : '' }}>{{ $item->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo">Logo (Format : jpeg,png,jpg, 3M maximum)</label>
                                <input type="file" name="logo" class="form-control">
                                @if($logoUrl)
                                    <img width="200" height="200" src="{{ $logoUrl }}" alt="Logo de l'établissement">
                                @endif
                                <span class="form-text text-muted">Le logo sera stocké sur Wasabi.</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cover">Image de façade (Format : jpeg,png,jpg, 3M maximum)</label>
                                <input type="file" name="cover" class="form-control">
                                @if($coverUrl)
                                    <img width="200" height="200" src="{{ $coverUrl }}" alt="Image de façade de l'établissement">
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" required>{{ $etablissement->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Votre établissement propose-t-il des services de dépannage à domicile ou en déplacement ?</label>
                        <input type="radio" {{ $etablissement->service_mobile == 0 ? 'checked' : '' }} name="service_mobile" value="0"> Non
                        <input type="radio" {{ $etablissement->service_mobile == 1 ? 'checked' : '' }} name="service_mobile" value="1"> Oui
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mb-3">Mettre a jour l'établissement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Active les tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@include('layouts.footer')
