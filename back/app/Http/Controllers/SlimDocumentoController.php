<?php

namespace App\Http\Controllers;

use App\Models\Slim;            // Ajusta el namespace si tu modelo está en otro lugar
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SlimDocumentoController extends Controller
{
    /** Lista paginada de documentos de un SLIM */
    public function index(Request $request, Slim $slim)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $query = Documento::query()
            ->with(['user:id,name,username'])
            ->where('caseable_type', Slim::class)
            ->where('caseable_id', $slim->id);

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('categoria', 'like', $like)
                    ->orWhere('descripcion', 'like', $like)
                    ->orWhere('original_name', 'like', $like)
                    ->orWhere('stored_name', 'like', $like)
                    ->orWhere('extension', 'like', $like)
                    ->orWhere('mime', 'like', $like)
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like)
                            ->orWhere('username', 'like', $like);
                    });
            });
        }

        $rows = $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($rows);
    }

    /** Subir archivo a un SLIM (crear Documento) */
    public function store(Request $request, Slim $slim)
    {
        $request->validate([
            'file'        => ['required','file','max:20480'], // 20 MB
            'titulo'      => ['nullable','string','max:255'],
            'categoria'   => ['nullable','string','max:100'],
            'descripcion' => ['nullable','string','max:2000'],
        ]);

        $file = $request->file('file');

        $disk = config('filesystems.default', 'public');
        $dir  = "slims/{$slim->id}/docs";

        $origName  = $file->getClientOriginalName();
        $ext       = strtolower($file->getClientOriginalExtension());
        $mime      = $file->getClientMimeType();
        $sizeBytes = $file->getSize();

        // nombre único
        $storedName = date('Ymd_His') . '_' . Str::random(8) . ($ext ? ".{$ext}" : '');

        // guardar
        $path = $dir . '/' . $storedName;
        Storage::disk($disk)->putFileAs($dir, $file, $storedName);

        $doc = Documento::create([
            'caseable_id'   => $slim->id,
            'caseable_type' => Slim::class,
            'user_id'       => $request->user()->id,
            'titulo'        => $request->input('titulo') ?: pathinfo($origName, PATHINFO_FILENAME),
            'categoria'     => $request->input('categoria'),
            'descripcion'   => $request->input('descripcion'),
            'original_name' => $origName,
            'stored_name'   => $storedName,
            'extension'     => $ext,
            'mime'          => $mime,
            'size_bytes'    => $sizeBytes,
            'disk'          => $disk,
            'path'          => $path,
            'url'           => Storage::disk($disk)->url($path),
        ]);

        return response()->json($doc->load('user:id,name,username'), 201);
    }

    /** Ver metadatos simples (opcional) */
    public function show(Documento $documento)
    {
        $this->ensureSlim($documento);
        return response()->json($documento->load('user:id,name,username'));
    }

    /** Actualizar metadatos (título/categoría/descripción) */
    public function update(Request $request, Documento $documento)
    {
        $this->ensureSlim($documento);

        $data = $request->validate([
            'titulo'      => ['nullable','string','max:255'],
            'categoria'   => ['nullable','string','max:100'],
            'descripcion' => ['nullable','string','max:2000'],
        ]);

        $documento->update($data);

        return response()->json([
            'message' => 'Documento actualizado',
            'data'    => $documento->fresh()->load('user:id,name,username')
        ]);
    }

    /** Eliminar documento (borra archivo + soft delete del registro) */
    public function destroy(Documento $documento)
    {
        $this->ensureSlim($documento);

        try {
            if ($documento->path && $documento->disk && Storage::disk($documento->disk)->exists($documento->path)) {
                Storage::disk($documento->disk)->delete($documento->path);
            }
        } catch (\Throwable $e) {
            // puedes loguear si quieres
        }

        $documento->delete();

        return response()->json(['message' => 'Documento eliminado'], 200);
    }

    /** Descargar (force download) */
    public function download(Documento $documento)
    {
        $this->ensureSlim($documento);

        if (!Storage::disk($documento->disk)->exists($documento->path)) {
            abort(404, 'Archivo no encontrado');
        }

        $name = $documento->original_name ?: $documento->stored_name;
        return Storage::disk($documento->disk)->download($documento->path, $name);
    }

    /** Ver inline (PDF/imágenes) */
    public function view(Documento $documento)
    {
        $this->ensureSlim($documento);

        if (!Storage::disk($documento->disk)->exists($documento->path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Mostrar inline; para discos locales funciona response(), para S3 será mejor URL temporal.
        return Storage::disk($documento->disk)->response(
            $documento->path,
            $documento->original_name ?: $documento->stored_name,
            [
                'Content-Type'        => $documento->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.($documento->original_name ?: $documento->stored_name).'"'
            ]
        );
    }

    /** Asegura que el documento pertenece a SLIM (por seguridad) */
    protected function ensureSlim(Documento $documento): void
    {
        if ($documento->caseable_type !== Slim::class) {
            abort(404);
        }
    }
}
