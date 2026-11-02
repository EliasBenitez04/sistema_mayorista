<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EcommerceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:articulos index'])->only('index');
        $this->middleware(['auth', 'permission:articulos create'])->only('importarForm', 'importar');
        $this->middleware(['auth', 'permission:articulos edit'])->only('asignarLinea');
        $this->middleware(['auth', 'permission:articulos destroy'])->only('eliminarImagen');
    }

    /**
     * Los artículos son variantes de 9 dígitos + color (2) + talle (2).
     * El catálogo agrupa por los primeros 11 caracteres sin alterar artículos.
     */
    private function codigoAgrupado(): string
    {
        return "CASE WHEN a.art_codigo ~ '^[0-9]{9}[A-Za-z]{2}[A-Za-z0-9]{2}$' "
            . "THEN SUBSTRING(a.art_codigo FROM 1 FOR 11) ELSE a.art_codigo END";
    }

    public function index(Request $request)
    {
        $request->validate([
            'buscar' => 'nullable|string|max:100',
            'linea' => 'nullable|string|max:30',
            'imagen' => 'nullable|in:si,no',
            'orden' => 'nullable|in:codigo,descripcion,precio_asc,precio_desc',
            'precio_min' => 'nullable|numeric|min:0',
            'precio_max' => 'nullable|numeric|min:0',
        ]);

        $lineas = DB::table('linea')->orderBy('linea_desc')->get();
        $clave = $this->codigoAgrupado();
        $base = DB::table('articulos as a')
            ->selectRaw($clave . ' AS codigo_base')
            ->selectRaw('MIN(a.art_descripcion) AS descripcion, MIN(a.prec_vent) AS precio_desde, MAX(a.prec_vent) AS precio_hasta, COUNT(*) AS variantes')
            ->groupByRaw($clave);

        // Fotos específicas de color prevalecen sobre fotografías genéricas (9 dígitos).
        $imagen = 'COALESCE(e.imagen, eg.imagen)';
        $linea = 'COALESCE(e.linea_cod, eg.linea_cod)';
        $ot = 'COALESCE(e.ot, eg.ot)';
        $descripcionArchivo = 'COALESCE(e.descripcion_archivo, eg.descripcion_archivo)';
        $query = DB::query()->fromSub($base, 'p')
            ->leftJoin('ecommerce_catalogo as e', 'e.codigo_base', '=', 'p.codigo_base')
            ->leftJoin('ecommerce_catalogo as eg', function ($join) {
                $join->on('eg.codigo_base', '=', DB::raw(
                    "CASE WHEN p.codigo_base ~ '^[0-9]{9}[A-Za-z]{2}$' "
                    . "THEN SUBSTRING(p.codigo_base FROM 1 FOR 9) ELSE NULL END"
                ));
            })
            ->leftJoin('linea as l', 'l.linea_cod', '=', DB::raw($linea))
            ->select('p.*', 'l.linea_desc')
            ->selectRaw($imagen . ' AS imagen, ' . $linea . ' AS linea_cod, '
                . $ot . ' AS ot, COALESCE(e.valor_v, eg.valor_v) AS valor_v, '
                . 'COALESCE(e.valor_c, eg.valor_c) AS valor_c, '
                . 'COALESCE(e.fecha_archivo, eg.fecha_archivo) AS fecha_archivo, '
                . $descripcionArchivo . ' AS descripcion_archivo');

        $buscar = trim((string) $request->input('buscar', ''));
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('p.codigo_base', 'ILIKE', '%' . $buscar . '%')
                    ->orWhere('p.descripcion', 'ILIKE', '%' . $buscar . '%')
                    ->orWhereRaw($ot . ' ILIKE ?', ['%' . $buscar . '%'])
                    ->orWhereRaw($descripcionArchivo . ' ILIKE ?', ['%' . $buscar . '%']);
            });
        }

        if ($request->input('linea') === 'sin-linea') {
            $query->whereRaw($linea . ' IS NULL');
        } elseif (ctype_digit((string) $request->input('linea'))) {
            $query->whereRaw($linea . ' = ?', [(int) $request->input('linea')]);
        }
        if ($request->input('imagen') === 'si') {
            $query->whereRaw($imagen . ' IS NOT NULL');
        } elseif ($request->input('imagen') === 'no') {
            $query->whereRaw($imagen . ' IS NULL');
        }
        if ($request->filled('precio_min')) {
            $query->where('p.precio_desde', '>=', $request->input('precio_min'));
        }
        if ($request->filled('precio_max')) {
            $query->where('p.precio_desde', '<=', $request->input('precio_max'));
        }

        switch ($request->input('orden', 'codigo')) {
            case 'descripcion':
                $query->orderBy('p.descripcion')->orderBy('p.codigo_base');
                break;
            case 'precio_asc':
                $query->orderBy('p.precio_desde')->orderBy('p.codigo_base');
                break;
            case 'precio_desc':
                $query->orderByDesc('p.precio_desde')->orderBy('p.codigo_base');
                break;
            default:
                $query->orderBy('p.codigo_base');
        }

        // La consulta de stock/talles se limita a los productos de la página actual.
        $productos = $query->paginate(18)->withQueryString();
        $codigos = $productos->pluck('codigo_base')->all();
        $variantes = collect();
        if ($codigos) {
            $variantes = DB::table('articulos as a')
                ->leftJoin('stock as s', 's.id_articulo', '=', 'a.id_articulo')
                ->whereIn(DB::raw($clave), $codigos)
                ->selectRaw($clave . ' AS codigo_base, a.art_codigo, COALESCE(SUM(GREATEST(s.cantidad, 0)), 0) AS disponible')
                ->groupByRaw($clave)
                ->groupBy('a.art_codigo')
                ->orderBy('a.art_codigo')
                ->get()
                ->groupBy('codigo_base');
        }

        // Stock: filtrar solamente sobre los productos recuperados cuando se solicita.
        // La paginación permanece por familias; se evita anunciar stock si no está registrado.
        return view('ecommerce.index', compact('productos', 'lineas', 'variantes'));
    }

    public function importarForm()
    {
        $lineas = DB::table('linea')->orderBy('linea_desc')->get();
        return view('ecommerce.importar', compact('lineas'));
    }

    public function importar(Request $request)
    {
        $request->validate([
            'linea_cod' => 'nullable|integer|exists:linea,linea_cod',
            'archivos' => 'nullable|array|max:20',
            'archivos.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:30720',
            'paquete_zip' => 'nullable|file|mimes:zip|max:204800',
        ], [
            'archivos.max' => 'Seleccioná como máximo 20 imágenes por carga, o utilizá un ZIP.',
            'archivos.*.max' => 'Cada imagen puede pesar hasta 30 MB.',
            'paquete_zip.max' => 'El ZIP puede pesar hasta 200 MB.',
        ]);

        $archivos = $request->file('archivos', []);
        $zipArchivo = $request->file('paquete_zip');
        if (empty($archivos) && !$zipArchivo) {
            throw ValidationException::withMessages([
                'archivos' => 'Seleccioná una o más imágenes o un paquete ZIP.'
            ]);
        }

        $resultado = ['nuevas' => 0, 'reemplazadas' => 0, 'omitidas' => 0];
        $errores = [];
        $linea = $request->filled('linea_cod') ? (int) $request->input('linea_cod') : null;

        foreach ($archivos as $archivo) {
            $this->procesarArchivo($archivo->getClientOriginalName(),
                file_get_contents($archivo->getRealPath()), $linea, $resultado, $errores);
        }

        if ($zipArchivo) {
            if (!class_exists(\ZipArchive::class)) {
                throw ValidationException::withMessages([
                    'paquete_zip' => 'La extensión ZIP de PHP no está habilitada en este servidor.'
                ]);
            }
            $zip = new \ZipArchive();
            if ($zip->open($zipArchivo->getRealPath()) !== true) {
                throw ValidationException::withMessages(['paquete_zip' => 'No se pudo abrir el ZIP.']);
            }
            try {
                if ($zip->numFiles > 500) {
                    throw ValidationException::withMessages([
                        'paquete_zip' => 'El ZIP admite como máximo 500 entradas por carga.'
                    ]);
                }
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    $nombre = $stat['name'] ?? '';
                    if ($nombre === '' || substr($nombre, -1) === '/' || strpos($nombre, '__MACOSX/') !== false) {
                        continue;
                    }
                    if (!preg_match('/\.(jpe?g|png|webp)$/i', $nombre)) {
                        continue;
                    }
                    if (($stat['size'] ?? 0) > 30 * 1024 * 1024) {
                        $resultado['omitidas']++;
                        $this->agregarError($errores, basename($nombre) . ': supera los 30 MB.');
                        continue;
                    }
                    $contenido = $zip->getFromIndex($i);
                    $this->procesarArchivo(basename(str_replace('\\', '/', $nombre)),
                        $contenido === false ? '' : $contenido, $linea, $resultado, $errores);
                }
            } finally {
                $zip->close();
            }
        }

        return redirect()->route('ecommerce.importar.form')
            ->with('resultado_importacion', $resultado)
            ->with('errores_importacion', $errores);
    }

    private function procesarArchivo(string $nombre, string $contenido, ?int $linea, array &$resultado, array &$errores): void
    {
        try {
            $datos = $this->leerNombre($nombre);
            if (!$datos) {
                throw new \DomainException('Nombre no reconocido. El archivo debe comenzar con un código de 9 dígitos, con color opcional. V, C, OT, descripción y fecha pueden faltar.');
            }

            $codigo = $datos['codigo_base'];
            $patronArticulo = strlen($codigo) === 9
                ? '^' . $codigo . '(?:[A-Z]{2}(?:[A-Z0-9]{2})?)?$'
                : '^' . $codigo . '(?:[A-Z0-9]{2})?$';
            $existe = DB::table('articulos')
                ->whereRaw('art_codigo ~* ?', [$patronArticulo])->exists();
            if (!$existe) {
                throw new \DomainException('No se encontró ese código base en artículos.');
            }

            $info = $contenido !== '' ? @getimagesizefromstring($contenido) : false;
            $extensiones = [
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_WEBP => 'webp',
            ];
            if (!$info || !isset($extensiones[$info[2]])) {
                throw new \DomainException('El archivo no contiene una imagen JPG, PNG o WEBP válida.');
            }
            if (strlen($contenido) > 30 * 1024 * 1024) {
                throw new \DomainException('La imagen supera los 30 MB.');
            }

            $anterior = DB::table('ecommerce_catalogo')->where('codigo_base', $codigo)->first();
            $lineaFinal = $linea;
            if (!$lineaFinal && $anterior && $anterior->linea_cod) {
                $lineaFinal = (int) $anterior->linea_cod;
            }
            if (!$lineaFinal && $datos['ot']) {
                $lineaFinal = DB::table('carga_fotos')
                    ->where('fot_ot', $datos['ot'])->value('linea_cod');
            }
            if (!$lineaFinal && $datos['descripcion_archivo']) {
                $lineaFinal = $this->lineaDesdeDescripcion($datos['descripcion_archivo']);
            }

            $ruta = 'ecommerce/' . $codigo . '_' . bin2hex(random_bytes(8)) . '.' . $extensiones[$info[2]];
            if (!Storage::disk('public')->put($ruta, $contenido)) {
                throw new \RuntimeException('No se pudo guardar la imagen en el disco público.');
            }
            try {
                $ahora = now();
                DB::table('ecommerce_catalogo')->upsert([[
                    'codigo_base' => $codigo,
                    'linea_cod' => $lineaFinal,
                    'imagen' => $ruta,
                    'nombre_archivo' => mb_substr($nombre, 0, 255),
                    'valor_v' => $datos['valor_v'],
                    'valor_c' => $datos['valor_c'],
                    'ot' => $datos['ot'],
                    'descripcion_archivo' => $datos['descripcion_archivo'],
                    'fecha_archivo' => $datos['fecha_archivo'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]], ['codigo_base'], [
                    'linea_cod', 'imagen', 'nombre_archivo', 'valor_v', 'valor_c',
                    'ot', 'descripcion_archivo', 'fecha_archivo', 'updated_at'
                ]);
            } catch (\Throwable $e) {
                Storage::disk('public')->delete($ruta);
                throw $e;
            }
            if ($anterior && $anterior->imagen && $anterior->imagen !== $ruta) {
                Storage::disk('public')->delete($anterior->imagen);
            }
            $resultado[$anterior && $anterior->imagen ? 'reemplazadas' : 'nuevas']++;
        } catch (\DomainException $e) {
            $resultado['omitidas']++;
            $this->agregarError($errores, mb_substr($nombre, 0, 80) . ': ' . $e->getMessage());
        } catch (\Throwable $e) {
            $resultado['omitidas']++;
            Log::error('Error al importar imagen e-commerce', ['archivo' => $nombre, 'error' => $e->getMessage()]);
            $this->agregarError($errores, mb_substr($nombre, 0, 80) . ': no se pudo procesar.');
        }
    }

    private function agregarError(array &$errores, string $mensaje): void
    {
        if (count($errores) < 30) {
            $errores[] = $mensaje;
        }
    }

    /**
     * Nombres flexibles: acepta 9, 11 y 13 caracteres de código.
     * Los valores V/C, OT, fecha y descripción son opcionales y pueden cambiar de orden.
     */
    private function leerNombre(string $nombre): ?array
    {
        $base = trim(pathinfo($nombre, PATHINFO_FILENAME));
        if (!preg_match('/^([0-9]{9}(?:[A-Z]{2}(?:[A-Z0-9]{2})?)?)(?:\s+|$)(.*)$/iu', $base, $m)) {
            return null;
        }
        $codigo = strtoupper($m[1]);
        if (strlen($codigo) === 13) {
            $codigo = substr($codigo, 0, 11);
        }
        $resto = trim($m[2]);
        $valorV = null;
        $valorC = null;
        $ot = null;
        $fecha = null;

        // Puede aparecer delante de "NENA..." o al final; también es opcional.
        if (preg_match('/(?<![0-9])([0-9]{2}[-\/.][0-9]{2}[-\/.](?:[0-9]{4}|[0-9]{2}))(?![0-9])/u', $resto, $d)) {
            $fechaTexto = str_replace(['/', '.'], '-', $d[1]);
            $formato = strlen($fechaTexto) === 8 ? '!d-m-y' : '!d-m-Y';
            $fechaObj = \DateTimeImmutable::createFromFormat($formato, $fechaTexto);
            if (!$fechaObj || $fechaObj->format(substr($formato, 1)) !== $fechaTexto) {
                return null;
            }
            $fecha = $fechaObj->format('Y-m-d');
            $resto = preg_replace('/(?<![0-9])[0-9]{2}[-\/.][0-9]{2}[-\/.](?:[0-9]{4}|[0-9]{2})(?![0-9])/u',
                ' ', $resto, 1);
        }

        if (preg_match('/(?:^|\s)V\s*([0-9]+(?:[.,][0-9]+)?)(?=\s|$)/iu', $resto, $v)) {
            $valorV = 'V' . $v[1];
            $resto = preg_replace('/(?:^|\s)V\s*[0-9]+(?:[.,][0-9]+)?(?=\s|$)/iu', ' ', $resto, 1);
        } elseif (preg_match('/^\s*([0-9]+(?:[.,][0-9]+)?)\s+(?=C\s*[0-9])/iu', $resto, $v)) {
            // Archivos heredados con "79 C28062..." en vez de "V79 C28062...".
            $valorV = 'V' . $v[1];
            $resto = preg_replace('/^\s*[0-9]+(?:[.,][0-9]+)?\s+(?=C\s*[0-9])/iu', '', $resto, 1);
        }

        if (preg_match('/(?:^|\s)C\s*([0-9]+(?:[.,][0-9]+)?)(?=\s|$)/iu', $resto, $c)) {
            $valorC = 'C' . $c[1];
            $resto = preg_replace('/(?:^|\s)C\s*[0-9]+(?:[.,][0-9]+)?(?=\s|$)/iu', ' ', $resto, 1);
        }
        if (preg_match('/(?:^|\s)OT\s*([0-9]+)(?=\s|$)/iu', $resto, $o)) {
            $ot = $o[1];
            $resto = preg_replace('/(?:^|\s)OT\s*[0-9]+(?=\s|$)/iu', ' ', $resto, 1);
        }

        $descripcion = trim(preg_replace('/\s+/u', ' ', $resto));
        return [
            'codigo_base' => $codigo,
            'valor_v' => $valorV,
            'valor_c' => $valorC,
            'ot' => $ot,
            'descripcion_archivo' => $descripcion === '' ? null : mb_substr($descripcion, 0, 255),
            'fecha_archivo' => $fecha,
        ];
    }

    /**
     * Solo deduce líneas por coincidencia exacta con el prefijo del nombre.
     * "NENA REMERA..." => NENA, "JUV NENA REMERA..." => JUV NENA.
     */
    private function lineaDesdeDescripcion(string $descripcion): ?int
    {
        $normalizar = function (string $texto): string {
            return trim((string) preg_replace('/[^A-Z0-9]+/u', ' ', mb_strtoupper(\Illuminate\Support\Str::ascii($texto))));
        };
        $texto = $normalizar($descripcion);
        if ($texto === '') {
            return null;
        }
        $mejor = null;
        $longitud = 0;
        foreach (DB::table('linea')->get(['linea_cod', 'linea_desc']) as $linea) {
            $etiqueta = $normalizar($linea->linea_desc);
            if ($etiqueta !== '' && ($texto === $etiqueta || strpos($texto, $etiqueta . ' ') === 0)
                && strlen($etiqueta) > $longitud) {
                $mejor = (int) $linea->linea_cod;
                $longitud = strlen($etiqueta);
            }
        }
        return $mejor;
    }

    public function asignarLinea(Request $request, string $codigo)
    {
        $request->validate(['linea_cod' => 'nullable|integer|exists:linea,linea_cod']);
        $codigo = strtoupper(trim($codigo));
        if (!preg_match('/^[A-Z0-9]{1,32}$/', $codigo)) {
            abort(404);
        }
        $patronArticulo = strlen($codigo) === 9
            ? '^' . $codigo . '(?:[A-Z]{2}(?:[A-Z0-9]{2})?)?$'
            : '^' . $codigo . '(?:[A-Z0-9]{2})?$';
        $existe = DB::table('articulos')->whereRaw('art_codigo ~* ?', [$patronArticulo])->exists();
        if (!$existe) {
            abort(404);
        }
        $anterior = DB::table('ecommerce_catalogo')->where('codigo_base', $codigo)->first();
        if ($anterior) {
            DB::table('ecommerce_catalogo')->where('codigo_base', $codigo)->update([
                'linea_cod' => $request->filled('linea_cod') ? (int) $request->linea_cod : null,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('ecommerce_catalogo')->insert([
                'codigo_base' => $codigo,
                'linea_cod' => $request->filled('linea_cod') ? (int) $request->linea_cod : null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        return back()->with('ecommerce_mensaje', 'Línea asignada correctamente.');
    }

    public function eliminarImagen(string $codigo)
    {
        $codigo = strtoupper(trim($codigo));
        $registro = DB::table('ecommerce_catalogo')->where('codigo_base', $codigo)->first();
        if (!$registro || !$registro->imagen) {
            return back()->with('ecommerce_mensaje', 'Ese producto no tiene imagen.');
        }
        DB::table('ecommerce_catalogo')->where('id', $registro->id)->update([
            'imagen' => null,
            'nombre_archivo' => null,
            'updated_at' => now(),
        ]);
        Storage::disk('public')->delete($registro->imagen);
        return back()->with('ecommerce_mensaje', 'Imagen retirada del catálogo.');
    }
}
