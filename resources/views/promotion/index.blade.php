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
        <div class="row mb-2">
            <div class="col-md-9">
            <h5 class="card-title">Les promotions</h5>
            </div>
            <div class="col-md-2 text-end">
                <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPromotion">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                        Nouvelle promotion
                    </font></font>
                </a>
            </div>
        </div>

        @if (!empty($promotions) && count($promotions) > 0)
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Libellé de la promotion</th>
                                            <th>Contact</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($promotions->isNotEmpty())
                                            @foreach ($promotions as $key => $item)
                                                <div class="modal fade" id="editPromotion-{{ $item->id }}" tabindex="-1" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Modifier la promotion</h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('promotion.update', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Libellé de la promotion</label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="libelle" value="{{ $item->libelle }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Contact de la promotion</label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="mobile" value="{{ $item->mobile }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Date de début</label>
                                                                                <div class="input-group">
                                                                                    <input readonly type="date" class="form-control" name="date_debut" value="{{ $item->date_debut }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Date de fin</label>
                                                                                <div class="input-group">
                                                                                    <input readonly type="date" class="form-control" name="date_fin" value="{{ $item->date_fin }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="">Image du article</label>
                                                                                <input type="file" class="form-control" name="image">
                                                                            </div>
                                                                            @if($item->image_url)
                                                                                <img src="{{ $item->image_url }}" class="card-img-top" alt="{{ $item->libelle }}">
                                                                            @endif
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="description">Description</label>
                                                                            <textarea name="description" class="form-control" required>{{ $item->description }}</textarea>
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
                                                <div class="modal fade" id="show{{ $item->id }}" tabindex="-1" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Détails de la promotion</h5>
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
                                                                                <img
                                                                                    width="150"
                                                                                    height="150"
                                                                                    src="{{ $item->image_url }}"
                                                                                    alt="{{ $item->libelle }}"
                                                                                >
                                                                            </h6>
                                                                        </li>
                                                                        <li>
                                                                            <h4>Libellé de la promotion</h4>
                                                                            <h6>{{ $item->libelle }}</h6>
                                                                        </li>
                                                                        <li>
                                                                            <h4>Contact</h4>
                                                                            <h6>{{ $item->mobile }}</h6>
                                                                        </li>
                                                                        <li>
                                                                            <h4>Date de début</h4>
                                                                            <h6>{{ $item->date_debut }}</h6>
                                                                        </li>
                                                                        <li>
                                                                            <h4>Date de fin</h4>
                                                                            <h6>{{ $item->date_fin }}</h6>
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
                                                            <img width="50" height="50" src="{{ $item->image_url }}" alt="{{ $item->libelle }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->libelle }}</td>
                                                    <td>{{ $item->mobile }} </td>
                                                    <td>{{ $item->date_debut }} </td>
                                                    <td>{{ $item->date_fin }} </td>
                                                    <td>
                                                        <div class="d-flex justify-content-between">
                                                            <!-- Boutons Edit et Delete alignés à gauche -->
                                                            <a href="" class="mt-2" data-bs-toggle="modal" data-bs-target="#editPromotion-{{ $item->id }}">
                                                                <img src="../assets/img/icons/edit.svg" alt="img">
                                                            </a>
                                                            <a class="me-3" data-bs-toggle="modal"
                                                                data-bs-target="#show{{ $item->id }}">
                                                                <img src="../assets/img/icons/eye.svg" alt="img">
                                                            </a>
                                                            <form id="deleteForm{{ $item->id }}" action="{{ route('promotion.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-link" onclick="confirmDelete({{ $item->id }})">
                                                                    <img src="../assets/img/icons/delete.svg" alt="img">
                                                                </button>
                                                            </form>
                                                        </div>
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
            <p>Aucun promotions enregistrer pour le moment !</p>
        @endif
    </div>

    <div class="modal fade" id="addPromotion" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une promotion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('promotion.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Libellé de la promotion</label>
                                    <div class="input-group">
                                          <input type="text" class="form-control" name="libelle">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Contact </label>
                                    <div class="input-group">
                                          <input type="number" class="form-control" name="mobile">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="date_debut">Date de début </label>
                                    <div class="input-group">
                                          <input type="date" class="form-control" name="date_debut">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Date de fin </label>
                                    <div class="input-group">
                                          <input type="date" class="form-control" name="date_fin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Image de la promotion</label>
                                    <input type="file" class="form-control" name="image">

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description de la promotion</label>
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
