@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="text-center mb-5">
            <h1 class="dashboard-title">
                Bienvenido al Sistema
            </h1>

            <style>
                .dashboard-title {
                    font-size: 3rem;
                    font-weight: 700;
                    color: #30485f;
                    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.15);
                    margin-bottom: 0.5rem;
                    transition: all 0.3s ease;
                }
            </style>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    {{-- Ciudades --}}
                    @can('clientes index')
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <a href="{{ route('clientes.index') }}" class="hover-card text-decoration-none">
                                <div class="small-box bg-gradient-navy shadow-lg dynamic-card">
                                    <div class="inner">
                                        <h3 class="font-weight-bold">Clientes: {{ $clientes }}</h3>
                                        <p>Gestión de clientes</p>
                                    </div>
                                    <div class="icon"
                                        style="color: rgba(255,255,255,1); font-size: 60px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Ver Clientes <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan
                    @can('pedido_compras index')
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <a href="{{ route('pedido_compras.index') }}" class="hover-card text-decoration-none">
                                <div class="small-box bg-gradient-lightblue shadow-lg dynamic-card">
                                    <div class="inner">
                                        <h3 class="font-weight-bold">Pedidos</h3>
                                        <p>Gestión de pedidos</p>
                                    </div>
                                    <div class="icon"
                                        style="color: rgba(255,255,255,1); font-size: 60px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Ver Pedidos <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan
                    {{-- @can('lineas index')
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <a href="{{ route('lineas.index') }}" class="hover-card text-decoration-none">
                                <div class="small-box bg-gradient-dark shadow-lg dynamic-card">
                                    <div class="inner">
                                        <h3 class="font-weight-bold">Lineas</h3>
                                        <p>Gestión de lineas</p>
                                    </div>
                                    <div class="icon"
                                        style="color: rgba(255,255,255,1); font-size: 60px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Ver Lineas <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan --}}

                    @can('ciudades index')
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <a href="{{ route('ciudades.index') }}" class="hover-card text-decoration-none">
                                <div class="small-box bg-gradient-navy shadow-lg dynamic-card">
                                    <div class="inner">
                                        <h3 class="font-weight-bold">Ciudades</h3>
                                        <p>Gestión de ciudades</p>
                                    </div>
                                    <div class="icon"
                                        style="color: rgba(255,255,255,1); font-size: 60px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        <i class="fas fa-city"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Ver Ciudades <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan

                    {{-- Departamentos --}}
                    @can('departamentos index')
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <a href="{{ route('Departamentos.index') }}" class="hover-card text-decoration-none">
                                <div class="small-box bg-gradient-lightblue shadow-lg dynamic-card">
                                    <div class="inner">
                                        <h3 class="font-weight-bold">Departamentos</h3>
                                        <p>Gestión de departamentos</p>
                                    </div>
                                    <div class="icon"
                                        style="color: rgba(255,255,255,1); font-size: 60px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Ver Departamentos <i class="fas fa-arrow-circle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan

                </div>
            </div>
        </section>
    </div>

    <style>
        .dynamic-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            perspective: 1000px;
        }

        .hover-card:hover .dynamic-card {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .small-box .icon {
            font-size: 50px;
            opacity: 0.2;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .small-box-footer {
            display: block;
            background: rgba(0, 0, 0, 0.05);
            padding: 10px;
            font-weight: bold;
            color: #fff;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }
    </style>
@endsection
