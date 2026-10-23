@extends('layouts.app')

@section('content')
<div class="content px-3 py-4 reporte-page">
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
        <div>
            <h2 class="font-weight-bold mb-1"><i class="fas fa-chart-bar text-primary mr-2"></i>Reportes</h2>
            <p class="text-muted mb-0">Consultas y exportaciones de datos del sistema mayorista.</p>
        </div>
        <span class="badge badge-light border p-2">Solo lectura</span>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-white font-weight-bold">Configuración del reporte</div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" id="reporte-form">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo">Tipo de reporte</label>
                        <select class="form-control" name="tipo" id="tipo" onchange="this.form.submit()">
                            @foreach(['pedidos'=>'Pedidos','articulos'=>'Artículos','stock'=>'Stock por sucursal','clientes'=>'Clientes'] as $valor=>$nombre)
                                <option value="{{ $valor }}" {{ $tipo === $valor ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($tipo === 'pedidos')
                    <div class="col-md-2 mb-3">
                        <label for="desde">Desde</label>
                        <input type="date" class="form-control" id="desde" name="desde" value="{{ $filtros['desde'] ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="hasta">Hasta</label>
                        <input type="date" class="form-control" id="hasta" name="hasta" value="{{ $filtros['hasta'] ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos</option>
                            @foreach($estados as $item)
                            <option value="{{ $item }}" {{ ($filtros['estado'] ?? '') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(in_array($tipo, ['pedidos','stock']))
                    <div class="col-md-3 mb-3">
                        <label for="sucursal">Sucursal</label>
                        <select name="sucursal" id="sucursal" class="form-control">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $codigo=>$nombre)
                            <option value="{{ $tipo === 'stock' ? $nombre : $codigo }}" {{ ($filtros['sucursal'] ?? '') == ($tipo === 'stock' ? $nombre : $codigo) ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-{{ in_array($tipo, ['pedidos','stock']) ? '4' : '6' }} mb-3">
                        <label for="buscar">Buscar</label>
                        <input class="form-control" id="buscar" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Código, número, nombre o descripción">
                    </div>
                </div>
                <div class="d-flex flex-wrap" style="gap:8px">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Generar reporte</button>
                    <a class="btn btn-outline-secondary" href="{{ route('reportes.index', ['tipo'=>$tipo]) }}">Limpiar filtros</a>
                    <button type="submit" name="exportar" value="csv" class="btn btn-success"><i class="fas fa-file-excel mr-1"></i> Exportar a Excel (CSV)</button>
                    <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i class="fas fa-print mr-1"></i> Imprimir / PDF</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="small-box bg-white border shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($filas->total(), 0, ',', '.') }}</h3>
                    <p>Registros encontrados</p>
                </div>
                <div class="icon"><i class="fas fa-list-alt"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-white border shadow-sm">
                <div class="inner">
                    <h3>{{ ucfirst($tipo) }}</h3>
                    <p>Reporte seleccionado</p>
                </div>
                <div class="icon"><i class="fas fa-chart-pie"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-white border shadow-sm">
                <div class="inner">
                    <h3>{{ now()->format('d/m/Y') }}</h3>
                    <p>Fecha de consulta</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between">
            <strong>Resultado: {{ ucfirst($tipo) }}</strong>
            <span class="ml-auto text-muted">Página {{ $filas->currentPage() }} de {{ $filas->lastPage() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm mb-0">
                    <thead class="thead-light"><tr>
                        @foreach($columnas as $titulo=>$campo)
                        <th class="p-3">{{ $titulo }}</th>
                        @endforeach
                    </tr></thead>
                    <tbody>
                    @forelse($filas as $fila)
                        <tr>
                        @foreach($columnas as $titulo=>$campo)
                            <td class="px-3 py-2">
                                @if(in_array($campo, ['ped_total','prec_vent']))
                                    {{ number_format((float)($fila->{$campo} ?? 0), 0, ',', '.') }}
                                @elseif($campo === 'ped_fecha' && $fila->{$campo})
                                    {{ \Carbon\Carbon::parse($fila->{$campo})->format('d/m/Y') }}
                                @else
                                    {{ $fila->{$campo} ?? '—' }}
                                @endif
                            </td>
                        @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columnas) }}" class="text-center p-5 text-muted">No hay registros para los filtros seleccionados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
            <small class="text-muted">Mostrando {{ $filas->firstItem() ?? 0 }} a {{ $filas->lastItem() ?? 0 }} de {{ $filas->total() }}</small>
            {{ $filas->links() }}
        </div>
    </div>
</div>
<style>
.reporte-page .card { border-radius: 12px; border: 1px solid #e4eaf1; }
.reporte-page .small-box { border-radius: 12px; }
.reporte-page .small-box .icon { color: #dde6f2; }
.reporte-page table { white-space: nowrap; font-size: 13px; }
@media print {
.main-sidebar, .main-header, .reporte-page form, .reporte-page .card-footer, .reporte-page .btn { display:none !important; }
.content-wrapper { margin-left:0 !important; }
.reporte-page .card { box-shadow:none !important; }
.reporte-page table { white-space:normal; }
}
</style>
@endsection
