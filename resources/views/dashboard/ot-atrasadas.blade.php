@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="mb-1">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                OT Atrasadas
            </h1>

            <p class="text-muted mb-0">

                Órdenes de trabajo sin movimiento durante

                <strong>
                    {{ $diasAlerta }} días o más
                </strong>

                <span class="ml-2 badge badge-secondary">
                    Se excluyen OT POSTERGADAS
                </span>

            </p>

        </div>

        <div>

            <a
                href="{{ route('dashboard.ot') }}"
                class="btn btn-outline-primary">

                <i class="fas fa-search"></i>

                Buscar OT

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- KPIs --}}
    {{-- ========================================================= --}}

    <div class="row mb-4">

        {{-- TOTAL --}}
        <div class="col-lg-4 col-md-6 mb-3">

            <div class="card shadow-sm border-left-danger h-100">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                OT Atrasadas
                            </div>

                            <div
                                class="h3 mb-0 font-weight-bold"
                                id="totalOTDashboard">

                                {{ $totalAtrasadas }}

                            </div>

                            <small class="text-muted">
                                {{ $diasAlerta }} días o más sin movimiento
                            </small>

                        </div>

                        <div class="col-auto">

                            <i class="fas fa-exclamation-circle fa-2x text-danger"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PROMEDIO --}}
        <div class="col-lg-4 col-md-6 mb-3">

            <div class="card shadow-sm border-left-warning h-100">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Promedio días sin movimiento
                            </div>

                            <div class="h3 mb-0 font-weight-bold">

                                {{ number_format($promedioDiasAtraso, 2, ',', '.') }}

                            </div>

                            <small class="text-muted">
                                días
                            </small>

                        </div>

                        <div class="col-auto">

                            <i class="fas fa-clock fa-2x text-warning"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- MAYOR ATRASO --}}
        <div class="col-lg-4 col-md-6 mb-3">

            <div class="card shadow-sm border-left-dark h-100">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Mayor atraso
                            </div>

                            <div class="h3 mb-0 font-weight-bold">

                                {{ number_format($mayorAtraso, 2, ',', '.') }}

                            </div>

                            <small class="text-muted">
                                días sin movimiento
                            </small>

                        </div>

                        <div class="col-auto">

                            <i class="fas fa-hourglass-end fa-2x text-dark"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RESUMEN POR PROCESO --}}
    {{-- ========================================================= --}}

    @if($otsPorProceso->isNotEmpty())

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">

                        <i class="fas fa-project-diagram text-primary"></i>

                        OT Atrasadas por Proceso

                    </h5>

                    <small class="text-muted">

                        Cantidad de OT detenidas actualmente en cada proceso

                    </small>

                </div>

                <span class="badge badge-primary p-2">

                    {{ $otsPorProceso->count() }}

                    {{ $otsPorProceso->count() == 1 ? 'proceso' : 'procesos' }}

                </span>

            </div>

        </div>


        <div class="card-body">

            <div class="row">

                @foreach($detalleProcesos as $detalle)

                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">

                    <div class="card proceso-card h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start">

                                <div class="pr-2">

                                    <div class="proceso-nombre">

                                        {{ $detalle['proceso'] }}

                                    </div>

                                    <div class="proceso-cantidad">

                                        {{ $detalle['cantidad'] }}

                                    </div>

                                    <div class="small text-muted">

                                        {{ $detalle['cantidad'] == 1
                                            ? 'OT atrasada'
                                            : 'OT atrasadas' }}

                                    </div>

                                </div>


                                <div class="proceso-icon">

                                    <i class="fas fa-exclamation-triangle"></i>

                                </div>

                            </div>


                            <div class="mt-3">

                                <div class="d-flex justify-content-between mb-1">

                                    <small class="text-muted">
                                        Participación
                                    </small>

                                    <small class="font-weight-bold">

                                        {{ number_format(
                                            $detalle['porcentaje'],
                                            1,
                                            ',',
                                            '.'
                                        ) }}%

                                    </small>

                                </div>


                                <div
                                    class="progress"
                                    style="height: 7px;">

                                    <div
                                        class="progress-bar bg-danger"
                                        role="progressbar"
                                        style="width: {{ min($detalle['porcentaje'], 100) }}%;">

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

    @endif


    {{-- ========================================================= --}}
    {{-- SIN OT --}}
    {{-- ========================================================= --}}

    @if($otsAtrasadas->isEmpty())

    <div class="card shadow-sm">

        <div class="card-body text-center py-5">

            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>

            <h4 class="text-success">
                No hay OT atrasadas
            </h4>

            <p class="text-muted mb-0">

                Actualmente no existen órdenes de trabajo con

                {{ $diasAlerta }} días o más sin movimiento.

            </p>

        </div>

    </div>

    @else


    {{-- ========================================================= --}}
    {{-- LISTADO --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm">

        {{-- HEADER --}}

        <div class="card-header bg-white">

            <div class="row align-items-end">

                {{-- TITULO --}}

                <div class="col-xl-4 col-lg-4 col-md-12 mb-3 mb-lg-0">

                    <h5 class="mb-1">

                        <i class="fas fa-list text-danger"></i>

                        Órdenes de Trabajo Atrasadas

                    </h5>

                    <small class="text-muted">

                        Mostrando

                        <strong
                            id="contadorOTFiltradas"
                            class="text-primary">

                            {{ $totalAtrasadas }}

                        </strong>

                        de

                        <strong>

                            {{ $totalAtrasadas }}

                        </strong>

                        OT

                    </small>

                </div>


                {{-- FILTRO --}}

                <div class="col-xl-5 col-lg-5 col-md-9 mb-3 mb-lg-0">

                    <label
                        for="filtroProceso"
                        class="small font-weight-bold text-muted mb-1">

                        <i class="fas fa-filter text-primary"></i>

                        Filtrar por proceso

                    </label>


                    <select
                        id="filtroProceso"
                        name="filtroProceso"
                        class="form-control">

                        <option value="">

                            Todos los procesos ({{ $totalAtrasadas }})

                        </option>


                        @foreach($detalleProcesos as $detalle)

                        <option
                            value="{{ $detalle['proceso'] }}"
                            data-cantidad="{{ $detalle['cantidad'] }}">

                            {{ $detalle['proceso'] }}

                            ({{ $detalle['cantidad'] }})

                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- LIMPIAR --}}

                <div class="col-xl-3 col-lg-3 col-md-3 mb-3 mb-lg-0">

                    <button
                        type="button"
                        id="btnLimpiarFiltro"
                        class="btn btn-outline-secondary btn-block">

                        <i class="fas fa-sync-alt"></i>

                        Mostrar todas

                    </button>

                </div>

            </div>

        </div>


        {{-- MENSAJE FILTRO --}}

        <div
            id="mensajeFiltro"
            class="filtro-activo d-none">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <i class="fas fa-filter text-primary mr-1"></i>

                    Mostrando OT atrasadas en:

                    <strong id="nombreProcesoFiltro"></strong>

                </div>


                <span
                    class="badge badge-primary"
                    id="badgeProcesoFiltro">

                    0 OT

                </span>

            </div>

        </div>


        {{-- LISTADO --}}

        <div class="card-body p-0">

            <div
                class="accordion"
                id="accordionOT">


                @foreach($otsAtrasadas as $index => $item)

                @php

                $ot = $item['ot'];

                /*
                * =====================================================
                * EXCLUIR OT POSTERGADAS
                * =====================================================
                */

                $estadoOT = strtoupper(
                trim((string) $ot->estado)
                );

                @endphp


                @if($estadoOT === 'POSTERGADO')

                @continue

                @endif


                @php

                $procesos = $item['procesos'];

                $ultimoMovimiento =
                $item['ultimo_movimiento'];

                $diasSinMovimiento =
                $item['dias_sin_movimiento'];

                $avance =
                $item['avance'];

                $ultimoProceso =
                $item['ultimo_proceso_normalizado'];

                @endphp


                {{-- OT --}}

                <div
                    class="card mb-0 border-bottom ot-item"
                    data-proceso="{{ trim($ultimoProceso) }}"
                    data-estado="{{ $estadoOT }}">


                    {{-- CABECERA --}}

                    <div
                        class="card-header bg-white"
                        id="heading{{ $index }}">

                        <div class="row align-items-center">


                            {{-- OT --}}

                            <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">

                                <small class="text-muted d-block">
                                    Orden de Trabajo
                                </small>

                                <strong class="text-primary ot-numero">

                                    OT #{{ $ot->nro_ot }}

                                </strong>

                            </div>


                            {{-- CODIGO --}}

                            <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">

                                <small class="text-muted d-block">
                                    Código
                                </small>

                                <strong>

                                    {{ $ot->codigo }}

                                </strong>

                            </div>


                            {{-- DESCRIPCION --}}

                            <div class="col-lg-3 col-md-12 mb-2 mb-lg-0">

                                <small class="text-muted d-block">
                                    Descripción
                                </small>

                                <span>

                                    {{ $ot->descripcion }}

                                </span>

                            </div>


                            {{-- ULTIMO PROCESO --}}

                            <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">

                                <small class="text-muted d-block">
                                    Último movimiento
                                </small>

                                <strong class="text-dark">

                                    {{ $ultimoProceso }}

                                </strong>

                                <small class="d-block text-muted">

                                    {{ \Carbon\Carbon::parse(
                                        $ultimoMovimiento->fecha_proceso
                                    )->format('d/m/Y') }}

                                </small>

                            </div>


                            {{-- ATRASO --}}

                            <div class="col-lg-2 col-md-4 text-center mb-2 mb-lg-0">

                                <span class="badge badge-danger p-2">

                                    <i class="fas fa-clock"></i>

                                    {{ number_format(
                                        $diasSinMovimiento,
                                        1,
                                        ',',
                                        '.'
                                    ) }}

                                    días

                                </span>

                            </div>


                            {{-- BOTON --}}

                            <div class="col-lg-1 col-md-2 text-right">

                                <button
                                    class="btn btn-sm btn-outline-primary btn-expand"
                                    type="button"
                                    data-toggle="collapse"
                                    data-target="#collapse{{ $index }}"
                                    aria-expanded="false"
                                    aria-controls="collapse{{ $index }}">

                                    <i class="fas fa-chevron-down"></i>

                                </button>

                            </div>

                        </div>

                    </div>


                    {{-- DETALLE --}}

                    <div
                        id="collapse{{ $index }}"
                        class="collapse"
                        aria-labelledby="heading{{ $index }}"
                        data-parent="#accordionOT">

                        <div class="card-body">


                            {{-- INFORMACION --}}

                            <div class="row mb-4">

                                <div class="col-md-3 mb-3">

                                    <div class="small text-muted">
                                        Cantidad orden
                                    </div>

                                    <strong>

                                        {{ $ot->cantidad_orden }}

                                    </strong>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <div class="small text-muted">
                                        Último movimiento
                                    </div>

                                    <strong>

                                        {{ \Carbon\Carbon::parse(
                                            $ultimoMovimiento->fecha_proceso
                                        )->format('d/m/Y H:i') }}

                                    </strong>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <div class="small text-muted">
                                        Días sin movimiento
                                    </div>

                                    <strong class="text-danger">

                                        {{ number_format(
                                            $diasSinMovimiento,
                                            2,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <div class="small text-muted">
                                        Estado
                                    </div>

                                    <strong
                                        class="
                                            {{ $estadoOT === 'ACTIVO'
                                                ? 'text-success'
                                                : 'text-secondary'
                                            }}
                                        ">

                                        {{ $ot->estado }}

                                    </strong>

                                </div>

                            </div>


                            {{-- AVANCE --}}

                            <div class="mb-4">

                                <div class="d-flex justify-content-between mb-1">

                                    <span class="small font-weight-bold">
                                        Avance de la OT
                                    </span>

                                    <span class="small font-weight-bold">
                                        {{ $avance }}%
                                    </span>

                                </div>


                                <div
                                    class="progress"
                                    style="height: 10px;">

                                    <div
                                        class="progress-bar
                                        {{
                                            $avance >= 100
                                                ? 'bg-success'
                                                : (
                                                    $avance >= 50
                                                        ? 'bg-info'
                                                        : 'bg-warning'
                                                )
                                        }}"
                                        role="progressbar"
                                        style="width: {{ min($avance, 100) }}%;">

                                    </div>

                                </div>

                            </div>


                            {{-- HISTORIAL --}}

                            <h6 class="font-weight-bold mb-3">

                                <i class="fas fa-route text-primary"></i>

                                Historial completo de procesos

                            </h6>


                            <div class="table-responsive">

                                <table class="table table-bordered table-hover table-sm">

                                    <thead class="thead-light">

                                        <tr>

                                            <th width="40">#</th>

                                            <th>Proceso</th>

                                            <th>Resultado</th>

                                            <th>Fecha</th>

                                            <th>Duración</th>

                                            <th>Acumulado</th>

                                            <th>Avance</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach($procesos as $numero => $proceso)

                                        <tr
                                            class="{{
                                                $proceso['es_suspendido']
                                                    ? 'table-secondary'
                                                    : ''
                                            }}">

                                            <td>

                                                {{ $numero + 1 }}

                                            </td>


                                            <td>

                                                <strong>

                                                    {{ $proceso['proceso'] }}

                                                </strong>


                                                @if($proceso['es_suspendido'])

                                                <span class="badge badge-secondary ml-1">

                                                    Suspendido

                                                </span>

                                                @endif

                                            </td>


                                            <td>

                                                @if($proceso['resultado'] !== null)

                                                {{ $proceso['resultado'] }}

                                                @else

                                                <span class="text-muted">
                                                    —
                                                </span>

                                                @endif

                                            </td>


                                            <td>

                                                {{ $proceso['fecha']->format('d/m/Y') }}

                                            </td>


                                            <td>

                                                @if($proceso['duracion_horas'] > 0)

                                                {{ number_format(
                                                        $proceso['duracion_horas'],
                                                        2,
                                                        ',',
                                                        '.'
                                                    ) }}

                                                h

                                                @else

                                                —

                                                @endif

                                            </td>


                                            <td>

                                                {{ number_format(
                                                    $proceso['horas_acumuladas'],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) }}

                                                h

                                            </td>


                                            <td>

                                                <div class="d-flex align-items-center">

                                                    <div
                                                        class="progress flex-grow-1 mr-2"
                                                        style="height: 8px;">

                                                        <div
                                                            class="progress-bar
                                                            {{
                                                                $proceso['avance_acumulado'] >= 100
                                                                    ? 'bg-success'
                                                                    : (
                                                                        $proceso['avance_acumulado'] >= 50
                                                                            ? 'bg-info'
                                                                            : 'bg-warning'
                                                                    )
                                                            }}"
                                                            style="width: {{ min(
                                                                $proceso['avance_acumulado'],
                                                                100
                                                            ) }}%;">

                                                        </div>

                                                    </div>


                                                    <small>

                                                        {{ $proceso['avance_acumulado'] }}%

                                                    </small>

                                                </div>

                                            </td>

                                        </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                @endforeach

            </div>


            {{-- SIN RESULTADOS --}}

            <div
                id="sinResultadosFiltro"
                class="text-center py-5 d-none">

                <i class="fas fa-filter fa-3x text-muted mb-3"></i>

                <h5 class="text-muted">

                    No hay OT para este proceso

                </h5>

                <p class="text-muted mb-0">

                    No existen OT atrasadas actualmente detenidas
                    en el proceso seleccionado.

                </p>

            </div>

        </div>

    </div>

    @endif

</div>


{{-- ============================================================= --}}
{{-- CSS --}}
{{-- ============================================================= --}}

<style>
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }

    .border-left-dark {
        border-left: 4px solid #343a40 !important;
    }

    .card-header {
        transition: background-color 0.2s ease;
    }

    .card-header:hover {
        background-color: #f8f9fa !important;
    }

    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .accordion .card {
        border-left: 0;
        border-right: 0;
        border-radius: 0;
    }

    .proceso-card {
        border: 1px solid #e3e6f0;
        border-left: 4px solid #dc3545;
        transition: all 0.2s ease;
    }

    .proceso-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
    }

    .proceso-nombre {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        line-height: 1.3;
        min-height: 32px;
    }

    .proceso-cantidad {
        font-size: 2rem;
        line-height: 1.1;
        font-weight: 700;
        color: #dc3545;
        margin-top: 5px;
    }

    .proceso-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: #f8d7da;
        color: #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .proceso-icon i {
        font-size: 18px;
    }

    .ot-numero {
        font-size: 1rem;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
        padding: 5px 10px !important;
        background-color: #fff !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {

        line-height: 26px !important;
        color: #495057 !important;
        font-size: 0.875rem;

    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {

        height: 36px !important;

    }

    .select2-dropdown {
        border-color: #ced4da !important;
    }

    .select2-results__option {
        font-size: 0.875rem;
        padding: 8px 12px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {

        background-color: #007bff;

    }

    .select2-container--default .select2-results__option[aria-selected="true"] {

        background-color: #e9ecef;
        color: #495057;

    }

    .filtro-activo {

        background: #f8f9fa;
        border-top: 1px solid #e3e6f0;
        border-bottom: 1px solid #e3e6f0;
        padding: 10px 20px;
        font-size: 0.875rem;

    }

    .btn-expand {
        transition: transform 0.2s ease;
    }

    .ot-item.filtro-oculto {
        display: none !important;
    }
</style>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>
    $(document).ready(function() {

        console.log('====================================');
        console.log('SISTEMA DE FILTRO OT INICIADO');
        console.log('====================================');


        /* =========================================================
           VERIFICAR ELEMENTOS
        ========================================================= */

        console.log(
            'OT encontradas:',
            $('.ot-item').length
        );

        console.log(
            'Procesos disponibles:',
            $('#filtroProceso option').length
        );


        /* =========================================================
           NORMALIZAR TEXTO
        ========================================================= */

        function normalizarTexto(texto) {

            if (texto === null || texto === undefined) {
                return '';
            }

            return String(texto)
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toUpperCase();

        }


        /* =========================================================
           INICIALIZAR SELECT2
        ========================================================= */

        if ($.fn.select2) {

            $('#filtroProceso').select2({

                width: '100%',

                placeholder: 'Seleccionar proceso...',

                allowClear: true,

                language: {

                    noResults: function() {
                        return 'No se encontró ningún proceso';
                    },

                    searching: function() {
                        return 'Buscando...';
                    }

                }

            });

            console.log(
                'Select2 inicializado correctamente'
            );

        } else {

            console.error(
                'ERROR: Select2 no está cargado.'
            );

        }


        /* =========================================================
           FUNCIÓN PRINCIPAL
        ========================================================= */

        function filtrarOTPorProceso(procesoSeleccionado) {

            var procesoFiltro =
                normalizarTexto(procesoSeleccionado);

            var totalVisible = 0;

            /*
             * EL TOTAL GENERAL VIENE DIRECTAMENTE DE LARAVEL
             */
            var totalOT = {{ $totalAtrasadas }};

            console.log(
                '------------------------------------'
            );

            console.log(
                'Filtro seleccionado:',
                procesoSeleccionado
            );

            console.log(
                'Filtro normalizado:',
                procesoFiltro
            );

            console.log(
                'Total OT atrasadas:',
                totalOT
            );


            /* =====================================================
               CERRAR OT ABIERTAS
            ===================================================== */

            $('.ot-item .collapse.show').each(function() {

                $(this).collapse('hide');

            });


            /* =====================================================
               RECORRER TODAS LAS OT
            ===================================================== */

            $('.ot-item').each(function() {

                var $ot = $(this);


                /* =================================================
                   SEGURIDAD EXTRA: NO MOSTRAR POSTERGADAS
                ================================================= */

                var estadoOT =
                    normalizarTexto(
                        $ot.attr('data-estado') || ''
                    );


                if (estadoOT === 'POSTERGADO') {

                    $ot
                        .addClass('filtro-oculto')
                        .hide();

                    return;

                }


                var procesoOT =
                    $ot.attr('data-proceso') || '';


                var procesoOTNormalizado =
                    normalizarTexto(procesoOT);


                console.log(
                    'OT:',
                    $ot.find('.ot-numero').text().trim(),
                    '| Proceso:',
                    procesoOT
                );


                /* =================================================
                   MOSTRAR TODAS
                ================================================= */

                if (procesoFiltro === '') {

                    $ot
                        .removeClass('filtro-oculto')
                        .show();

                    totalVisible++;

                    return;

                }


                /* =================================================
                   COMPARAR PROCESO
                ================================================= */

                if (
                    procesoOTNormalizado ===
                    procesoFiltro
                ) {

                    $ot
                        .removeClass('filtro-oculto')
                        .show();

                    totalVisible++;

                } else {

                    $ot
                        .addClass('filtro-oculto')
                        .hide();

                }

            });


            /* =====================================================
               ACTUALIZAR CONTADOR DEL LISTADO
            ===================================================== */

            $('#contadorOTFiltradas')
                .text(totalVisible);


            /*
             * IMPORTANTE:
             * El KPI principal siempre conserva
             * el total general de Laravel.
             */

            $('#totalOTDashboard')
                .text(totalOT);


            /* =====================================================
               MENSAJE DEL FILTRO
            ===================================================== */

            if (procesoFiltro !== '') {

                $('#mensajeFiltro')
                    .removeClass('d-none');

                $('#nombreProcesoFiltro')
                    .text(procesoSeleccionado);

                $('#badgeProcesoFiltro')
                    .text(
                        totalVisible +
                        ' OT'
                    );

            } else {

                $('#mensajeFiltro')
                    .addClass('d-none');

                $('#nombreProcesoFiltro')
                    .text('');

                $('#badgeProcesoFiltro')
                    .text('0 OT');

            }


            /* =====================================================
               SIN RESULTADOS
            ===================================================== */

            if (totalVisible === 0) {

                $('#sinResultadosFiltro')
                    .removeClass('d-none');

            } else {

                $('#sinResultadosFiltro')
                    .addClass('d-none');

            }


            /* =====================================================
               BOTÓN
            ===================================================== */

            if (procesoFiltro === '') {

                $('#btnLimpiarFiltro')
                    .html(
                        '<i class="fas fa-sync-alt"></i> Mostrar todas'
                    );

            } else {

                $('#btnLimpiarFiltro')
                    .html(
                        '<i class="fas fa-times"></i> Quitar filtro'
                    );

            }


            console.log(
                'OT visibles:',
                totalVisible
            );

            console.log(
                '------------------------------------'
            );

        }


        /* =========================================================
           CAMBIO SELECT2
        ========================================================= */

        $('#filtroProceso').on(
            'change',
            function() {

                var proceso =
                    $(this).val() || '';

                console.log(
                    'EVENTO CHANGE:',
                    proceso
                );

                filtrarOTPorProceso(proceso);

            }
        );


        /* =========================================================
           SELECT2: SELECCIÓN
        ========================================================= */

        $('#filtroProceso').on(
            'select2:select',
            function(e) {

                var valorReal =
                    $(this).val() || '';

                console.log(
                    'SELECT2 SELECT:',
                    valorReal
                );

                filtrarOTPorProceso(valorReal);

            }
        );


        /* =========================================================
           LIMPIAR SELECT2
        ========================================================= */

        $('#filtroProceso').on(
            'select2:clear',
            function() {

                console.log(
                    'SELECT2 LIMPIADO'
                );

                filtrarOTPorProceso('');

            }
        );


        /* =========================================================
           BOTÓN MOSTRAR TODAS
        ========================================================= */

        $('#btnLimpiarFiltro').on(
            'click',
            function() {

                console.log(
                    'BOTÓN LIMPIAR PRESIONADO'
                );


                $('#filtroProceso')
                    .val('')
                    .trigger('change');


                filtrarOTPorProceso('');


                $('#mensajeFiltro')
                    .addClass('d-none');

            }
        );


        /* =========================================================
           ABRIR OT
        ========================================================= */

        $('.collapse').on(
            'show.bs.collapse',
            function() {

                var target =
                    $(this).attr('id');

                $(
                        'button[data-target="#' +
                        target +
                        '"] i'
                    )
                    .removeClass('fa-chevron-down')
                    .addClass('fa-chevron-up');

            }
        );


        /* =========================================================
           CERRAR OT
        ========================================================= */

        $('.collapse').on(
            'hide.bs.collapse',
            function() {

                var target =
                    $(this).attr('id');

                $(
                        'button[data-target="#' +
                        target +
                        '"] i'
                    )
                    .removeClass('fa-chevron-up')
                    .addClass('fa-chevron-down');

            }
        );


        /* =========================================================
           ESTADO INICIAL
        ========================================================= */

        filtrarOTPorProceso('');

    });
</script>

@endsection