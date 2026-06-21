<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard OT</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .kpi-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            transition: .3s;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
        }

        .kpi-value {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
        }

        .chart-card,
        .table-card,
        .info-card,
        .resumen-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        .timeline {
            position: relative;
            margin-left: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            width: 3px;
            height: 100%;
            background: #0d6efd;
        }

        .timeline-item {
            position: relative;
            padding-left: 35px;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #0d6efd;
            border: 3px solid #fff;
        }

        .timeline-item.is-last::before {
            background: #198754;
        }

        .progress {
            height: 25px;
        }

        .progress-bar {
            font-weight: bold;
        }

        .header-card {
            background: linear-gradient(135deg, #0d6efd, #4a8cff);
            color: white;
            border-radius: 15px;
        }

        .report-meta {
            font-size: 12px;
            opacity: .85;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            .header-card {
                background: #0d6efd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid py-4">

        <div class="card header-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">Dashboard de Trazabilidad</h2>
                    <p class="mb-0">Reporte gerencial de seguimiento de Orden de Trabajo</p>
                </div>

                <div class="d-flex align-items-end flex-column gap-2 no-print">
                    <form method="GET" action="{{ route('dashboard.ot') }}" class="d-flex gap-2">
                        <input type="text" name="nro_ot" value="{{ $nroOtBuscada ?? '' }}" class="form-control"
                            placeholder="Ingrese N° de OT">

                        <button class="btn btn-light">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-light" onclick="history.back()">
                            <i class="bi bi-arrow-left"></i> Volver
                        </button>

                        {{-- @if ($ot)
                            <button type="button" class="btn btn-outline-light" onclick="window.print()">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        @endif --}}
                    </form>
                </div>
            </div>
        </div>

        @if ($mensaje)
            <div class="alert alert-warning shadow-sm rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ $mensaje }}</div>
            </div>
        @endif

        @if ($ot && $resumen)

            {{-- DATOS GENERALES --}}
            <div class="card info-card mb-4">
                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h3 class="mb-0">OT N° {{ $ot->nro_ot }}</h3>
                                <span class="badge bg-primary">{{ $ot->codigo }}</span>
                                <span class="badge bg-{{ $resumen['estado_color'] }} fs-6">
                                    {{ $resumen['estado_texto'] }}
                                </span>
                            </div>

                            <p class="text-muted mt-2 mb-1">
                                {{ $ot->descripcion }}
                            </p>

                            <p class="report-meta text-muted mb-0" style="font-size:13px">
                                Inicio: {{ $resumen['fecha_inicio']->format('d/m/Y') }}
                                &nbsp;·&nbsp;
                                Último movimiento: {{ $resumen['fecha_ultimo_proceso']->format('d/m/Y') }}
                                &nbsp;·&nbsp;
                                Reporte generado: {{ $resumen['fecha_generacion_reporte']->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="col-md-5">
                            <label class="fw-semibold">Avance de la OT</label>

                            <div class="progress">
                                <div class="progress-bar bg-{{ $resumen['avance_color'] }}"
                                    style="width: {{ $resumen['avance'] }}%">
                                    {{ $resumen['avance'] }}%
                                </div>
                            </div>
                        </div>

                    </div>

                    @if ($resumen['esta_demorada'])
                        <div class="alert alert-danger mt-3 mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-5"></i>
                            <div>
                                Esta OT no registra movimientos hace
                                <strong>{{ $resumen['dias_desde_ultimo_proceso'] }} días</strong>.
                                Último proceso: <strong>{{ $resumen['ultimo_proceso'] }}</strong>.
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- KPI --}}
            <div class="row mb-4 g-3">

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Procesos</div>
                            <div class="kpi-value">{{ $resumen['cantidad_procesos'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Avance</div>
                            <div class="kpi-value text-{{ $resumen['avance_color'] }}">
                                {{ $resumen['avance'] }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Tiempo Total</div>
                            <div class="kpi-value" style="font-size:24px">
                                {{ number_format($resumen['tiempo_total_horas'], 2) }} h
                            </div>
                            <small class="text-muted">{{ $resumen['tiempo_total_dias'] }} días</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Días sin Movimiento</div>
                            <div class="kpi-value {{ $resumen['esta_demorada'] ? 'text-danger' : '' }}"
                                style="font-size:24px">
                                {{ $resumen['dias_desde_ultimo_proceso'] }}
                            </div>
                            <small class="text-muted">desde el último proceso</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Duración Promedio</div>
                            <div class="kpi-value" style="font-size:24px">
                                {{ number_format($resumen['duracion_promedio_horas'], 2) }} h
                            </div>
                            <small class="text-muted">por proceso</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card kpi-card">
                        <div class="card-body text-center">
                            <div class="kpi-label">Último Proceso</div>
                            <div class="kpi-value" style="font-size:16px">
                                {{ $resumen['ultimo_proceso'] }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RESUMEN EJECUTIVO --}}
            <div class="card resumen-card mb-4">
                <div class="card-body">
                    <h5><i class="bi bi-clipboard-data"></i> Resumen Ejecutivo</h5>
                    <p class="mb-0">
                        La OT lleva <strong>{{ $resumen['tiempo_total_dias'] }} días</strong>
                        ({{ number_format($resumen['tiempo_total_horas'], 2) }} horas) desde su primer proceso
                        registrado, con un avance de <strong>{{ $resumen['avance'] }}%</strong>
                        y estado <strong>{{ $resumen['estado_texto'] }}</strong>.

                        @if ($resumen['proceso_mas_lento'])
                            El proceso con mayor duración fue
                            <strong>{{ $resumen['proceso_mas_lento']['proceso'] }}</strong>
                            ({{ number_format($resumen['proceso_mas_lento']['duracion_horas'], 2) }} horas),
                            mientras que el más rápido fue
                            <strong>{{ $resumen['proceso_mas_rapido']['proceso'] }}</strong>
                            ({{ number_format($resumen['proceso_mas_rapido']['duracion_horas'], 2) }} horas).
                        @endif

                        @if ($resumen['finalizada'])
                            La OT se encuentra <strong>finalizada</strong>.
                        @elseif ($resumen['esta_demorada'])
                            La OT está <strong>demorada</strong>: no hay movimientos hace
                            {{ $resumen['dias_desde_ultimo_proceso'] }} días.
                        @else
                            La OT está <strong>en proceso</strong> con actividad reciente.
                        @endif
                    </p>
                </div>
            </div>

            {{-- GRAFICOS --}}
            <div class="row mb-4">

                <div class="col-lg-6">

                    <div class="card chart-card">
                        <div class="card-body">
                            <h5>Duración por Proceso (horas)</h5>
                            <canvas id="chartTiempos"></canvas>
                        </div>
                    </div>

                </div>

                <div class="col-lg-6">

                    <div class="card chart-card">
                        <div class="card-body">
                            <h5>Avance Acumulado (%)</h5>
                            <canvas id="chartProcesos"></canvas>
                        </div>
                    </div>

                </div>

            </div>

            {{-- TIMELINE + HISTORIAL --}}
            <div class="row">

                <div class="col-lg-4">

                    <div class="card chart-card">
                        <div class="card-body">

                            <h5>Línea de Tiempo</h5>

                            <div class="timeline">

                                @foreach ($procesos as $p)
                                    <div class="timeline-item @if ($loop->last) is-last @endif">
                                        <strong>{{ $p['proceso'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $p['fecha']->format('d/m/Y H:i') }}</small>
                                        <br>
                                        <span class="badge bg-secondary">{{ $p['resultado'] }}</span>
                                        <span class="badge bg-light text-dark border">
                                            {{ $p['avance_acumulado'] }}%
                                        </span>
                                        @if (!$loop->first)
                                            <div class="small text-muted mt-1">
                                                +{{ number_format($p['duracion_horas'], 2) }} h desde el proceso
                                                anterior
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-lg-8">

                    <div class="card table-card">
                        <div class="card-body">

                            <h5>Historial Completo</h5>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Proceso</th>
                                            <th>Resultado</th>
                                            <th>Fecha</th>
                                            <th>Duración</th>
                                            <th>Avance</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($procesos as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p['proceso'] }}</td>
                                                <td>{{ $p['resultado'] }}</td>
                                                <td>{{ $p['fecha']->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ number_format($p['duracion_horas'], 2) }} h
                                                    <span class="text-muted">
                                                        ({{ $p['duracion_dias'] }} d)
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $p['avance_acumulado'] >= 100 ? 'success' : ($p['avance_acumulado'] >= 50 ? 'info' : 'warning') }}">
                                                        {{ $p['avance_acumulado'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        @endif

    </div>

    @if ($ot && $resumen)
        <script>
            const labels = {!! json_encode($labels) !!};
            const duraciones = {!! json_encode($duraciones) !!};
            const avances = {!! json_encode($avancesAcumulados) !!};

            new Chart(document.getElementById('chartTiempos'), {

                type: 'bar',

                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Horas',
                        data: duraciones,
                        backgroundColor: '#0d6efd'
                    }]
                },

                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Horas'
                            }
                        }
                    }
                }

            });

            new Chart(document.getElementById('chartProcesos'), {

                type: 'line',

                data: {
                    labels: labels,
                    datasets: [{
                        label: '% Avance',
                        data: avances,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, .15)',
                        fill: true,
                        tension: .3
                    }]
                },

                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: value => value + '%'
                            }
                        }
                    }
                }

            });
        </script>
    @endif

</body>

</html>
