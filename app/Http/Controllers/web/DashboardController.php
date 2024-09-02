<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\GroupMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    public function index()
    {
        $user = Auth::user();

        $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
        $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

        return view('Modulos.Dashboard.index', compact('user', 'groupMenu', 'groupMenuLeft'));
    }
    public function dataDashboard(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio', now()->startOfYear()->format('Y-m-d'));
        $fechaFin = $request->input('fechaFin', now()->addDay()->format('Y-m-d'));
    
    
        // Verificar que la fecha de fin no sea anterior a la fecha de inicio
        if ($fechaFin < $fechaInicio) {
            return response()->json(['error' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'], 400);
        }
    
        // Filtrar datos por el rango de fechas
        $mensajes = AccessLog::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('state', 1)
            ->orderBy('created_at', 'asc')
            ->get();
    
        // Agrupar los mensajes por estado usando categorías descriptivas
        $estadoGroups = $mensajes->groupBy('status')->map(function ($items, $estado) {
            return [
                'label' => $this->mapEstado($estado),
                'count' => $items->count(),
            ];
        });
    
        // Agrupar mensajes por punto de acceso
        $breakPointGroups = $mensajes->groupBy('breakPoint')->map(function ($items, $breakPoin) {
            return [
                'label' => $this->mapBreakPoint($breakPoin),
                'count' => $items->count(),
            ];
        });
    
        // Obtener el total de accesos permitidos y denegados
        $totalAccesosPermitidos = $mensajes->where('status', 'Permitido')->count();;  // Total de mensajes
        $totalAccesosDenegados = $mensajes->where('status', 'Denegado')->count();  // Contar los denegados
    
        // Obtener accesos del día actual
        $accesosDelDiaActual = $mensajes->filter(function ($item) {
            return $item->created_at->isToday(); // Filtrar mensajes del día actual
        })->count();
    
        // Devolver los datos para los gráficos
        return response()->json([
            'estadoData' => [
                'labels' => $estadoGroups->pluck('label'),
                'data' => $estadoGroups->pluck('count'),
            ],
            'alcanceData' => [
                'labels' => $breakPointGroups->pluck('label'),
                'data' => $breakPointGroups->pluck('count'),
            ],
            "accessPermitidos" => $totalAccesosPermitidos,
            "accessDenegados" => $totalAccesosDenegados,
            "accessByDay" => $accesosDelDiaActual,
        ]);
    }
    
    private function mapEstado($estado)
    {
        // Mapea los estados a categorías más generales
        $map = [
            'Permitido' => 'Exitoso',
            'Denegado' => 'Fallido',
            'pending' => 'En Proceso',
            'error' => 'Error',
        ];
    
        return $map[$estado] ?? 'Otro'; // Devuelve 'Otro' si el estado no está en el mapa
    }
    
    private function mapBreakPoint($breakPoin)
    {
        $map = [
            'RFID' => 'Tarjeta',
            'CAMARA' => 'Reconocimiento Facial',
            'ACCESO DIRECTO' => 'Solicitud',
            'error' => 'Error',
        ];
    
        return $map[$breakPoin] ?? 'Otro'; // Devuelve 'Otro' si el punto de acceso no está en el mapa
    }
    
    
}
