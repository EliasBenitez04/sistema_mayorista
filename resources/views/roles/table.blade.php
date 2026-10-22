@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="roles-table">
                <thead class="text-center">
                    <tr>
                        <th>Rol</th>
                        <th>Permisos</th>
                        @canany(['roles edit', 'roles destroy'])
                            <th style="width: 150px;">Operaciones</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr class="text-center align-middle">
                            <td class="text-left ref-primary"><i class="fas fa-user-shield text-muted mr-1"></i>{{ $role->name }}</td>
                            <td class="text-left"><span class="badge badge-info">{{ $role->guard_name }}</span></td>

                            @canany(['roles edit', 'roles destroy'])
                                <td>
                                    {!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'delete']) !!}
                                    <div class="ref-actions" role="group">
                                        @can('roles edit')
                                            <a href="{{ route('roles.edit', [$role->id]) }}" class="btn btn-primary"
                                                title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('roles destroy')
                                            {!! Form::button('<i class="fas fa-trash"></i>', [
                                                'type' => 'button',
                                                'class' => 'btn btn-danger btn-sg alert-delete',
                                                'title' => 'Eliminar',
                                            ]) !!}
                                        @endcan
                                    </div>
                                    {!! Form::close() !!}
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="@canany(['roles edit', 'roles destroy']) 3 @else 2 @endcanany"
                                class="text-center text-muted py-4">
                                No existen registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="ref-footer"><div class="ref-footer-info">Mostrando <strong>{{ $roles->firstItem() ?? 0 }}</strong> - <strong>{{ $roles->lastItem() ?? 0 }}</strong> de <strong>{{ $roles->total() }}</strong> registros</div><div>@include('adminlte-templates::common.paginate', ['records' => $roles])</div></div>