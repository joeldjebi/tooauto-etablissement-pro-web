

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

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash1.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters">{{ $serviceCount }}</span></h5>
                        <h6>PRESTATIONS</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash1 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash2.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters">{{ $articleCount }}</span></h5>
                        <h6>ARTICLES</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash2 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash3.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters">{{ $annonceCount }}</span></h5>
                        <h6>ANNONCES</h6>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($annonces) && count($annonces) > 0)
            <div class="row">
                <div class="col-lg-12">
        
                    <div class="card">
                        <div class="card-header">
                        <h5 class="card-title">Les besoins des usagers</h5>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Libellé</th>
                                            <th>Satut</th>
                                            <th>Date d'annonce</th>
                                            <th>Contact</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($annonces->isNotEmpty())
                                            @foreach ($annonces as $key => $item)
                                            @php
                                                $dateTimecreated_at = new DateTime($item->created_at);
                                                $created_at = $dateTimecreated_at->format('d-m-Y H:i:s');
                                            @endphp
                                            <div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Détails de l'annonce</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="productdetails">
                                                                <ul class="product-bar">
                                                                    <li>
                                                                        <h4>Image</h4>
                                                                        <h6>
                                                                            @if($item->image_url)
                                                                                <img 
                                                                                    width="150" 
                                                                                    height="150" 
                                                                                    src="{{ $item->image_url }}" 
                                                                                    alt="{{ $item->libelle }}"
                                                                                >
                                                                            @else
                                                                                Aucune image
                                                                            @endif
                                                                        </h6>
                                                                    </li>
                                                                    <li>
                                                                        <h4>Libellé</h4>
                                                                        <h6>{{ $item->libelle }}</h6>
                                                                    </li>
                                                                    <li>
                                                                        <h4>Statut</h4>
                                                                        <h6>
                                                                            <span class="badge bg-outline-{{ $item->statut == 1 ? 'success' : 'danger' }}">
                                                                                {{ $item->statut == 1 ? 'Actif' : 'Non actif' }}
                                                                            </span>  
                                                                        </h6>
                                                                    </li>
                                                                    <li>
                                                                        <h4>Date de l'annonce</h4>
                                                                        <h6>{{ $created_at }}</h6>
                                                                    </li>
                                                                    <li>
                                                                        <h4>Contact du client</h4>
                                                                        <h6><a href="tel:{{ $item->usager->mobile ?? "" }}" class="btn btn-success btn-sm contactus">{{ $item->usager->mobile ?? "" }}</a></h6>
                                                                    </li>
                                                                    <li>
                                                                        <h4>Description</h4>
                                                                        <h6>{!! $item->description !!},</h6>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if($item->image_url)
                                                        <img
                                                            width="50"
                                                            height="50"
                                                            src="{{ $item->image_url }}"
                                                            alt="{{ $item->libelle }}"
                                                        >
                                                    @endif
                                                </td>
                                                <td>{{ $item->libelle }}</td>
                                                <td>
                                                    <span class="badge bg-outline-{{ $item->statut == 1 ? 'success' : 'danger' }}">
                                                        {{ $item->statut == 1 ? 'Actif' : 'Non actif' }}
                                                    </span>                                                    
                                                </td>
                                                <td> {{ $created_at }} </td>
                                                <td> 
                                                    @if ($item->is_whatsapp == 0)
                                                        <a href="tel:{{ $item->usager->mobile ?? "" }}" class="btn btn-success btn-sm contactus">
                                                            Contacter téléphone
                                                        </a> 
                                                    @else
                                                        <a target="_blank" href="https://wa.me/+225{{ $item->mobile ?? "" }}" class="btn btn-success btn-sm contactus">
                                                            Contacter par whatsapp
                                                        </a> 
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="me-3" data-bs-toggle="modal"
                                                        data-bs-target="#edit{{ $item->id }}">
                                                        <img src="../assets/img/icons/eye.svg" alt="img">
                                                    </a>
                                                    {{-- <a href="">
                                                        <span class="fas toggle-password fa-eye-slash" id="togglePassword"></span>
                                                    </a>  --}}
                                                    <button class="btn btn-danger" onclick="confirmHideAnnonce({{ $item->id }})">Masquer</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <p>Aucun annonces enregistrer pour le moment !</p>
        @endif
    </div>
</div>

<script>
    function confirmHideAnnonce(annonceId) {
        if (confirm("Êtes-vous sûr de vouloir masquer cette annonce ?")) {
            // Si l'utilisateur confirme, appeler la fonction hideAnnonce
            hideAnnonce(annonceId);
        }
    }

    function hideAnnonce(annonceId) {
        let etablissementId = {{ $etablissement->id }};
        
        // Effectuer la requête AJAX pour masquer l'annonce
        $.ajax({
            url: "{{ route('etablissement.hideAnnonce') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                etablissement_id: etablissementId,
                annonce_id: annonceId
            },
            success: function(response) {
                if (response.success) {
                    // Afficher le modal de succès
                    // $('#successModal').modal('show');
                    location.reload(); // Recharger la page pour mettre à jour les annonces affichées
                } else {
                    alert('Erreur lors de la mise à jour de l\'annonce.');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la mise à jour de l'annonce.");
                alert('Erreur lors de la mise à jour de l\'annonce.');
            }
        });
    }
</script>
@include('layouts.footer')
