<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
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

        return response()->json(['message' => 'Person not found'], 404);
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
        $accessLog=AccessLog::with(['authorizedPerson'])->find($accessLog->id);

        return response()->json([
            'message' => 'AccessLog created successfully',
            'data' => $accessLog,
        ], 201);
    }
}
