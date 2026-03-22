<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Inicio</p>
    </a>
</li>

<br>

<li class="nav-item {{ request()->routeIs('lineas.*', 'Departamentos.*', 'ciudades.*', 'clientes.*', 'sucursal.*', 'articulos.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('Departamentos.*', 'ciudades.*', 'clientes.*', 'sucursal.*', 'articulos.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Carga de Datos
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        {{-- @can('departamentos index')
            <li class="nav-item">
                <a href="{{ route('lineas.index') }}"
                    class="nav-link {{ request()->routeIs('lineas.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tshirt"></i>
                    <p>- Lineas</p>
                </a>
            </li>
        @endcan --}}
        @can('articulos index')
            <li class="nav-item">
                <a href="{{ route('articulos.index') }}"
                    class="nav-link {{ request()->routeIs('articulos.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-box-open"></i>
                    <p>- Artículos</p>
                </a>
            </li>
        @endcan
        @can('sucursal index')
            <li class="nav-item">
                <a href="{{ route('sucursal.index') }}"
                    class="nav-link {{ request()->routeIs('sucursal.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-store"></i>
                    <p>- Sucursales</p>
                </a>
            </li>
        @endcan
        @can('clientes index')
            <li class="nav-item">
                <a href="{{ route('clientes.index') }}"
                    class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-friends"></i>
                    <p>- Clientes</p>
                </a>
            </li>
        @endcan
        @can('departamentos index')
            <li class="nav-item">
                <a href="{{ route('Departamentos.index') }}"
                    class="nav-link {{ request()->routeIs('Departamentos.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-map-marked-alt"></i>
                    <p>- Departamentos</p>
                </a>
            </li>
        @endcan

        @can('ciudades index')
            <li class="nav-item">
                <a href="{{ route('ciudades.index') }}"
                    class="nav-link {{ request()->routeIs('ciudades.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-city"></i>
                    <p>- Ciudades</p>
                </a>
            </li>
        @endcan
    </ul>
</li>


<br>

<li class="nav-item {{ request()->routeIs('pedido_compras.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('pedido_compras.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice"></i>
        <p>
            Pedidos
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        @can('pedido_compras index')
            <li class="nav-item">
                <a href="{{ route('pedido_compras.index') }}"
                    class="nav-link {{ request()->routeIs('pedido_compras.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-angle-right"></i>
                    <p>Realizar Pedido</p>
                </a>
            </li>
        @endcan
    </ul>
</li>
{{-- <br>

<li class="nav-item {{ request()->routeIs('carga_fotos.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('carga_fotos.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-camera-retro"></i>
        <p>
            Carga De Fotos
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        @can('carga_fotos index')
            <li class="nav-item">
                <a href="{{ route('carga_fotos.index') }}"
                    class="nav-link {{ request()->routeIs('carga_fotos.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-camera"></i>
                    <p>- Carga De Fotos</p>
                </a>
            </li>
        @endcan
    </ul>
</li> --}}
<br>
{{-- @hasrole('ADMIN') --}}
<!-- Configuraciones -->
<li class="nav-item {{ Request::is('usuarios*', 'roles*', 'permissions*', 'auditoria*') ? 'menu-open' : '' }}">
    <a href="#"
        class="nav-link {{ Request::is('usuarios*', 'roles*', 'permissions*', 'auditoria*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-cogs"></i>
        <p>
            Configuraciones
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>- Usuarios</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('permissions.index') }}"
                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-key"></i>
                <p>- Permisos</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>- Roles</p>
            </a>
        </li>
    </ul>
</li>

{{-- @endhasallroles --}}
