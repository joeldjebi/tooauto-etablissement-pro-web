			<!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
                    <div id="sidebar-menu" class="sidebar-menu">
                        <ul>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Main</h6>
                                <ul>
                                    <li class="{{ $menu == "dashboard" ? 'active' : ''}}">
                                        <a href="{{ route('dashboard') }}" ><i data-feather="grid"></i><span>Tableau de bord</span></a>
                                    </li>
                                </ul>								
                            </li>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Artices</h6>
                                <ul>
                                    <li class="{{ $menu == "articles" ? 'active' : ''}}">
                                        <a href="{{ route('article.index') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                            <span>Liste des articles</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Promotions</h6>
                                <ul>
                                    <li class="{{ $menu == "promotion" ? 'active' : ''}}">
                                        <a href="{{ route('promotion.index') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                            <span>Liste des promotions</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Abonnements</h6>
                                <ul>
                                    <li class="{{ $menu == "abonnements" ? 'active' : ''}}">
                                        <a href="{{ route('abonnement.index') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                            <span>Historique d'abonnements</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Forfait</h6>
                                <ul>
                                    <li class="{{ $menu == "forfaits" ? 'active' : ''}}">
                                        <a href="{{ route('forfait.index') }}"><i data-feather="feather"></i><span>Liste des forfaits</span></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <h6 class="submenu-hdr">Paramètres</h6>
                                <a href="javascript:void(0);" class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns">
                                        <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"></path>
                                    </svg>
                                    <span>Paramètres</span><span class="menu-arrow"></span>
                                </a>
                                <ul style="display: none;">
                                    <li class="{{ $menu == "etablissement-edit" ? 'active' : ''}}">
                                        <a href="{{ route('etablissement.edit') }}">Etablissement </a>
                                    </li>
                                    <li class="{{ $menu == "profil" ? 'active' : ''}}">
                                        <a href="{{ route('profil.index') }}">Mon compte </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>