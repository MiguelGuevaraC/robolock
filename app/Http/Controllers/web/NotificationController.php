<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // NotificationController.php

    public function index(Request $request)
    {
        $perPage = 30; // Número de notificaciones por página

        // Obtén las notificaciones paginadas
        $notifications = Notification::where('state', 1)
            ->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($notifications, 200);
    }

    public function show($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            return response()->json($notification, 200);
        }

        return response()->json(['message' => 'Notificación no encontrada'], 404);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::find($id);
    
        // Verificar si la notificación existe
        if (!$notification) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }
    
        // Comprobar el estado de la notificación
        if ($request->status === 'Permitido') {
            // Comparar la contraseña solo si el estado es "Permitido"
            if ($request->password === 'OpenDoor') {
                $notification->status = $request->status; // Asigna el estado recibido
                $notification->state = 2; // Marca como procesado
                $notification->save();
    
                return response()->json(['message' => 'Estado actualizado correctamente'], 200);
            } else {
                return response()->json(['message' => 'Contraseña Incorrecta'], 422);
            }
        } else {
            // Actualizar solo el estado si es "No Permitido" u otro estado
            $notification->status = $request->status; // Asigna el estado recibido
            $notification->state = 2; // Marca como procesado
            $notification->save();

            $accessLog = AccessLog::create([
                'status' => 'No Permitido',
                'breakPoint' => 'Solicitud Acceso Directo',
               'person_id' => auth()->user()->person ? auth()->user()->person->id : null,

            ]);
    
            return response()->json(['message' => 'Estado actualizado correctamente'], 200);
        }
    }


    
    // Obtener notificaciones nuevas
    public function getNewNotifications()
    {
        $notifications = Notification::where('state', '1')->get();

        // Recorrer las notificaciones para agregar la columna 'fecha'
        $notifications->each(function ($notification) {
            $notification->fecha = date('Y-m-d,H:i:s', strtotime($notification->created_at));
        });

        return response()->json($notifications);
    }

    // Marcar notificación como vista
    public function markAsSeen($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->state = '0'; // Cambia el estado a 0
            $notification->save();
            return response()->json(['message' => 'Notificación marcada como vista.']);
        }
        return response()->json(['message' => 'Notificación no encontrada.'], 404);
    }

}
