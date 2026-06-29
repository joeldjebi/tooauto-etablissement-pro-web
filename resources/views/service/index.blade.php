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
            {{-- <div class="col-md-10">
                <div class="col-12">
                    <div class="form-group">
                        <div class="input-group">
                              <input type="text" class="form-control" name="search_service" placeholder="Que recherchez-vous ?">
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-2 text-end mb-4">
                <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addService">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                        Nouvelle prestation
                    </font></font>
                </a>
            </div>
        </div>
        @if (!empty($services) && count($services) > 0)
            <div class="row">
                @foreach ($services as $item)
                    <div class="modal fade" id="editService-{{ $item->id }}" tabindex="-1" role="dialog"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Modifier un service</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('service.update', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="mobile">Nom du service</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="libelle" value="{{ $item->libelle }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="mobile">Montant minimum</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="inputGroup-sizing-default">F CFA</span>
                                                        <input type="text" class="form-control" name="amount_min" value="{{ $item->amount_min }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">Image du service</label>
                                                    <input type="file" class="form-control" name="image">
                                                </div>
                                                <img src="services/image/{{ $item->image }}" class="card-img-top" alt="{{ $item->libelle }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" class="form-control" required>{{ $item->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer text-center">
                                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermé</button>
                                            <button class="btn btn-submit">Modifier</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="card card-services">
                            <img width="50" height="50" src="services/image/{{ $item->image }}" class="card-img-top" alt="{{ $item->libelle }}">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                                        {{ $item->libelle }}
                                    </font></font>
                                </h6>
                                <h6>{{ number_format($item->amount_min, 0, ',', ' ') }} F CFA</h6><br>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                                    <div class="d-flex justify-content-between">
                                            <!-- Boutons Edit et Delete alignés à gauche -->
                                            <a href="" class="me-3" data-bs-toggle="modal" data-bs-target="#editService-{{ $item->id }}">
                                                <img src="../assets/img/icons/edit.svg" alt="img">
                                            </a>
                                            <form id="deleteForm{{ $item->id }}" action="{{ route('service.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-link" onclick="confirmDelete({{ $item->id }})">
                                                    <img src="../assets/img/icons/delete.svg" alt="img">
                                                </button>
                                            </form>
                                    </div>
                                    
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <p>Aucun service enregistrer pour le moment !</p>
        @endif
    </div>

    <div class="modal fade" id="addService" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un service</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('service.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Nom du service</label>
                                    <div class="input-group">
                                          <input type="text" class="form-control" name="libelle">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Montant minimum</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">F CFA</span>
                                          <input type="text" class="form-control" name="amount_min">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Image du service</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-center">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermé</button>
                            <button class="btn btn-submit">Envoyé</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')