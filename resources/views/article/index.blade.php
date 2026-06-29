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
             <h5 class="card-title">Les articles</h5>
            </div>
            <div class="col-md-2 text-end">
                <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addArticle">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                        Nouveau article
                    </font></font>
                </a>
            </div>
        </div>

        @if (!empty($articles) && count($articles) > 0)
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
                                            <th>Libellé</th>
                                            <th>Montant</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($articles->isNotEmpty())
                                            @foreach ($articles as $key => $item)
                                                <div class="modal fade" id="editArticle-{{ $item->id }}" tabindex="-1" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Modifier un article</h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('article.update', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Nom du article</label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="libelle" value="{{ $item->libelle }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Montant de l'article</label>
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text" id="inputGroup-sizing-default">F CFA</span>
                                                                                    <input type="text" class="form-control" name="amount" value="{{ $item->amount }}">
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
                                                                <h5 class="modal-title">Détails de l'article</h5>
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
                                                                            <h4>Libellé</h4>
                                                                            <h6>{{ $item->libelle }}</h6>
                                                                        </li>
                                                                        <li>
                                                                            <h4>Montant</h4>
                                                                            <h6>{{ number_format($item->amount, 0, ',', ' ') }} F CFA</h6>
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
                                                    <td>{{ number_format($item->amount, 0, ',', ' ') }} F CFA</td>
                                                    <td>
                                                        <div class="d-flex justify-content-between">
                                                            <!-- Boutons Edit et Delete alignés à gauche -->
                                                            <a href="" class="mt-2" data-bs-toggle="modal" data-bs-target="#editArticle-{{ $item->id }}">
                                                                <img src="../assets/img/icons/edit.svg" alt="img">
                                                            </a>
                                                            <a class="me-3" data-bs-toggle="modal"
                                                                data-bs-target="#show{{ $item->id }}">
                                                                <img src="../assets/img/icons/eye.svg" alt="img">
                                                            </a>
                                                            <form id="deleteForm{{ $item->id }}" action="{{ route('article.destroy', $item->id) }}" method="POST" style="display:inline;">
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
            <p>Aucun articles enregistrer pour le moment !</p>
        @endif
    </div>

    <div class="modal fade" id="addArticle" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un article</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('article.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Nom de l'article</label>
                                    <div class="input-group">
                                          <input type="text" class="form-control" name="libelle">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="mobile">Montant de l'article </label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">F CFA</span>
                                          <input type="text" class="form-control" name="amount">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Image de l'article</label>
                                    <input type="file" class="form-control" name="image">

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description de l'article</label>
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
