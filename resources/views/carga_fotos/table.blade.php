<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden"><div class="card-body p-0">

    <div class="table-responsive">
        <table class="table table-striped table-hover text-center mb-0" id="carga_fotos-table">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 10%">Fecha</th>
                    <th style="width: 10%">N° OT</th>
                    <th style="width: 10%">Prec. Costo</th>
                    <th style="width: 10%">Prec. Venta</th>
                    <th style="width: 20%">Artículo</th>
                    <th style="width: 15%">Imagen</th>
                    <th style="width: 15%">Usuario</th>
                    <th style="width: 10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fotos as $value)
                    <tr>
                        <!-- Fecha -->
                        <td class="align-middle text-center">
                            {{ \Carbon\Carbon::parse($value->fot_fecha)->format('d/m/Y') }}
                        </td>

                        <!-- OT -->
                        <td class="align-middle text-center">{{ $value->fot_ot }}</td>

                        <!-- Costo -->
                        <td class="align-middle text-center">
                            {{ number_format($value->fot_costo, 0, ',', '.') }} Gs.
                        </td>

                        <!-- Venta -->
                        <td class="align-middle text-center">
                            {{ number_format($value->fot_venta, 0, ',', '.') }} Gs.
                        </td>

                        <!-- Descripción y Línea concatenadas -->
                        <td class="align-middle text-center">
                            {{ $value->fot_desc }} {{ $value->linea->linea_desc ?? $value->linea_cod }}
                        </td>
                        <!-- Imagen -->
                        <td class="align-middle text-center">
                            @if ($value->foto_disponible)
                            <a href="{{ route('carga_fotos.show', $value->fot_cod) }}" title="Ver fotografía en detalle">
                                <img src="{{ $value->foto_url }}" alt="Foto de OT {{ $value->fot_ot }}"
                                     loading="lazy" style="width:96px;height:72px;object-fit:contain;background:#f5f8fc;border-radius:8px;border:1px solid #dce6ef;padding:3px">
                            </a>
                        @else
                            <span class="text-muted small"><i class="far fa-image mr-1"></i>Sin imagen disponible</span>
                        @endif
                        </td>

                        <!-- Usuario -->
                        <td class="align-middle text-center">
                            {{ $value->user->name ?? $value->user_id }}
                        </td>

                        <!-- Acciones -->
                        <td class="align-middle text-center" style="width: 140px;">
                            <div class="btn-group">
                                <a href="{{ route('carga_fotos.show', [$value->fot_cod]) }}" class="btn btn-info btn-sg"
                                    title="Ver">
                                    <i class="far fa-eye"></i>
                                </a>

                                @can('carga_fotos edit')
                                    <a href="{{ route('carga_fotos.edit', [$value->fot_cod]) }}"
                                        class="btn btn-warning btn-sg" title="Editar">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan

                                @can('carga_fotos destroy')
                                    {!! Form::open([
                                        'route' => ['carga_fotos.destroy', $value->fot_cod],
                                        'method' => 'delete',
                                        'class' => 'delete-form',
                                        'style' => 'display:inline',
                                    ]) !!}
                                    {!! Form::button('<i class="fas fa-trash"></i>', [
                                        'type' => 'button',
                                        'class' => 'btn btn-danger btn-sg alert-delete',
                                        'data-mensaje' => $value->fot_ot,
                                        'title' => 'Eliminar',
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-exclamation-circle"></i> No se encontraron resultados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix bg-light">
        <div class="float-left text-muted">
            Mostrando {{ $fotos->firstItem() ?? 0 }} -
            {{ $fotos->lastItem() ?? 0 }} de
            {{ $fotos->total() }} registros
        </div>
        <div class="float-right">
            {{ $fotos->links() }}
        </div>
    </div>
</div>

</div>
