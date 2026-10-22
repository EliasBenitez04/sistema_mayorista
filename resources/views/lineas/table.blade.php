@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="lineas-table">
            <thead class="text-center"><tr>
                <th style="width:80px">#</th><th class="text-left">Descripción</th>
                @canany(['lineas edit','lineas destroy']) <th style="width:150px">Operaciones</th> @endcanany
            </tr></thead>
            <tbody>
                @forelse ($linea as $value)
                    <tr>
                        <td class="text-center"><span class="ref-id">{{ $value->linea_cod }}</span></td>
                        <td class="ref-primary"><i class="fas fa-list text-muted mr-1"></i>{{ $value->linea_desc }}</td>
                        @canany(['lineas edit','lineas destroy'])
                            <td>
                                {!! Form::open(['route'=>['lineas.destroy',$value->linea_cod],'method'=>'delete']) !!}
                                <div class="ref-actions">
                                    @can('lineas edit')
                                        <a href="{{ route('lineas.edit', [$value->linea_cod]) }}" class="btn btn-primary" title="Editar"><i class="far fa-edit"></i></a>
                                    @endcan
                                    @can('lineas destroy')
                                        {!! Form::button('<i class="fas fa-trash"></i>', ['type'=>'button','class'=>'btn btn-danger alert-delete','data-mensaje'=>$value->linea_desc,'title'=>'Inactivar']) !!}
                                    @endcan
                                </div>
                                {!! Form::close() !!}
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr><td colspan="@canany(['lineas edit','lineas destroy']) 3 @else 2 @endcanany" class="text-center text-muted py-4">No existen registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ref-footer">
        <div class="ref-footer-info">Mostrando <strong>{{ $linea->firstItem() ?? 0 }}</strong> - <strong>{{ $linea->lastItem() ?? 0 }}</strong> de <strong>{{ $linea->total() }}</strong> registros</div>
        <div>{{ $linea->links() }}</div>
    </div>
</div>
