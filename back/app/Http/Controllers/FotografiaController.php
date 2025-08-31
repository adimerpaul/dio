<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Intervention Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FotografiaController extends Controller
{
    // GET /api/casos/{caso}/fotografias
    public function index(Request $request, Caso $caso)
    {
        $q    = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $per  = (int) $request->get('per_page', 12);

        $query = Fotografia::with('user')->where('caso_id', $caso->id);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('id')->paginate($per, ['*'], 'page', $page);
        return response()->json($rows);
    }

    // POST /api/casos/{caso}/fotografias
    public function store(Request $request, Caso $caso)
    {
        $request->validate([
            'file'        => ['required','image','mimes:jpg,jpeg,png,webp','max:10240'],
            'titulo'      => ['nullable','string','max:255'],
            'descripcion' => ['nullable','string'],
        ]);

        $disk = 'public';
        $dir  = "casos/{$caso->id}/fotos";

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $nameNoExt    = pathinfo($originalName, PATHINFO_FILENAME);
        $extLower     = strtolower($file->getClientOriginalExtension()); // jpg|jpeg|png|webp
        $base         = Str::slug($nameNoExt) . '-' . Str::random(6);

        $manager = new ImageManager(new Driver());

        // Imagen base
        $img = $manager->read($file->getPathname());
        // Reducir manteniendo aspecto (máx 2000 px)
        $img->resizeDown(width: 2000, height: 2000);

        // Elegir encoder según formato de entrada
        $storedExt = $extLower === 'jpeg' ? 'jpg' : $extLower;
        if (!in_array($storedExt, ['jpg','png','webp'])) {
            $storedExt = 'jpg';
        }

        $storedName = "{$base}.{$storedExt}";
        $path       = "{$dir}/{$storedName}";

        if ($storedExt === 'jpg') {
            Storage::disk($disk)->put($path, (string) $img->toJpeg(quality: 85));
            $mime = 'image/jpeg';
        } elseif ($storedExt === 'png') {
            Storage::disk($disk)->put($path, (string) $img->toPng()); // compresión interna
            $mime = 'image/png';
        } else { // webp
            Storage::disk($disk)->put($path, (string) $img->toWebp(quality: 80));
            $mime = 'image/webp';
        }

        // Miniatura 480 px (siempre en el mismo formato del principal)
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

        // URLs RELATIVAS públicas (luego el front arma absolutas)
        $url      = Storage::url($path);      // "/storage/...."
        $thumbUrl = Storage::url($thumbPath); // "/storage/...."

        $bytes  = Storage::disk($disk)->size($path);
        $width  = $img->width();
        $height = $img->height();

        $foto = Fotografia::create([
            'caso_id'       => $caso->id,
            'user_id'       => Auth::id(),
            'titulo'        => $request->get('titulo') ?: $nameNoExt,
            'descripcion'   => $request->get('descripcion'),
            'original_name' => $originalName,
            'stored_name'   => $storedName,
            'extension'     => $storedExt,
            'mime'          => $mime,
            'size_bytes'    => $bytes,
            'disk'          => $disk,
            'path'          => $path,
            'url'           => $url,       // "/storage/...."
            'thumb_path'    => $thumbPath,
            'thumb_url'     => $thumbUrl,  // "/storage/...."
            'width'         => $width,
            'height'        => $height,
        ]);

        return response()->json(['message' => 'Foto subida', 'data' => $foto], 201);
    }

    // DELETE /api/fotografias/{fotografia}
    public function destroy(Fotografia $fotografia)
    {
        if ($fotografia->path && Storage::disk($fotografia->disk)->exists($fotografia->path)) {
            Storage::disk($fotografia->disk)->delete($fotografia->path);
        }
        if ($fotografia->thumb_path && Storage::disk($fotografia->disk)->exists($fotografia->thumb_path)) {
            Storage::disk($fotografia->disk)->delete($fotografia->thumb_path);
        }
        $fotografia->delete();

        return response()->json(['message' => 'Eliminado']);
    }
}
