<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditController extends Controller
{
    /**
     * GET /api/audits
     * Params:
     *  - q: texto libre
     *  - event: created|updated|deleted
     *  - type:  short model name (Caso, Problematica, SesionPsicologica, Informe, Documento, Fotografia, User, etc.)
     *  - start, end: rango por created_at (YYYY-MM-DD)
     *  - page, per_page
     */
    public function index(Request $request)
    {
        $q       = trim($request->get('q', ''));
        $event   = trim($request->get('event', ''));
        $type    = trim($request->get('type', ''));    // p.ej. "Caso"
        $start   = $request->get('start');
        $end     = $request->get('end');
        $page    = (int) $request->get('page', 1);
        $per     = (int) $request->get('per_page', 10);

        $builder = DB::table('audits as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.*',
                'u.name as user_name',
                'u.username as user_username'
            );

        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $w->where('a.event', 'like', "%{$q}%")
                    ->orWhere('a.auditable_type', 'like', "%{$q}%")
                    ->orWhere('a.auditable_id', 'like', "%{$q}%")
                    ->orWhere('u.name', 'like', "%{$q}%")
                    ->orWhere('u.username', 'like', "%{$q}%")
                    ->orWhere('a.tags', 'like', "%{$q}%")
                    ->orWhere('a.url', 'like', "%{$q}%")
                    ->orWhere('a.ip_address', 'like', "%{$q}%")
                    ->orWhere('a.user_agent', 'like', "%{$q}%")
                    ->orWhere('a.old_values', 'like', "%{$q}%")
                    ->orWhere('a.new_values', 'like', "%{$q}%");
            });
        }

        if ($event !== '') {
            $builder->where('a.event', $event);
        }

        if ($type !== '') {
            // Normalizamos comparando por el "short name" del FQCN
            $builder->whereRaw("SUBSTRING_INDEX(a.auditable_type, '\\\\', -1) = ?", [$type]);
        }

        if ($start) {
            $builder->where('a.created_at', '>=', $start . ' 00:00:00');
        }
        if ($end) {
            $builder->where('a.created_at', '<=', $end . ' 23:59:59');
        }

        $rows = $builder->orderByDesc('a.created_at')
            ->orderByDesc('a.id')
            ->paginate($per, ['*'], 'page', $page);

        // Post-procesamos para exponer campos útiles
        $rows->getCollection()->transform(function ($r) {
            $r->model_short = class_basename($r->auditable_type); // p.ej. App\Models\Caso => Caso
            $r->user_label  = $r->user_name ?? $r->user_username ?? null;
            $r->changed_count = $this->countChanges($r->old_values, $r->new_values);
            $r->summary_fields = $this->summaryFields($r->old_values, $r->new_values, 5);
            $r->entity_route = $this->guessEntityRoute($r->auditable_type, $r->auditable_id);
            return $r;
        });

        return response()->json($rows);
    }

    /**
     * GET /api/audits/{id}
     * Retorna el registro + diff de campos {field, old, new}.
     */
    public function show($id)
    {
        $row = DB::table('audits as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select('a.*', 'u.name as user_name', 'u.username as user_username', 'u.email as user_email')
            ->where('a.id', $id)
            ->first();

        if (!$row) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $old = $this->jsonToArray($row->old_values);
        $new = $this->jsonToArray($row->new_values);

        $diff = $this->buildDiff($old, $new);

        $row->model_short = class_basename($row->auditable_type);
        $row->user_label  = $row->user_name ?? $row->user_username ?? null;
        $row->entity_route = $this->guessEntityRoute($row->auditable_type, $row->auditable_id);

        return response()->json([
            'audit' => $row,
            'old'   => $old,
            'new'   => $new,
            'diff'  => $diff,
        ]);
    }

    // ===== helpers =====

    private function jsonToArray(?string $json): array
    {
        if (!$json) return [];
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }

    private function buildDiff(array $old, array $new): array
    {
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $out  = [];
        foreach ($keys as $k) {
            $ov = $old[$k] ?? null;
            $nv = $new[$k] ?? null;
            if ($ov !== $nv) {
                $out[] = [
                    'field' => $k,
                    'old'   => $ov,
                    'new'   => $nv,
                ];
            }
        }
        return $out;
    }

    private function countChanges(?string $oldJson, ?string $newJson): int
    {
        return count($this->buildDiff($this->jsonToArray($oldJson), $this->jsonToArray($newJson)));
    }

    private function summaryFields(?string $oldJson, ?string $newJson, int $limit = 5): array
    {
        $diff = $this->buildDiff($this->jsonToArray($oldJson), $this->jsonToArray($newJson));
        return array_slice(array_map(fn ($d) => $d['field'], $diff), 0, $limit);
    }

    private function guessEntityRoute(string $fqcn, $id): ?string
    {
        $short = class_basename($fqcn);
        switch ($short) {
            case 'Caso':               return "/casos/{$id}";
            case 'Problematica':       return "/problematicas/{$id}";
            case 'SesionPsicologica':  return "/sesiones-psicologicas/{$id}";
            case 'Informe':            return "/informes/{$id}";
            case 'Documento':          return "/documentos/{$id}";
            case 'Fotografia':         return "/fotografias/{$id}";
            case 'User':               return "/usuarios/{$id}";
            default:                   return null;
        }
    }
}
