<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Acceso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007BFF;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            line-height: 1.6;
            margin: 0 0 15px;
        }
        .img-container {
            text-align: center;
            margin-top: 20px;
        }
        .img-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .footer p {
            margin: 0;
        }
        .button {
            display: inline-block;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Notificación de Acceso - RoboLock</h1>
        <p>Estimado usuario,</p>
        <p>El sistema de seguridad de acceso RoboLock ha detectado una solicitud de entrada en la puerta.</p>
        <p>Un visitante ha solicitado acceso y se ha registrado su imagen. A continuación, se presenta la foto del solicitante para su revisión.</p>
        <p><strong>Fecha y hora de la solicitud:</strong> {{ $fechaNotificacion }}</p>
        

        <p>Por favor, acceda al sistema de seguridad para permitir o denegar esta solicitud.</p>

        <a href="http://192.168.235.126/robolock/public/login" class="button">Acceder al Sistema</a>

        <div class="footer">
            <p>Saludos cordiales,</p>
            <p><strong>RoboLock</strong></p>
            <p>Para cualquier consulta, no dude en contactarnos.</p>
        </div>
    </div>
</body>
</html>
