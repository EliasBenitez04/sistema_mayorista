<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('home') }}" class="brand-link text-center">
        <img src="{{ asset('storage/logos/gts_logo.jpg') }}" alt="Gotitas" class="brand-image img-circle elevation-3"
            style="opacity: .9">

        <span class="brand-text font-weight-light ml-2">
            {{ config('app.name') }}
        </span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @include('layouts.menu')
            </ul>
        </nav>
    </div>
</aside>
