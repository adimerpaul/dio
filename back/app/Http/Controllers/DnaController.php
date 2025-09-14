<?php

namespace App\Http\Controllers;

use App\Models\Dna;
use App\Models\DnaMenor;
use App\Models\DnaFamiliar;
use App\Models\Psicologica;
use App\Models\InformeLegal;
use App\Models\Documento;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DnaController extends Controller
{

    public function docIndex(Request $request, Dna $dna)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = $dna->documentos()->with('user:id,name,username');

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('categoria', 'like', $like)
                    ->orWhere('descripcion', 'like', $like)
                    ->orWhere('original_name', 'like', $like)
                    ->orWhere('mime', 'like', $like)
                    ->orWhere('extension', 'like', $like);
            });
        }

        $rows = $query->orderByDesc('id')->paginate($perPage)->appends($request->query());

        // Agrega size_human a cada ítem (para que el front lo muestre)
        $rows->getCollection()->transform(function($d){
            $d->size_human = $this->humanBytes($d->size_bytes);
            return $d;
        });

        return response()->json($rows);
    }

    /** Helper privado para tamaño legible */
    private function humanBytes($bytes)
    {
        if (!$bytes || $bytes <= 0) return null;
        $u = ['B','KB','MB','GB','TB'];
        $i = (int) floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), 2).' '.$u[$i];
    }

    public function psicoPdf(\App\Models\Psicologica $psicologica)
    {
        abort_unless($psicologica->caseable_type === \App\Models\Dna::class, 404);

        $psicologica->load('user:id,name');
        $caso = \App\Models\Dna::findOrFail($psicologica->caseable_id);

        // Usa dompdf (barryvdh/laravel-dompdf). Si ya lo tienes en SLIM, esto funciona igual.
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('letter'); // o 'a4'
        $pdf->loadView('pdf.dna.psicologica', [
            'sesion' => $psicologica,
            'caso'   => $caso,
        ]);

        $filename = 'DNA-Sesion-'.$psicologica->id.'.pdf';
        return $pdf->stream($filename);
    }

    /* ==========================
     * CRUD principal de Dna
     * ========================== */
    public function index(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $perPage = max(5, min((int) $request->get('per_page', 10), 100));

        $query = Dna::query()
            ->with(['user:id,name', 'abogado:id,name'])
            ->withCount(['menores', 'familiares', 'psicologicas', 'informesLegales', 'documentos', 'fotografias'])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('codigo', 'like', $like)
                    ->orWhere('tipo_proceso', 'like', $like)
                    ->orWhere('denunciante_nombre', 'like', $like)
                    ->orWhere('denunciado_nombre', 'like', $like)
                    ->orWhere('zona', 'like', $like)
                    ->orWhere('descripcion', 'like', $like);
            });
        }

        return $query->paginate($perPage)->appends($request->query());
    }

    public function store(Request $request)
    {
        $rules = [
            'codigo'         => ['nullable','string','max:50'],
            'fecha_atencion' => ['nullable','date'],
            'tipo_proceso'   => ['required', Rule::in(['PROCESO_PENAL','PROCESO_FAMILIAR','PROCESO_NNA','APOYO_INTEGRAL'])],
            'principal'      => ['nullable','string','max:255'],
            'domicilio'      => ['nullable','string','max:255'],
            'telefono'       => ['nullable','string','max:100'],
            'zona'           => ['nullable','string','max:120'],

            // Denunciado
            'denunciado_nombre'        => ['nullable','string','max:255'],
            'denunciado_sexo'          => ['nullable','string','max:30'],
            'denunciado_edad'          => ['nullable','integer','min:0','max:120'],
            'denunciado_relacion'      => ['nullable','string','max:255'],
            'denunciado_ci'            => ['nullable','string','max:100'],
            'denunciado_domicilio'     => ['nullable','string','max:255'],
            'denunciado_telefono'      => ['nullable','string','max:100'],
            'denunciado_lugar_trabajo' => ['nullable','string','max:255'],
            'denunciado_ocupacion'     => ['nullable','string','max:255'],

            // Denunciante
            'denunciante_nombre'        => ['nullable','string','max:255'],
            'denunciante_sexo'          => ['nullable','string','max:30'],
            'denunciante_edad'          => ['nullable','integer','min:0','max:120'],
            'denunciante_ci'            => ['nullable','string','max:100'],
            'denunciante_domicilio'     => ['nullable','string','max:255'],
            'denunciante_telefono'      => ['nullable','string','max:100'],
            'denunciante_lugar_trabajo' => ['nullable','string','max:255'],
            'denunciante_ocupacion'     => ['nullable','string','max:255'],

            'descripcion'     => ['nullable','string','max:5000'],
            'abogado_user_id' => ['nullable','exists:users,id'],

            // Arrays relacionados
            'menores'                     => ['nullable','array'],
            'menores.*.nombre'            => ['nullable','string','max:255'],
            'menores.*.gestante'          => ['nullable','boolean'],
            'menores.*.edad_anios'        => ['nullable','integer','min:0','max:20'],
            'menores.*.edad_meses'        => ['nullable','integer','min:0','max:11'],
            'menores.*.sexo'              => ['nullable','string','max:30'],
            'menores.*.cert_nac'          => ['nullable','boolean'],
            'menores.*.estudia'           => ['nullable','boolean'],
            'menores.*.ultimo_curso'      => ['nullable','string','max:120'],
            'menores.*.tipo_trabajo'      => ['nullable','string','max:120'],

            'grupo_familiar'              => ['nullable','array'],
            'grupo_familiar.*.nombre'     => ['nullable','string','max:255'],
            'grupo_familiar.*.parentesco' => ['nullable','string','max:120'],
            'grupo_familiar.*.edad'       => ['nullable','integer','min:0','max:120'],
            'grupo_familiar.*.sexo'       => ['nullable','string','max:30'],
            'grupo_familiar.*.instruccion'=> ['nullable','string','max:120'],
            'grupo_familiar.*.ocupacion'  => ['nullable','string','max:120'],
        ];

        $data = $request->validate($rules);
        $data['area']    = 'DNA';
        $data['user_id'] = $request->user()?->id;

        $menores = $data['menores'] ?? [];
        $familia = $data['grupo_familiar'] ?? [];
        unset($data['menores'], $data['grupo_familiar']);

        $dna = Dna::create($data);

        foreach ($menores as $m) {
            $dna->menores()->create([
                'nombre'       => $m['nombre']        ?? null,
                'gestante'     => (bool)($m['gestante'] ?? false),
                'edad_anios'   => $m['edad_anios']     ?? null,
                'edad_meses'   => $m['edad_meses']     ?? null,
                'sexo'         => $m['sexo']           ?? null,
                'cert_nac'     => (bool)($m['cert_nac'] ?? false),
                'estudia'      => (bool)($m['estudia']  ?? false),
                'ultimo_curso' => $m['ultimo_curso']    ?? null,
                'tipo_trabajo' => $m['tipo_trabajo']    ?? null,
            ]);
        }

        foreach ($familia as $g) {
            $dna->familiares()->create([
                'nombre'      => $g['nombre']      ?? null,
                'parentesco'  => $g['parentesco']  ?? null,
                'edad'        => $g['edad']        ?? null,
                'sexo'        => $g['sexo']        ?? null,
                'instruccion' => $g['instruccion'] ?? null,
                'ocupacion'   => $g['ocupacion']   ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Caso Dna creado con éxito',
            'dna'     => $dna->load(['user:id,name','abogado:id,name','menores','familiares']),
        ], 201);
    }

    public function show(Dna $dna)
    {
        return $dna->load(['user:id,name','abogado:id,name','menores','familiares']);
    }

    public function update(Request $request, Dna $dna)
    {
        $dna->update($request->except(['menores', 'grupo_familiar']));

        if ($request->has('menores')) {
            $dna->menores()->delete();
            foreach ((array)$request->input('menores', []) as $m) {
                $dna->menores()->create([
                    'nombre'       => $m['nombre']        ?? null,
                    'gestante'     => (bool)($m['gestante'] ?? false),
                    'edad_anios'   => $m['edad_anios']     ?? null,
                    'edad_meses'   => $m['edad_meses']     ?? null,
                    'sexo'         => $m['sexo']           ?? null,
                    'cert_nac'     => (bool)($m['cert_nac'] ?? false),
                    'estudia'      => (bool)($m['estudia']  ?? false),
                    'ultimo_curso' => $m['ultimo_curso']    ?? null,
                    'tipo_trabajo' => $m['tipo_trabajo']    ?? null,
                ]);
            }
        }

        if ($request->has('grupo_familiar')) {
            $dna->familiares()->delete();
            foreach ((array)$request->input('grupo_familiar', []) as $g) {
                $dna->familiares()->create([
                    'nombre'      => $g['nombre']      ?? null,
                    'parentesco'  => $g['parentesco']  ?? null,
                    'edad'        => $g['edad']        ?? null,
                    'sexo'        => $g['sexo']        ?? null,
                    'instruccion' => $g['instruccion'] ?? null,
                    'ocupacion'   => $g['ocupacion']   ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Caso Dna actualizado',
            'dna'     => $dna->load(['user:id,name','abogado:id,name','menores','familiares']),
        ]);
    }

    public function destroy(Dna $dna)
    {
        $dna->delete();
        return response()->json(['message' => 'Caso Dna eliminado']);
    }
    // ====== DNA: Informes Legales (polimórfico) ======
    public function legalIndex(Request $request, \App\Models\Dna $dna)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = \App\Models\InformeLegal::query()
            ->with(['user:id,name,username'])
            ->where('caseable_type', \App\Models\Dna::class)
            ->where('caseable_id', $dna->id);

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('numero', 'like', $like)
                    ->orWhere('contenido_html', 'like', $like)
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like)
                            ->orWhere('username', 'like', $like);
                    });
            });
        }

        $rows = $query->orderByDesc('fecha')->orderByDesc('id')
            ->paginate($perPage)->appends($request->query());

        return response()->json($rows);
    }

    public function legalStore(Request $request, \App\Models\Dna $dna)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'numero'         => ['nullable','string','max:120'],
            'contenido_html' => ['required','string'],
        ]);

        $data['caseable_id']   = $dna->id;
        $data['caseable_type'] = \App\Models\Dna::class;
        $data['user_id']       = $request->user()?->id;

        $inf = \App\Models\InformeLegal::create($data);
        return response()->json($inf->load('user:id,name,username'), 201);
    }

    public function legalShow(\App\Models\InformeLegal $informe)
    {
        abort_unless($informe->caseable_type === \App\Models\Dna::class, 404);
        return $informe->load(['user:id,name,username','caseable:id']);
    }

    public function legalUpdate(Request $request, \App\Models\InformeLegal $informe)
    {
        abort_unless($informe->caseable_type === \App\Models\Dna::class, 404);

        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['sometimes','required','string','max:255'],
            'numero'         => ['nullable','string','max:120'],
            'contenido_html' => ['sometimes','required','string'],
        ]);

        $informe->update($data);
        return response()->json([
            'message' => 'Informe actualizado',
            'data'    => $informe->fresh()->load('user:id,name,username')
        ]);
    }

    public function legalDestroy(\App\Models\InformeLegal $informe)
    {
        abort_unless($informe->caseable_type === \App\Models\Dna::class, 404);
        $informe->delete();
        return response()->json(['message' => 'Informe legal eliminado']);
    }

// ====== DNA: PDF Informe Legal ======
    public function legalPdf(\App\Models\InformeLegal $informe, Request $request)
    {
        abort_unless($informe->caseable_type === \App\Models\Dna::class, 404);

        $inf  = $informe->load(['user:id,name,username','caseable']);
        $pdf  = app('dompdf.wrapper');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadView('pdf.dna.informe_legal', ['informe' => $inf]);
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        $filename = 'DNA_InformeLegal_'.$informe->id.'.pdf';
        $download = (int) $request->query('download', 0) === 1;

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }



    /* ==========================
     * Seguimiento (para tu Vue)
     * ========================== */
    public function seguimiento(Dna $dna)
    {
        $dna->load(['user:id,name']);

        $header = [
            'slim_id'        => null,
            'caso_id'        => $dna->id,
            'caso_numero'    => $dna->codigo,
            'fecha_registro' => optional($dna->created_at)->format('Y-m-d'),
            'tipologia'      => $dna->tipo_proceso,
            'modalidad'      => $dna->principal,
            'zona'           => $dna->zona,
            'direccion'      => $dna->domicilio,
            'denunciante'    => $dna->denunciante_nombre,
            'denunciado'     => $dna->denunciado_nombre,
            'fecha_hecho'    => optional($dna->fecha_atencion)->format('Y-m-d'),
            'registrado_por' => $dna->user?->name,
        ];

        $items = [];

        foreach ($dna->psicologicas()->with('user:id,name')->get() as $p) {
            $items[] = [
                'uid'         => 'psico-'.$p->id,
                'tipo'        => 'Sesión',
                'titulo'      => $p->titulo,
                'descripcion' => strip_tags((string) $p->contenido_html),
                'usuario'     => $p->user?->name,
                'fecha'       => $p->fecha ? (string)$p->fecha : optional($p->created_at)->format('Y-m-d'),
                'modulo'      => 'Psicológico',
                'icon'        => 'psychology',
                'links'       => [],
            ];
        }

        foreach ($dna->informesLegales()->with('user:id,name')->get() as $i) {
            $items[] = [
                'uid'         => 'legal-'.$i->id,
                'tipo'        => 'Informe',
                'titulo'      => trim($i->titulo.' '.($i->numero ? "({$i->numero})" : '')),
                'descripcion' => strip_tags((string) $i->contenido_html),
                'usuario'     => $i->user?->name,
                'fecha'       => $i->fecha ? (string)$i->fecha : optional($i->created_at)->format('Y-m-d'),
                'modulo'      => 'Legal',
                'icon'        => 'gavel',
                'links'       => [],
            ];
        }

        foreach ($dna->documentos()->with('user:id,name')->get() as $d) {
            $items[] = [
                'uid'         => 'doc-'.$d->id,
                'tipo'        => 'Documento',
                'titulo'      => $d->titulo ?: $d->original_name,
                'descripcion' => (string) $d->descripcion,
                'usuario'     => $d->user?->name,
                'fecha'       => optional($d->created_at)->format('Y-m-d'),
                'modulo'      => 'Documentos',
                'icon'        => 'description',
                'links'       => ['view' => $d->url],
            ];
        }

        foreach ($dna->fotografias()->with('user:id,name')->get() as $f) {
            $items[] = [
                'uid'         => 'foto-'.$f->id,
                'tipo'        => 'Fotografía',
                'titulo'      => $f->titulo ?: $f->original_name,
                'descripcion' => (string) ($f->descripcion ?? ''),
                'usuario'     => $f->user?->name,
                'fecha'       => optional($f->created_at)->format('Y-m-d'),
                'modulo'      => 'Multimedia',
                'icon'        => 'image',
                'links'       => ['view' => $f->url],
            ];
        }

        usort($items, fn($a,$b)=>strcmp(($b['fecha'] ?? '0000-00-00'), ($a['fecha'] ?? '0000-00-00')));

        return response()->json([
            'header' => $header,
            'items'  => $items,
        ]);
    }


    /* ==========================
     * Psicológicas (polimórfico)
     * ========================== */
    public function psicoIndex(Request $request, Dna $dna)
    {
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));

        $query = $dna->psicologicas()
            ->with('user:id,name')
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        return $query->paginate($perPage)->appends($request->query());
    }

    public function psicoStore(Request $request, Dna $dna)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:120'],
            'contenido_html' => ['nullable','string'],
        ]);

        $data['caseable_id']   = $dna->id;
        $data['caseable_type'] = Dna::class;
        $data['user_id']       = $request->user()?->id;

        $ps = Psicologica::create($data);
        return response()->json($ps->load('user:id,name'), 201);
    }

    public function psicoShow(Psicologica $psicologica)
    {
        abort_unless($psicologica->caseable_type === Dna::class, 404);
        return $psicologica->load('user:id,name');
    }

    public function psicoUpdate(Request $request, Psicologica $psicologica)
    {
        abort_unless($psicologica->caseable_type === Dna::class, 404);

        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['sometimes','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:120'],
            'contenido_html' => ['nullable','string'],
        ]);

        $psicologica->update($data);
        return $psicologica->refresh()->load('user:id,name');
    }

    public function psicoDestroy(Psicologica $psicologica)
    {
        abort_unless($psicologica->caseable_type === Dna::class, 404);
        $psicologica->delete();
        return response()->json(['message' => 'Sesión psicológica eliminada']);
    }

    /* ==========================
     * Documentos (polimórfico)
     * ========================== */
    public function docView(Documento $documento)
    {
        abort_unless($documento->caseable_type === Dna::class, 404);

        // Si es URL externa, redirige
        if (!$documento->disk || !$documento->path) {
            if ($documento->url) return redirect()->away($documento->url);
            abort(404);
        }

        $stream = Storage::disk($documento->disk)->readStream($documento->path);
        if ($stream === false) abort(404);

        $filename = $documento->original_name ?: $documento->stored_name ?: 'documento';
        $mime     = $documento->mime ?: 'application/octet-stream';

        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control'       => 'private, max-age=10800, pre-check=10800',
            'Pragma'              => 'public',
        ]);
    }

    public function docDownload(Documento $documento)
    {
        abort_unless($documento->caseable_type === Dna::class, 404);

        // Si es URL externa, redirige
        if (!$documento->disk || !$documento->path) {
            if ($documento->url) return redirect()->away($documento->url);
            abort(404);
        }

        $downloadName = $documento->original_name ?: $documento->stored_name ?: 'documento';
        return Storage::disk($documento->disk)->download($documento->path, $downloadName);
    }

    public function docStore(Request $request, Dna $dna)
    {
        $data = $request->validate([
            'titulo'       => ['nullable','string','max:255'],
            'categoria'    => ['nullable','string','max:120'],
            'descripcion'  => ['nullable','string'],
            'url'          => ['nullable','url'],
            'file'         => ['nullable','file','max:51200'], // 50MB
        ]);

        $payload = [
            'caseable_id'   => $dna->id,
            'caseable_type' => Dna::class,
            'user_id'       => $request->user()?->id,
            'titulo'        => $data['titulo'] ?? null,
            'categoria'     => $data['categoria'] ?? null,
            'descripcion'   => $data['descripcion'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $file        = $request->file('file');
            $ext         = strtolower($file->getClientOriginalExtension());
            $storedName  = Str::uuid()->toString().'.'.$ext;
            $path        = $file->storeAs("dna/{$dna->id}/documentos", $storedName, 'public');
            $url         = Storage::disk('public')->url($path);

            $payload += [
                'original_name' => $file->getClientOriginalName(),
                'stored_name'   => $storedName,
                'extension'     => $ext,
                'mime'          => $file->getClientMimeType(),
                'size_bytes'    => $file->getSize(),
                'disk'          => 'public',
                'path'          => $path,
                'url'           => $url,
            ];
        } else {
            // Cuando sólo mandan URL externa
            if (!empty($data['url'])) {
                $payload += [
                    'disk' => null, 'path' => null, 'stored_name' => null,
                    'url'  => $data['url'],
                ];
            }
        }

        $doc = Documento::create($payload);
        return response()->json($doc->load('user:id,name'), 201);
    }

    public function docShow(Documento $documento)
    {
        abort_unless($documento->caseable_type === Dna::class, 404);
        return $documento->load('user:id,name');
    }

    public function docUpdate(Request $request, Documento $documento)
    {
        abort_unless($documento->caseable_type === Dna::class, 404);

        $data = $request->validate([
            'titulo'       => ['nullable','string','max:255'],
            'categoria'    => ['nullable','string','max:120'],
            'descripcion'  => ['nullable','string'],
        ]);

        $documento->update($data);
        return $documento->refresh()->load('user:id,name');
    }

    public function docDestroy(Documento $documento)
    {
        abort_unless($documento->caseable_type === Dna::class, 404);

        // Borra archivo físico si es de tu storage
        if ($documento->disk && $documento->path) {
            Storage::disk($documento->disk)->delete($documento->path);
        }

        $documento->delete();
        return response()->json(['message' => 'Documento eliminado']);
    }

    /** LISTAR (paginado) */
    public function fotoIndex(Request $request, Dna $dna)
    {
        $perPage = max(1, min((int)$request->query('per_page', 12), 100));
        $q       = trim((string)$request->query('q', ''));

        $query = $dna->fotografias()->with('user:id,name')
            ->orderByDesc('id');

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function($s) use($like){
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('descripcion', 'like', $like)
                    ->orWhere('original_name', 'like', $like);
            });
        }

        $rows = $query->paginate($perPage)->appends($request->query());

        // Añade tamaño humano y asegura thumb_url en la respuesta
        $rows->getCollection()->transform(function($f){
            $f->size_human = $this->humanFilesize((int)$f->size_bytes);
            // si tienes thumbs guardados como path, saca la URL
            if ($f->thumb_path && !$f->thumb_url) {
                $f->thumb_url = Storage::disk($f->disk ?? 'public')->url($f->thumb_path);
            }
            return $f;
        });

        return response()->json($rows);
    }

    /** SUBIR */
    public function fotoStore(Request $request, \App\Models\Dna $dna)
    {
        $request->validate([
            'file'        => ['required','image','mimes:jpg,jpeg,png,webp','max:20480'], // 20 MB
            'titulo'      => ['nullable','string','max:255'],
            'descripcion' => ['nullable','string','max:5000'],
        ]);

        $disk = 'public';
        $dir  = "dna/{$dna->id}/fotografias";

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $nameNoExt    = pathinfo($originalName, PATHINFO_FILENAME);
        $extLower     = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        // Nombre base único, legible
        $base = Str::slug($nameNoExt).'-'.Str::random(6);

        // === Procesado de imagen (como en SLIM) ===
        $manager = new ImageManager(new Driver());

        // Principal (máx 2000px)
        $img = $manager->read($file->getPathname());
        $img->resizeDown(width: 2000, height: 2000);

        // Normaliza extensión de salida
        $storedExt  = in_array($extLower, ['jpg','jpeg','png','webp']) ? ($extLower === 'jpeg' ? 'jpg' : $extLower) : 'jpg';
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

        // Miniatura (máx 480px)
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

        // URLs públicas
        $relUrl      = Storage::url($path);       // => "/storage/..."
        $relThumbUrl = Storage::url($thumbPath);  // => "/storage/..."

        // ABSOLUTAS (http://localhost:8000/...) para que siempre cargue bien
        $absUrl      = url($relUrl);
        $absThumbUrl = url($relThumbUrl);

        $bytes  = Storage::disk($disk)->size($path);
        $width  = $img->width();
        $height = $img->height();

        $foto = \App\Models\Fotografia::create([
            'caseable_id'   => $dna->id,
            'caseable_type' => \App\Models\Dna::class,
            'user_id'       => $request->user()?->id,

            'titulo'        => $request->string('titulo')->toString() ?: $nameNoExt,
            'descripcion'   => $request->get('descripcion'),

            'original_name' => $originalName,
            'stored_name'   => $storedName,
            'extension'     => $storedExt,
            'mime'          => $mime,
            'size_bytes'    => $bytes,

            'disk'          => $disk,
            'path'          => $path,
            'url'           => $relUrl,       // se guarda relativo en BD

            'thumb_path'    => $thumbPath,
            'thumb_url'     => $relThumbUrl,  // se guarda relativo en BD

            'width'         => $width,
            'height'        => $height,
        ])->load('user:id,name');

        // Para la respuesta, sobreescribimos con ABSOLUTAS (no cambia lo guardado)
        $foto->setAttribute('url', $absUrl);
        $foto->setAttribute('thumb_url', $absThumbUrl);
        $foto->size_human = $this->humanFilesize((int) $foto->size_bytes);

        return response()->json($foto, 201);
    }

    /** ELIMINAR */
    public function fotoDestroy(Fotografia $fotografia)
    {
        abort_unless($fotografia->caseable_type === Dna::class, 404);

        if ($fotografia->disk && $fotografia->path) {
            Storage::disk($fotografia->disk)->delete($fotografia->path);
        }
        if ($fotografia->disk && $fotografia->thumb_path) {
            Storage::disk($fotografia->disk)->delete($fotografia->thumb_path);
        }

        $fotografia->delete();
        return response()->json(['message' => 'Fotografía eliminada']);
    }

    /** Utilidad tamaño humano */
    private function humanFilesize(int $bytes, int $decimals = 1): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B','KB','MB','GB','TB'];
        $pow   = (int)floor(log($bytes, 1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1024 ** $pow);
        return round($bytes, $decimals).' '.$units[$pow];
    }
}
