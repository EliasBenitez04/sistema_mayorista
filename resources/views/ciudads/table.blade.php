@include('partials.ref_table_style')

    <div class="card-body p-0 ref-table-card">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="ciudades-table">
                <thead class="text-center">
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th class="text-left">Descripción</th>
                        @canany(['ciudades edit', 'ciudades destroy'])
                            <th style="width: 150px;">Operaciones</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ciudad as $value)
                        <tr class="text-center align-middle">
                            <td><span class="ref-id">{{ $value->id_ciudad }}</span></td>
                            <td class="text-left ref-primary"><i class="fas fa-city text-muted mr-1"></i>{{ $value->ciu_descripcion }}</td>

                            @canany(['ciudades edit', 'ciudades destroy'])
                                <td>
                                    {!! Form::open(['route' => ['ciudades.destroy', $value->id_ciudad], 'method' => 'delete']) !!}
                                    <div class="ref-actions" role="group">
                                        @can('ciudades edit')
                                            <a href="{{ route('ciudades.edit', [$value->id_ciudad]) }}"
                                                class="btn btn-primary" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('ciudades destroy')
                                            {!! Form::button('<i class="fas fa-trash"></i>', [
                                                'type' => 'button',
                                                'class' => 'btn btn-danger alert-delete',
                                                'data-mensaje' => $value->ciu_descripcion,
                                                'title' => 'Inactivar',
                                            ]) !!}
                                        @endcan
                                    </div>
                                    {!! Form::close() !!}
                                </td>
                            @endcanany

                        </tr>
                    @empty
                        <tr>
                            <td colspan="@canany(['ciudades edit', 'ciudades destroy']) 3 @else 2 @endcanany"
                                class="text-center text-muted py-4">
                                No existen registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="ref-footer"><div class="ref-footer-info">Mostrando <strong>{{ $ciudad->firstItem() ?? 0 }}</strong> - <strong>{{ $ciudad->lastItem() ?? 0 }}</strong> de <strong>{{ $ciudad->total() }}</strong> registros</div><div>{{ $ciudad->links() }}</div></div>