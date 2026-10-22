@include('partials.ref_table_style')
<div class="card-body p-0 ref-table-card">
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0" id="permissions-table">
            <thead class="text-center">
                <tr>
                    <th>Permiso</th>
                    <th>Guard</th>
                    <th class="text-center" style="width: 120px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td class="ref-primary">\n                            <i class="fas fa-key text-secondary mr-1"></i>
                            {{ $permission->name }}
                        </td>

                        <td>
                            <span class="badge badge-info">
                                {{ $permission->guard_name }}
                            </span>
                        </td>

                        <td class="text-center">
                            {!! Form::open([
                                'route' => ['permissions.destroy', $permission->id],
                                'method' => 'delete',
                                'class' => 'd-inline form-delete',
                            ]) !!}
                            <div class="ref-actions">

                                @can('permissions edit')
                                    <a href="{{ route('permissions.edit', $permission->id) }}"
                                        class="btn btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('permissions destroy')
                                    {!! Form::button('<i class="fas fa-trash-alt"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger alert-delete',
                                        'title' => 'Eliminar',
                                    ]) !!}
                                @endcan

                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i>
                            No hay permisos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="ref-footer"><div class="ref-footer-info">Mostrando <strong>{{ $permissions->firstItem() ?? 0 }}</strong> - <strong>{{ $permissions->lastItem() ?? 0 }}</strong> de <strong>{{ $permissions->total() }}</strong> registros</div><div>@include('adminlte-templates::common.paginate', ['records' => $permissions])</div></div>