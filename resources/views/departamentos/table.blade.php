@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="departamentos-table">
            <thead class="text-center"><tr>
                <th style="width:80px">#</th><th class="text-left">Descripción</th>
                @canany(['departamentos edit', 'departamentos destroy']) <th style="width:150px">Operaciones</th> @endcanany
            </tr></thead>
            <tbody>
                @forelse ($departamento as $value)
                    <tr>
                        <td class="text-center"><span class="ref-id">{{ $value->id_departamento }}</span></td>
                        <td class="ref-primary"><i class="fas fa-map text-muted mr-1"></i>{{ $value->dep_descripcion }}</td>
                        @canany(['departamentos edit', 'departamentos destroy'])
                            <td>
                                {!! Form::open(['route'=>['Departamentos.destroy',$value->id_departamento],'method'=>'delete']) !!}
                                <div class="ref-actions">
                                    @can('departamentos edit')
                                        <a href="{{ route('Departamentos.edit', [$value->id_departamento]) }}" class="btn btn-primary" title="Editar"><i class="far fa-edit"></i></a>
                                    @endcan
                                    @can('departamentos destroy')
                                        {!! Form::button('<i class="fas fa-trash"></i>', ['type'=>'button','class'=>'btn btn-danger alert-delete','data-mensaje'=>$value->dep_descripcion,'title'=>'Inactivar']) !!}
                                    @endcan
                                </div>
                                {!! Form::close() !!}
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr><td colspan="@canany(['departamentos edit', 'departamentos destroy']) 3 @else 2 @endcanany" class="text-center text-muted py-4">No existen registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ref-footer">
        <div class="ref-footer-info">Mostrando <strong>{{ $departamento->firstItem() ?? 0 }}</strong> - <strong>{{ $departamento->lastItem() ?? 0 }}</strong> de <strong>{{ $departamento->total() }}</strong> registros</div>
        <div>{{ $departamento->links() }}</div>
    </div>
</div>
