<aside class="main-sidebar sidebar-dark-darkest elevation-4">
    <a href="{{ route('home') }}" class="brand-link text-center">
        <img src="{{ asset('storage/logos/gts_logo.jpg') }}" alt="Gotitas" class="brand-image img-circle elevation-3"
            style="opacity: .9">

        <span class="brand-text font-weight-bold text">
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
    <style>
        /* Diseño del menú lateral: estilos limitados a este sidebar. */
        .main-sidebar.sidebar-dark-darkest {
            background: linear-gradient(180deg, #101b2d 0%, #0c1423 100%);
            color: #cbd5e1;
            box-shadow: 4px 0 18px rgba(4, 13, 29, .15) !important;
        }
        .sidebar-dark-darkest .brand-link {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            height: 57px;
            padding: 9px 14px;
            background: rgba(255, 255, 255, .035);
            border-bottom: 1px solid rgba(255, 255, 255, .10);
            text-decoration: none;
        }
        .sidebar-dark-darkest .brand-link .brand-image {
            float: none;
            width: 34px;
            height: 34px;
            margin: 0;
            border: 2px solid rgba(255, 255, 255, .78);
            object-fit: cover;
            opacity: 1 !important;
        }
        .sidebar-dark-darkest .brand-text {
            font-size: 14px;
            font-weight: 750 !important;
            color: #fff;
            letter-spacing: -.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-dark-darkest .sidebar {
            padding: 13px 9px 20px;
        }
        .sidebar-dark-darkest .nav-sidebar > .nav-item { margin-bottom: 6px; }
        .sidebar-dark-darkest .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            min-height: 40px;
            border-radius: 9px;
            padding: 10px 11px;
            color: #c1cee0;
            font-size: 13px;
            font-weight: 550;
            transition: background .18s ease, color .18s ease, box-shadow .18s ease;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link .nav-icon {
            width: 21px;
            flex: 0 0 21px;
            margin: 0 11px 0 0;
            text-align: center;
            font-size: 15px;
            color: #93a8c4;
            transition: color .18s ease;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link p {
            margin: 0;
            white-space: normal;
            line-height: 1.35;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link p .right {
            top: 50%;
            transform: translateY(-50%);
            right: 12px;
            font-size: 12px;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link:hover {
            background: rgba(98, 143, 207, .13);
            color: #fff;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link:hover .nav-icon {
            color: #b7d5ff;
        }
        .sidebar-dark-darkest .nav-sidebar > .nav-item > .nav-link.active {
            background: linear-gradient(100deg, #2468e8, #1257ca);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 11px rgba(18, 91, 210, .27);
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link.active .nav-icon { color: #fff; }
        .sidebar-dark-darkest .nav-treeview {
            margin: 5px 0 8px 13px;
            padding-left: 10px;
            border-left: 1px solid rgba(153, 178, 214, .24);
        }
        .sidebar-dark-darkest .nav-treeview > .nav-item > .nav-link {
            min-height: 35px;
            padding: 8px 9px;
            font-size: 12px;
            color: #afc0d5;
            border-radius: 7px;
        }
        .sidebar-dark-darkest .nav-treeview > .nav-item > .nav-link .nav-icon {
            flex-basis: 18px;
            width: 18px;
            margin-right: 8px;
            font-size: 12px;
        }
        .sidebar-dark-darkest .nav-treeview > .nav-item > .nav-link.active {
            background: rgba(54, 117, 223, .20);
            color: #fff;
            box-shadow: inset 3px 0 0 #5ba2ff;
            font-weight: 650;
        }
        .sidebar-dark-darkest .nav-sidebar > br { display: none; }
        .sidebar-dark-darkest .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar-dark-darkest .sidebar::-webkit-scrollbar-thumb {
            background: rgba(151, 170, 197, .26);
            border-radius: 8px;
        }
        .sidebar-dark-darkest .nav-sidebar .nav-link:focus-visible {
            outline: 2px solid #81b6ff;
            outline-offset: 2px;
        }
        @media (prefers-reduced-motion: reduce) {
            .sidebar-dark-darkest .nav-sidebar .nav-link { transition: none; }
        }
    </style>
</aside>
