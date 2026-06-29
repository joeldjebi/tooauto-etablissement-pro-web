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
        
        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Tarifs</font></font></h1>
            <p class="lead"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Créez rapidement un tableau de prix efficace pour vos clients potentiels avec cet exemple Bootstrap. Il est conçu avec des composants et des utilitaires Bootstrap par défaut avec peu de personnalisation.</font></font></p>
          </div>

          <div class="container">
            <div class="card-deck mb-3 text-center">
                <div class="row">
                    @foreach ($forfaits as $item)
                        <div class="col-md-4">
                            <div class="card mb-4 box-shadow">
                                <div class="card-header">
                                    <h4 class="my-0 font-weight-normal">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">{{ $item->nom }}</font>
                                        </font>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <h1 class="card-title pricing-card-title">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">{{ number_format($item->prix, 0, ',', ' ') }} F CFA </font>
                                        </font><br>
                                        <small class="text-muted">
                                            <font style="vertical-align: inherit;">
                                                <font style="vertical-align: inherit;">Durée {{ $item->duree }}  mois</font>
                                            </font>
                                        </small>
                                    </h1>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        @foreach(explode(',', $item->avantages) as $avantage)
                                            <li>
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">{{ trim($avantage) }}</font>
                                                </font>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($item->id != 1)
                                        <button type="button" class="btn btn-lg btn-block btn-outline-primary">
                                            <font style="vertical-align: inherit;">
                                                <font style="vertical-align: inherit;">Souscrire</font>
                                            </font>
                                        </button>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            
          </div>
    </div>

</div>
@include('layouts.footer')