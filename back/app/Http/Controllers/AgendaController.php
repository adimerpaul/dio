<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgendaController extends Controller
{
    // GET /agendas?start=YYYY-MM-DD&end=YYYY-MM-DD&user_id=...&status=...&q=...
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Agenda::with(['user:id,name', 'caso:id,caso_numero,denunciante_nombre_completo'])
            ->orderBy('start', 'asc');

        // Filtros por rango (recomendado por FullCalendar feed)
        if ($request->filled('start')) $query->where('start', '>=', $request->get('start'));
        if ($request->filled('end'))   $query->where('start', '<=', $request->get('end'));

        // Filtro por profesional
//        if ($request->filled('user_id')) {
//            $query->where('user_id', $request->get('user_id'));
//        } else {
//            // Si el rol es Psicologo, limitar a sus propios eventos por defecto
//            if ($user->role === 'Psicologo') {
//                $query->where('user_id', $user->id);
//            }
//        }
        $user = $request->user();
        $query->where('user_id', $user->id);

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Búsqueda libre
        if ($q = trim((string) $request->get('q', ''))) {
            $query->where(function ($s) use ($q) {
                $like = "%{$q}%";
                $s->where('title', 'like', $like)
                    ->orWhere('notes', 'like', $like)
                    ->orWhere('location', 'like', $like);
            });
        }

        return response()->json($query->get());
    }

    // POST /agendas
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'title'     => ['required','string','max:255'],
            'notes'     => ['nullable','string'],
            'location'  => ['nullable','string','max:255'],
            'start'     => ['required','date'],
            'end'       => ['nullable','date','after_or_equal:start'],
            'all_day'   => ['boolean'],
            'status'    => ['nullable', Rule::in(['Programado','Reprogramado','Atendido','Cancelado'])],
            'color'     => ['nullable','string','max:30'],
            'caso_id'   => ['nullable','exists:casos,id'],
            'user_id'   => ['nullable','exists:users,id'], // si no mandas, por defecto el actual
        ]);

        $data['user_id']   = $data['user_id']   ?? $user->id;
        $data['status']    = $data['status']    ?? 'Programado';
        $data['created_by']= $user->id;

        $agenda = Agenda::create($data);

        return response()->json(['message' => 'Evento creado', 'data' => $agenda->load('user','caso')], 201);
    }

    // GET /agendas/{agenda}
    public function show(Agenda $agenda)
    {
        return response()->json($agenda->load('user','caso','creator'));
    }

    // PUT /agendas/{agenda}
    public function update(Request $request, Agenda $agenda)
    {
        $data = $request->validate([
            'title'     => ['sometimes','required','string','max:255'],
            'notes'     => ['nullable','string'],
            'location'  => ['nullable','string','max:255'],
            'start'     => ['sometimes','required','date'],
            'end'       => ['nullable','date','after_or_equal:start'],
            'all_day'   => ['boolean'],
            'status'    => ['nullable', Rule::in(['Programado','Reprogramado','Atendido','Cancelado'])],
            'color'     => ['nullable','string','max:30'],
            'caso_id'   => ['nullable','exists:casos,id'],
            'user_id'   => ['nullable','exists:users,id'],
        ]);

        $agenda->update($data);

        return response()->json(['message' => 'Evento actualizado', 'data' => $agenda->fresh()->load('user','caso')]);
    }

    // DELETE /agendas/{agenda}
    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return response()->json(['message' => 'Evento eliminado']);
    }
}
