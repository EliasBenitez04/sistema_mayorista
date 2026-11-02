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

        $query = DB::query()->fromSub($base, 'p')
            ->leftJoin('ecommerce_catalogo as e', 'e.codigo_base', '=', 'p.codigo_base')
            ->leftJoin('linea as l', 'l.linea_cod', '=', 'e.linea_cod')
            ->select('p.*', 'e.imagen', 'e.linea_cod', 'e.ot', 'e.valor_v', 'e.valor_c',
                'e.fecha_archivo', 'e.descripcion_archivo', 'l.linea_desc');

        $buscar = trim((string) $request->input('buscar', ''));
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('p.codigo_base', 'ILIKE', '%' . $buscar . '%')
                    ->orWhere('p.descripcion', 'ILIKE', '%' . $buscar . '%')
                    ->orWhere('e.ot', 'ILIKE', '%' . $buscar . '%')
                    ->orWhere('e.descripcion_archivo', 'ILIKE', '%' . $buscar . '%');
            });
        }

        if ($request->input('linea') === 'sin-linea') {
            $query->whereNull('e.linea_cod');
        } elseif (ctype_digit((string) $request->input('linea'))) {
            $query->where('e.linea_cod', (int) $request->input('linea'));
        }
        if ($request->input('imagen') === 'si') {
            $query->whereNotNull('e.imagen');
        } elseif ($request->input('imagen') === 'no') {
            $query->whereNull('e.imagen');
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
            'archivos.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'paquete_zip' => 'nullable|file|mimes:zip|max:102400',
        ], [
            'archivos.max' => 'Seleccioná como máximo 20 imágenes por carga, o utilizá un ZIP.',
            'archivos.*.max' => 'Cada imagen puede pesar hasta 8 MB.',
            'paquete_zip.max' => 'El ZIP puede pesar hasta 100 MB.',
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
                    if (($stat['size'] ?? 0) > 8 * 1024 * 1024) {
                        $resultado['omitidas']++;
                        $this->agregarError($errores, basename($nombre) . ': supera los 8 MB.');
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
                throw new \DomainException('Nombre no reconocido. Usá CODIGO V35 C13.388 OT26697 DESCRIPCION DD-MM-AA.jpg');
            }

            $codigo = $datos['codigo_base'];
            $existe = DB::table('articulos')->where(function ($q) use ($codigo) {
                $q->where('art_codigo', $codigo)
                    ->orWhere('art_codigo', 'LIKE', $codigo . '__');
            })->exists();
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
            if (strlen($contenido) > 8 * 1024 * 1024) {
                throw new \DomainException('La imagen supera los 8 MB.');
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
     * Ejemplo:
     * 100613678CV V35 C13.388 OT26697 BERMUDA ECO VARÓN 08-10-26.jpg
     * V y C se conservan textualmente, sin sobrescribir precios del ERP.
     */
    private function leerNombre(string $nombre): ?array
    {
        $base = trim(pathinfo($nombre, PATHINFO_FILENAME));
        $patron = '/^([0-9]{9}[A-Z]{2})\s+'
            . '(V[0-9]+(?:[.,][0-9]+)?)\s+'
            . '(C[0-9]+(?:[.,][0-9]+)?)\s+'
            . 'OT\s*([0-9]+)\s+'
            . '(.+?)\s+'
            . '([0-9]{2}-[0-9]{2}-[0-9]{2}(?:[0-9]{2})?)$/iu';

        if (preg_match($patron, $base, $m)) {
            $formato = strlen($m[6]) === 8 ? '!d-m-y' : '!d-m-Y';
            $fecha = \DateTimeImmutable::createFromFormat($formato, $m[6]);
            if (!$fecha || $fecha->format(substr($formato, 1)) !== $m[6]) {
                return null;
            }
            return [
                'codigo_base' => strtoupper($m[1]),
                'valor_v' => strtoupper($m[2]),
                'valor_c' => strtoupper($m[3]),
                'ot' => $m[4],
                'descripcion_archivo' => mb_substr(trim($m[5]), 0, 255),
                'fecha_archivo' => $fecha->format('Y-m-d'),
            ];
        }
        // Permite cargar también una foto nombrada únicamente con el código base.
        if (preg_match('/^([0-9]{9}[A-Z]{2})$/i', $base, $m)) {
            return [
                'codigo_base' => strtoupper($m[1]),
                'valor_v' => null, 'valor_c' => null, 'ot' => null,
                'descripcion_archivo' => null, 'fecha_archivo' => null,
            ];
        }
        return null;
    }

    public function asignarLinea(Request $request, string $codigo)
    {
        $request->validate(['linea_cod' => 'nullable|integer|exists:linea,linea_cod']);
        $codigo = strtoupper(trim($codigo));
        if (!preg_match('/^[A-Z0-9]{1,32}$/', $codigo)) {
            abort(404);
        }
        $existe = DB::table('articulos')->where(function ($q) use ($codigo) {
            $q->where('art_codigo', $codigo)->orWhere('art_codigo', 'LIKE', $codigo . '__');
        })->exists();
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
