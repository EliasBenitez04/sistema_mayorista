@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="sucursals-table">
            <thead class="text-center">
                <tr class="text-center">
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sucursal as $sucursales)
                    <tr class="align-middle">
                        <td class="text-center"><span class="ref-id">{{ $sucursales->cod_suc }}</span></td>
                        <td class="ref-primary"><i class="fas fa-store text-muted mr-1"></i>{{ $sucursales->suc_descri }}</td>
                        <td class="ref-secondary"><i class="fas fa-map-marker-alt mr-1"></i>{{ $sucursales->suc_direccion ?? "—" }}</td>
                        <td class="text-center">{{ $sucursales->suc_telefono }}</td>
                        <td class="text-center" style="width: 150px">
                            {!! Form::open([
                                'route' => ['sucursal.destroy', $sucursales->cod_suc],
                                'method' => 'delete',
                                'class' => 'd-inline',
                            ]) !!}
                            <div class="ref-actions">
                                @can('sucursal edit')
                                    <a href="{{ route('sucursal.edit', [$sucursales->cod_suc]) }}"
                                        class="btn btn-primary" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                @endcan
                                @can('sucursal destroy')
                                    {!! Form::button('<i class="fas fa-trash-alt"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger alert-delete',
                                        'data-mensaje' => "la sucursal: {$sucursales->suc_descri}",
                                        'title' => 'Eliminar',
                                    ]) !!}
                                @endcan
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="ref-footer"><div class="ref-footer-info">Mostrando <strong>{{ $sucursal->firstItem() ?? 0 }}</strong> - <strong>{{ $sucursal->lastItem() ?? 0 }}</strong> de <strong>{{ $sucursal->total() }}</strong> registros</div><div>{{ $sucursal->links() }}</div></div></div>
