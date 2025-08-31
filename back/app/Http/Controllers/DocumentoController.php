<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    // GET /api/casos/{caso}/documentos
    public function index(Request $request, Caso $caso)
    {
        $q   = trim($request->get('q', ''));
        $per = (int) $request->get('per_page', 10);

        $rows = Documento::with('user')
            ->where('caso_id', $caso->id)
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('titulo', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%")
                        ->orWhere('original_name', 'like', "%{$q}%")
                        ->orWhere('categoria', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($per);

        return response()->json($rows);
    }

    // POST /api/casos/{caso}/documentos  (multipart/form-data)
    public function store(Request $request, Caso $caso)
    {
        $request->validate([
            'file'        => ['required','file','max:20480', // 20MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,txt,zip,jpg,jpeg,png,webp'
            ],
            'titulo'      => ['nullable','string','max:255'],
            'categoria'   => ['nullable','string','max:40'],
            'descripcion' => ['nullable','string'],
        ]);

        $file = $request->file('file');

        $disk = 'public';
        $dir  = "casos/{$caso->id}/documentos";
        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());
        $storedName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $ext;

        $path = $file->storeAs($dir, $storedName, $disk);
        $url  = Storage::disk($disk)->url($path);

        $doc = Documento::create([
            'caso_id'       => $caso->id,
            'user_id'       => Auth::id(),
            'titulo'        => $request->get('titulo') ?: pathinfo($originalName, PATHINFO_FILENAME),
            'categoria'     => $request->get('categoria'),
            'descripcion'   => $request->get('descripcion'),

            'original_name' => $originalName,
            'stored_name'   => $storedName,
            'extension'     => $ext,
            'mime'          => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'disk'          => $disk,
            'path'          => $path,
            'url'           => $url,
        ]);

        return response()->json(['message' => 'Archivo subido', 'data' => $doc], 201);
    }

    // GET /api/documentos/{documento}
    public function show(Documento $documento)
    {
        $documento->load('user','caso');
        return response()->json($documento);
    }

    // PUT /api/documentos/{documento}  (solo metadatos)
    public function update(Request $request, Documento $documento)
    {
        $data = $request->validate([
            'titulo'      => ['nullable','string','max:255'],
            'categoria'   => ['nullable','string','max:40'],
            'descripcion' => ['nullable','string'],
        ]);
        $documento->update($data);
        return response()->json(['message'=>'Actualizado','data'=>$documento]);
    }

    // DELETE /api/documentos/{documento}
    public function destroy(Documento $documento)
    {
        // borra del storage y marca soft delete
        if (Storage::disk($documento->disk)->exists($documento->path)) {
            Storage::disk($documento->disk)->delete($documento->path);
        }
        $documento->delete();
        return response()->json(['message'=>'Eliminado']);
    }

    // GET /api/documentos/{documento}/download
    public function download(Documento $documento)
    {
        if (! Storage::disk($documento->disk)->exists($documento->path)) {
            abort(404, 'Archivo no encontrado');
        }
        $stream = Storage::disk($documento->disk)->readStream($documento->path);
        return Response::stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $documento->mime ?: 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$documento->original_name.'"',
        ]);
    }

    // GET /api/documentos/{documento}/view (inline)
    public function view(Documento $documento)
    {
        if (! Storage::disk($documento->disk)->exists($documento->path)) {
            abort(404, 'Archivo no encontrado');
        }
        $stream = Storage::disk($documento->disk)->readStream($documento->path);
        return Response::stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $documento->mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$documento->original_name.'"',
        ]);
    }
}
