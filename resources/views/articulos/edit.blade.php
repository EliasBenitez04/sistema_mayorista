@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        Editar Articulos
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($articulo, [
                'route' => ['articulos.update', $articulo->id_articulo],
                'method' => 'patch',
                'files' => true,
                'class' => 'confirm-submit'
            ]) !!}

            <div class="card-body">

                @include('sweetalert::alert')

                <div class="row">

                    @include('articulos.fields')

                    @php
                        $codigoEdicion = strtoupper(trim((string) $articulo->art_codigo));
                        $tieneFamilia = preg_match('/^([0-9]{9})([A-Z]{2})([A-Z0-9]{2})$/', $codigoEdicion, $componentesCodigo);
                        $cantidadFamilia = $tieneFamilia
                            ? \Illuminate\Support\Facades\DB::table('articulos')
                                ->where('art_codigo', 'like', $componentesCodigo[1] . $componentesCodigo[2] . '%')
                                ->get(['art_codigo'])
                                ->filter(function ($registro) use ($componentesCodigo) {
                                    return preg_match('/^([0-9]{9})([A-Z]{2})([A-Z0-9]{2})$/', strtoupper($registro->art_codigo), $p)
                                        && $p[1] === $componentesCodigo[1] && $p[2] === $componentesCodigo[2];
                                })->count()
                            : 0;
                    @endphp
                    @if($tieneFamilia)
                        <div class="col-12 mb-3">
                            <div class="alert mb-0" style="background:#eef5ff;border:1px solid #bfd6fa;border-radius:10px;color:#1e467b">
                                <label class="d-flex align-items-start mb-1" style="gap:10px;cursor:pointer">
                                    <input type="checkbox" name="actualizar_familia" value="1"
                                        {{ old('actualizar_familia', '1') == '1' ? 'checked' : '' }}
                                        style="margin-top:4px;min-width:17px;height:17px">
                                    <span><strong>Aplicar cambios a todos los talles de este color</strong>
                                        <span class="d-block mt-1" style="font-size:12px">
                                            Familia {{ $componentesCodigo[1] }} · Color {{ $componentesCodigo[2] }} · {{ $cantidadFamilia }} variantes.
                                            Actualiza descripción, costo, venta e IVA en todas ellas.
                                            Al cambiar el color del código (por ejemplo, GR02 a VD02), conserva cada talle: GR01 → VD01, GR02 → VD02...
                                        </span>
                                    </span>
                                </label>
                                <small class="d-block mt-2">Desmarcá esta opción para editar únicamente el artículo actual.</small>
                            </div>
                        </div>
                    @else
                        <div class="col-12 mb-3"><div class="alert alert-light border mb-0">
                            Este código no corresponde al formato de familia de 9 dígitos + 2 letras de color + 2 caracteres de talle.
                            La edición afectará únicamente a este artículo.
                        </div></div>
                    @endif


                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('articulos.index') }}" class="btn btn-default"> Cancelar </a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
