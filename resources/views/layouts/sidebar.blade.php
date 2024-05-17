<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item @if (Route::currentRouteName() == 'home') active @endif">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="mdi mdi-home menu-icon"></i>
                <span class="menu-title">Home</span>
            </a>
        </li>
        @if (Illuminate\Support\Facades\Auth::user()->hasRole('super-admin'))
            <li class="nav-item @if (Route::currentRouteName() == 'dashboard') active @endif">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="mdi mdi-chart-bar menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Sales Order</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#stock" aria-expanded="false" aria-controls="stock">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Stock</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="stock">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="../../pages/ui-features/buttons.html">
                                Stock In
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../pages/ui-features/buttons.html">
                                Stock Out
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'customer.index') active @endif">
                <a class="nav-link" href="{{ route('customer.index') }}">
                    <i class="mdi mdi-account-multiple menu-icon"></i>
                    <span class="menu-title">Customer</span>
                </a>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') active @endif">
                <a class="nav-link" data-toggle="collapse" href="#product" aria-expanded="false"
                    aria-controls="product">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Product</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') show @endif" id="product">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'category-product.index') active @endif"
                                href="{{ route('category-product.index') }}">
                                Category Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'product.index') active @endif"
                                href="{{ route('product.index') }}">
                                Product
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'supplier.index') active @endif">
                <a class="nav-link" href="{{ route('supplier.index') }}">
                    <i class="mdi mdi-account-multiple menu-icon"></i>
                    <span class="menu-title">Supplier</span>
                </a>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'user.index') active @endif">
                <a class="nav-link" href="{{ route('user.index') }}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Users</span>
                </a>
            </li>
        @else
            @if (Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                        <span class="menu-title">Sales Order</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#stock" aria-expanded="false"
                        aria-controls="stock">
                        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                        <span class="menu-title">Stock</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="stock">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="../../pages/ui-features/buttons.html">
                                    Stock In
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../../pages/ui-features/buttons.html">
                                    Stock Out
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item @if (Route::currentRouteName() == 'customer.index') active @endif">
                    <a class="nav-link" href="{{ route('customer.index') }}">
                        <i class="mdi mdi-account-multiple menu-icon"></i>
                        <span class="menu-title">Customer</span>
                    </a>
                </li>
                <li class="nav-item @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') active @endif">
                    <a class="nav-link" data-toggle="collapse" href="#product" aria-expanded="false"
                        aria-controls="product">
                        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                        <span class="menu-title">Product</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') show @endif" id="product">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'category-product.index') active @endif"
                                    href="{{ route('category-product.index') }}">
                                    Category Product
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'product.index') active @endif"
                                    href="{{ route('product.index') }}">
                                    Product
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item @if (Route::currentRouteName() == 'supplier.index') active @endif">
                    <a class="nav-link" href="{{ route('supplier.index') }}">
                        <i class="mdi mdi-account-multiple menu-icon"></i>
                        <span class="menu-title">Supplier</span>
                    </a>
                </li>
            @else
                @if (Illuminate\Support\Facades\Auth::user()->hasRole('cashier'))
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                            <span class="menu-title">Sales Order</span>
                        </a>
                    </li>
                    <li class="nav-item @if (Route::currentRouteName() == 'customer.index') active @endif">
                        <a class="nav-link" href="{{ route('customer.index') }}">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">Customer</span>
                        </a>
                    </li>
                    <li class="nav-item @if (Route::currentRouteName() == 'product.index') active @endif">
                        <a class="nav-link" href="{{ route('product.index') }}">
                            <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                            <span class="menu-title">Product</span>
                        </a>
                    </li>
                @endif
            @endif
        @endif
    </ul>
</nav>
