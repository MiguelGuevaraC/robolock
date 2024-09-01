<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Notification;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function searchByUid($uid)
    {

        $person = Person::where('uid', $uid)->first();

        if ($person) {
            return response()->json($person, 200);
        }

        return response()->json(['message' => 'Persona no encontrada'], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'status' => 'required|in:Permitido,Denegado', // Cambia los valores según tu lógica
            'breakPoint' => 'required|string|in:RFID,CAMARA,SISTEMA',
            'person_id' => 'nullable|exists:people,id', // Asegúrate de que el ID existe en la tabla `people`
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Crear un nuevo AccessLog
        $accessLog = AccessLog::create([
            'status' => $request->input('status'),
            'breakPoint' => $request->input('breakPoint'),
            'person_id' => $request->input('person_id'),
        ]);
        $accessLog = AccessLog::with(['authorizedPerson'])->find($accessLog->id);

        return response()->json([
            'message' => 'AccessLog created successfully',
            'data' => $accessLog,
        ], 201);
    }

    public function storeNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photoPath' => 'required', // Cambia los valores según tu lógica
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Crear un nuevo AccessLog
        $fullPath = $request->input('photoPath');
        $relativePath = str_replace('/var/www/html/robolock/public/', '', $fullPath);

        $relativePath = "robolock/public/"+$relativePath;
        $accessLog = Notification::create([
            'photoPath' => $relativePath,
            'state' => 1,
        ]);
        

        $accessLog = Notification::find($accessLog->id);

        return response()->json([
            $accessLog
        ], 201);
    }

    public function accessPermitidosByAdmin()
    {

        $access = Notification::where('status', 'Permitido')->first();

        return response()->json($access);
    }
    public function update($id)
    {

        $access = Notification::find($id);
        if (!$access) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }
        $access->status = 'Permitida Abierta';
        $access->save();
        return response()->json($access);
    }
}
