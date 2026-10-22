@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="clientes-table">
            <thead class="text-center"><tr>
                <th style="width:55px">#</th><th style="width:130px">Nro. Documento</th>
                <th style="min-width:230px">Cliente</th><th style="min-width:220px">Dirección</th>
                <th style="width:130px">Teléfono</th><th style="width:150px">Departamento</th>
                <th style="width:150px">Ciudad</th><th style="width:110px">Operaciones</th>
            </tr></thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        <td class="text-center"><span class="ref-id">{{ $cliente->id_cliente }}</span></td>
                        <td class="text-center"><span class="ref-primary"><i class="fas fa-id-card text-muted mr-1"></i>{{ $cliente->cli_ci }}</span></td>
                        <td><div class="ref-primary"><i class="fas fa-user text-muted mr-1"></i>{{ $cliente->cli_nombre }} @if ($cliente->cli_apellido) {{ $cliente->cli_apellido }} @endif</div></td>
                        <td><span class="ref-secondary">@if ($cliente->cli_direccion)<i class="fas fa-map-marker-alt mr-1"></i>{{ $cliente->cli_direccion }}@else Sin dirección @endif</span></td>
                        <td class="text-center">@if ($cliente->cli_telefono)<i class="fas fa-phone text-muted mr-1"></i>{{ $cliente->cli_telefono }}@else — @endif</td>
                        <td class="text-center"><i class="fas fa-map text-muted mr-1"></i>{{ $cliente->dep_descripcion ?? '—' }}</td>
                        <td class="text-center"><i class="fas fa-city text-muted mr-1"></i>{{ $cliente->ciu_descripcion ?? '—' }}</td>
                        <td>
                            {!! Form::open(['route' => ['clientes.destroy', $cliente->id_cliente], 'method' => 'delete']) !!}
                            <div class="ref-actions">
                                @can('clientes edit')
                                    <a href="{{ route('clientes.edit', [$cliente->id_cliente]) }}" class="btn btn-primary" data-toggle="tooltip" title="Editar cliente"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('clientes destroy')
                                    {!! Form::button('<i class="far fa-trash-alt"></i>', ['type'=>'submit','class'=>'btn btn-danger alert-delete','data-toggle'=>'tooltip','title'=>'Eliminar cliente']) !!}
                                @endcan
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No existen registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ref-footer">
        <div class="ref-footer-info">Mostrando <strong>{{ $clientes->firstItem() ?? 0 }}</strong> - <strong>{{ $clientes->lastItem() ?? 0 }}</strong> de <strong>{{ $clientes->total() }}</strong> registros</div>
        <div>{{ $clientes->links() }}</div>
    </div>
</div>
