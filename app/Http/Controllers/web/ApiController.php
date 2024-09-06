<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Mail\ExampleMail;
use App\Models\AccessLog;
use App\Models\Notification;
use App\Models\Person;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'status' => 'required', // Asegúrate de que los valores coincidan con la lógica deseada
            'breakPoint' => 'required', // Define los valores permitidos

            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Crear un nuevo AccessLog
            $accessLog = AccessLog::create([
                'status' => $request->input('status'),
                'breakPoint' => $request->input('breakPoint'),
                'person_id' => $request->input('person_id') ?? 1,
                'photo' => $request->input('photo') ?? null,
            ]);

            // Recuperar el AccessLog con las relaciones
            $accessLog = AccessLog::with(['authorizedPerson'])->find($accessLog->id);

            return response()->json([
                'message' => 'AccessLog created successfully',
                'data' => $accessLog,
            ], 201);
        } catch (\Exception $e) {
            // Capturar cualquier error y devolverlo en la respuesta
            return response()->json([
                'message' => 'Error creating AccessLog',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photoPath' => 'required', // Asegura que se proporcione una ruta de imagen
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Obtener y ajustar la ruta de la imagen
        $fullPath = $request->input('photoPath');
        $relativePath = str_replace('/var/www/html/robolock/public/', '', $fullPath);
        dd($fullPath);

        // Crear un nuevo registro en Notification
        $accessLog = Notification::create([
            'photoPath' => $relativePath,
            'state' => 1,
        ]);

        $accessLog = Notification::find($accessLog->id);

        // Define las variables para el correo
        $receptor = 'mguevaracaj@unprg.edu.pe';
        $asunto = 'Notificación de Acceso';
        $fechaNotificacion = Carbon::now()->format('l, d F Y \a \l\a\s H:i'); // Ejemplo: 'Lunes, 01 Enero 2024 a las 14:35'
        $photoUrl = url($relativePath); // Obtener la URL completa de la imagen

        // Cuerpo del correo con la imagen
        // Cuerpo del correo con la imagen
        $cuerpo = [
            'photoPath' => $relativePath,
            'fechaNotificacion' => $fechaNotificacion,
            'photoUrl' => $photoUrl,
        ];

        // Enviar el correo electrónico
        $this->send($receptor, $asunto, $cuerpo);

        return response()->json([
            $accessLog,
        ], 201);
    }

public function send(string $to = '', string $subject = '', array $body = [])
{
    $receptor = $to ?? 'mguevaracaj@unprg.edu.pe';
    $asunto = $subject ?? 'Asunto del correo';

    // Crear una instancia de la clase Mailable y establecer la vista HTML
    $correo = new ExampleMail($asunto, $body);

    // Registrar log antes de enviar el correo
    Log::info("Intentando enviar un correo a: {$receptor} con el asunto: {$asunto}");

    // Enviar el correo electrónico
    try {
        Mail::to($receptor)->send($correo);
        // Registrar log después de enviar el correo exitosamente
        Log::info("Correo enviado exitosamente a: {$receptor}");
    } catch (Exception $e) {
        // Registrar el error en los logs
        Log::error("Error al enviar el correo a: {$receptor}. Detalles: " . $e->getMessage());
        echo ('Error al enviar el correo: ' . $e->getMessage());
    }
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
