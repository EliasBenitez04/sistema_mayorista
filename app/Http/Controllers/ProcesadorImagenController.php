<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ProcesadorImagenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ia index')->only('index');
        $this->middleware('permission:ia create')->only('subir');
    }

    public function index()
    {
        return view('ia_prendas.index');
    }

    public function subir(Request $request)
    {
        $request->validate([
            'imagenes' => 'required',
            'imagenes.*' => 'image|max:10240'
        ]);

        $base = storage_path('app/public/prendas');

        $originales = $base . '/originales';
        $procesadas = $base . '/procesadas';

        File::ensureDirectoryExists($originales);
        File::ensureDirectoryExists($procesadas);

        File::cleanDirectory($originales);
        File::cleanDirectory($procesadas);

        // 🔹 Guardar nombres originales
        $imagenes = [];

        foreach ($request->file('imagenes') as $img) {

            $name = uniqid() . '.' . $img->getClientOriginalExtension();

            $img->move($originales, $name);

            $imagenes[] = $name;
        }

        /*
        -------------------------------------------------
        🔥 EJECUTAR REMBG
        -------------------------------------------------
        */
        $cmd = 'rembg p "' . $originales . '" "' . $procesadas . '" 2>&1';

        exec($cmd, $output, $code);

        if ($code !== 0) {
            return back()->with('error', 'Error IA al procesar imágenes')
                ->with('log', $output);
        }

        /*
        -------------------------------------------------
        🔥 CONVERTIR PNG → JPG (OBLIGATORIO)
        -------------------------------------------------
        */
        $preview = [];

        foreach ($imagenes as $nombre) {

            $nombreSinExt = pathinfo($nombre, PATHINFO_FILENAME);

            $pngPath = $procesadas . '/' . $nombreSinExt . '.png';

            $jpgPath = $procesadas . '/' . $nombreSinExt . '.jpg';

            if (File::exists($pngPath)) {

                $image = imagecreatefrompng($pngPath);

                // fondo blanco para JPG
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

                imagejpeg($bg, $jpgPath, 90);

                imagedestroy($image);
                imagedestroy($bg);
            }

            $preview[] = [
                'original' => asset('storage/prendas/originales/' . $nombre),
                'procesada' => asset('storage/prendas/procesadas/' . $nombreSinExt . '.jpg'),
            ];
        }

        session()->flash('preview_ia', $preview);

        alert()->success('Listo', 'Imágenes procesadas correctamente con IA');

        return back();
    }

    public function descargarZip()
    {
        $procesadas = storage_path('app/public/prendas/procesadas');
        $zipPath = storage_path('app/public/prendas/zip/prendas.zip');

        File::ensureDirectoryExists(dirname($zipPath));

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {

            // 🔥 SOLO JPG (NO PNG)
            foreach (File::files($procesadas) as $file) {

                if ($file->getExtension() === 'jpg') {
                    $zip->addFile($file->getRealPath(), $file->getFilename());
                }
            }

            $zip->close();
        }

        return response()->download($zipPath);
    }
}
