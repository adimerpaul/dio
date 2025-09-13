<?php

namespace App\Http\Controllers;

use App\Models\Slim;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Intervention Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SlimFotografiaController extends Controller
{
    /** GET /api/slims/{slim}/fotografias */
    public function index(Request $request, Slim $slim)
    {
        $q    = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $per  = (int) $request->get('per_page', 12);

        $query = Fotografia::with('user:id,name,username')
            ->where('caseable_type', Slim::class)
            ->where('caseable_id', $slim->id);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('id')->paginate($per, ['*'], 'page', $page);
        return response()->json($rows);
    }

    /** POST /api/slims/{slim}/fotografias */
    public function store(Request $request, Slim $slim)
    {
        $request->validate([
            'file'        => ['required','image','mimes:jpg,jpeg,png,webp','max:10240'], // 10 MB
            'titulo'      => ['nullable','string','max:255'],
            'descripcion' => ['nullable','string'],
        ]);

        $disk = 'public';                // <- FORZAR PUBLIC
        $dir  = "slims/{$slim->id}/fotos";

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $nameNoExt    = pathinfo($originalName, PATHINFO_FILENAME);
        $extLower     = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        // Nombre base único
        $base = Str::slug($nameNoExt) . '-' . Str::random(6);

        $manager = new ImageManager(new Driver());

        // ==== Imagen principal (máx 2000px) ====
        $img = $manager->read($file->getPathname());
        $img->resizeDown(width: 2000, height: 2000);

        // Normalizamos extensión de salida
        $storedExt = in_array($extLower, ['jpg','jpeg','png','webp']) ? ($extLower === 'jpeg' ? 'jpg' : $extLower) : 'jpg';
        $storedName = "{$base}.{$storedExt}";
        $path       = "{$dir}/{$storedName}";

        if ($storedExt === 'jpg') {
            Storage::disk($disk)->put($path, (string) $img->toJpeg(quality: 85));
            $mime = 'image/jpeg';
        } elseif ($storedExt === 'png') {
            Storage::disk($disk)->put($path, (string) $img->toPng());
            $mime = 'image/png';
        } else { // webp
            Storage::disk($disk)->put($path, (string) $img->toWebp(quality: 80));
            $mime = 'image/webp';
        }

        // ==== Miniatura (máx 480px) ====
        $thumb = $manager->read($file->getPathname());
        $thumb->resizeDown(width: 480, height: 480);

        $thumbName = "{$base}-thumb.{$storedExt}";
        $thumbPath = "{$dir}/{$thumbName}";

        if ($storedExt === 'jpg') {
            Storage::disk($disk)->put($thumbPath, (string) $thumb->toJpeg(quality: 80));
        } elseif ($storedExt === 'png') {
            Storage::disk($disk)->put($thumbPath, (string) $thumb->toPng());
        } else {
            Storage::disk($disk)->put($thumbPath, (string) $thumb->toWebp(quality: 75));
        }

        // URLs públicas relativas (/storage/...)
        $url      = Storage::url($path);
        $thumbUrl = Storage::url($thumbPath);

        $bytes  = Storage::disk($disk)->size($path);
        $width  = $img->width();
        $height = $img->height();

        $foto = Fotografia::create([
            'caseable_id'   => $slim->id,
            'caseable_type' => Slim::class,
            'user_id'       => Auth::id(),

            'titulo'        => $request->string('titulo')->toString() ?: $nameNoExt,
            'descripcion'   => $request->get('descripcion'),

            'original_name' => $originalName,
            'stored_name'   => $storedName,
            'extension'     => $storedExt,
            'mime'          => $mime,
            'size_bytes'    => $bytes,

            'disk'          => $disk,
            'path'          => $path,
            'url'           => $url,

            'thumb_path'    => $thumbPath,
            'thumb_url'     => $thumbUrl,

            'width'         => $width,
            'height'        => $height,
        ]);

        return response()->json($foto->load('user:id,name,username'), 201);
    }

    /** DELETE /api/slims/fotografias/{fotografia} */
    public function destroy(Fotografia $fotografia)
    {
        // Asegura que la foto pertenece a un SLIM (polimórfico)
        if ($fotografia->caseable_type !== Slim::class) abort(404);

        try {
            if ($fotografia->path && Storage::disk($fotografia->disk)->exists($fotografia->path)) {
                Storage::disk($fotografia->disk)->delete($fotografia->path);
            }
            if ($fotografia->thumb_path && Storage::disk($fotografia->disk)->exists($fotografia->thumb_path)) {
                Storage::disk($fotografia->disk)->delete($fotografia->thumb_path);
            }
        } catch (\Throwable $e) {
            // opcional: \Log::warning($e->getMessage());
        }

        $fotografia->delete();

        return response()->json(['message' => 'Fotografía eliminada']);
    }
}
