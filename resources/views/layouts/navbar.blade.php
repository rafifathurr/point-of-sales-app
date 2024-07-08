<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5 ml-3" href="{{ route('home') }}">
            <img src="{{ asset('images/renata_label_icon.png') }}" style="height:75% !important;" class="mr-2"
                alt="logo" />
        </a>
        <a class="navbar-brand brand-logo-mini ml-2" href="{{ route('home') }}">
            <img src="{{ asset('images/renata_label_icon_sm.png') }}" alt="logo" />
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        @if (!isset($hide_button_hamburger_nav))
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        @endif
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="{{ asset('images/user-icon.png') }}" alt="profile" />
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <div class="user-box p-3">
                        <img src="{{ asset('images/user-icon.png') }}" alt="profile" />
                        <span class="ml-3"><b>{{ Illuminate\Support\Facades\Auth::user()->name }}</b></span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>
