@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="usuarios-table">
            <thead class="text-center"><tr>
                <th style="width:55px">#</th><th>Usuario</th><th class="text-left">Correo</th><th>CI</th>
                <th class="text-left">Dirección</th><th>Teléfono</th><th>Rol</th><th>Estado</th><th style="width:110px">Acciones</th>
            </tr></thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td class="text-center"><span class="ref-id">{{ $usuario->id }}</span></td>
                        <td class="ref-primary"><i class="fas fa-user text-muted mr-1"></i>{{ $usuario->name }}</td>
                        <td><i class="fas fa-envelope text-muted mr-1"></i>{{ $usuario->email }}</td>
                        <td class="text-center">{{ $usuario->ci }}</td>
                        <td>{{ $usuario->direccion ?? '—' }}</td>
                        <td class="text-center"><i class="fas fa-phone-alt text-muted mr-1"></i>{{ $usuario->telefono ?? '—' }}</td>
                        <td class="text-center"><span class="badge badge-primary">{{ $usuario->roles->first()->name ?? 'Sin rol' }}</span></td>
                        <td class="text-center">
                            @if ($usuario->estado === 'ACTIVO')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-ban"></i> Inactivo</span>
                            @endif
                        </td>
                        <td>
                            {!! Form::open(['route'=>['usuarios.destroy',$usuario->id],'method'=>'delete','class'=>'d-inline']) !!}
                            <div class="ref-actions">
                                @can('usuarios edit')
                                    <a href="{{ route('usuarios.edit',$usuario->id) }}" class="btn btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('usuarios destroy')
                                    {!! Form::button('<i class="fas fa-user-slash"></i>', ['type'=>'button','class'=>'btn btn-danger alert-delete','title'=>'Inactivar','data-mensaje'=>'el usuario','data-accion'=>'inactivar']) !!}
                                @endcan
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No existen registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ref-footer">
        <div class="ref-footer-info">Mostrando <strong>{{ $usuarios->firstItem() ?? 0 }}</strong> - <strong>{{ $usuarios->lastItem() ?? 0 }}</strong> de <strong>{{ $usuarios->total() }}</strong> registros</div>
        <div>{{ $usuarios->links() }}</div>
    </div>
</div>
