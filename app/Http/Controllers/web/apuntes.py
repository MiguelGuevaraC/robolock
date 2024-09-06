import numpy as np
from mfrc522 import SimpleMFRC522
import RPi.GPIO as GPIO
import smbus2 as smbus
import time
import requests
import os
import cv2
import face_recognition
import speech_recognition as sr
from gtts import gTTS
from pydub import AudioSegment
from pydub.playback import play
from datetime import datetime
from threading import Thread, Lock

# Configuracion de pines
SERVO_PIN = 12  # Pin fisico 12 para la senal del servomotor (GPIO 18 en BCM)

# Direccion I2C de tu LCD
I2C_ADDR = 0x27  # Direccion del display LCD (puede variar)

# Definicion de los comandos de LCD
LCD_CHR = 1  # Modo de datos
LCD_CMD = 0  # Modo de comandos

LCD_LINE_1 = 0x80  # Direccion RAM de la primera linea
LCD_LINE_2 = 0xC0  # Direccion RAM de la segunda linea

LCD_BACKLIGHT = 0x08  # Encender retroiluminacion
ENABLE = 0b00000100  # Habilitar bit

# Temporizacion
E_PULSE = 0.0005
E_DELAY = 0.0005

# Inicializacion de bus
bus = smbus.SMBus(1)  # 1 indica bus I2C-1
lcd_lock = Lock()  # Creacion del Lock para el acceso sincronizado al LCD

# Inicializa el lector RFID
reader = SimpleMFRC522()

# Inicializa el LCD
def lcd_init():
    lcd_byte(0x33, LCD_CMD)  # Modo de 4 bits
    lcd_byte(0x32, LCD_CMD)  # Modo de 4 bits
    lcd_byte(0x06, LCD_CMD)  # Desplazamiento automatico
    lcd_byte(0x0C, LCD_CMD)  # Encender display, sin cursor
    lcd_byte(0x28, LCD_CMD)  # Modo de 2 lineas, 5x7 matriz de puntos
    lcd_byte(0x01, LCD_CMD)  # Limpiar display
    time.sleep(E_DELAY)

def lcd_byte(bits, mode):
    """Envia byte al display"""
    with lcd_lock:  # Usar el Lock al escribir en el LCD
        bits_high = mode | (bits & 0xF0) | LCD_BACKLIGHT
        bits_low = mode | ((bits << 4) & 0xF0) | LCD_BACKLIGHT

        bus.write_byte(I2C_ADDR, bits_high)
        lcd_toggle_enable(bits_high)

        bus.write_byte(I2C_ADDR, bits_low)
        lcd_toggle_enable(bits_low)

def lcd_toggle_enable(bits):
    """Toggle enable"""
    time.sleep(E_DELAY)
    bus.write_byte(I2C_ADDR, (bits | ENABLE))
    time.sleep(E_PULSE)
    bus.write_byte(I2C_ADDR, (bits & ~ENABLE))
    time.sleep(E_DELAY)

def lcd_string(message, line):
    """Enviar string a LCD con desplazamiento si es necesario"""
    if len(message) > 16:
        message = message.ljust(len(message) + 16)
        for i in range(len(message) - 15):
            lcd_byte(line, LCD_CMD)
            for j in range(16):
                lcd_byte(ord(message[i + j]), LCD_CHR)
            time.sleep(0.4)
    else:
        lcd_byte(line, LCD_CMD)
        for i in range(16):
            lcd_byte(ord(message[i]) if i < len(message) else ord(" "), LCD_CHR)

def setup_servo():
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BOARD)
    GPIO.setup(SERVO_PIN, GPIO.OUT)
    pwm = GPIO.PWM(SERVO_PIN, 50)  # Configura PWM a 50 Hz
    pwm.start(0)
    return pwm

def set_servo_angle(pwm, angle):
    """Configura el angulo del servomotor"""
    try:
        if 0 <= angle <= 180:
            duty_cycle = 2 + (angle / 18)
            pwm.ChangeDutyCycle(duty_cycle)
            print(f"Servomotor movido a {angle} grados.")
            time.sleep(1)
            pwm.ChangeDutyCycle(0)  # Detener el pulso para evitar temblores
        else:
            print(f"Angulo {angle} fuera del rango permitido (0-180 grados).")
    except Exception as e:
        print(f"Error al configurar el angulo del servomotor: {e}")

def reproducir_audio(mensaje):
    """Genera y reproduce un mensaje de audio."""
    try:
        tts = gTTS(mensaje, lang='es')
        tts.save('mensaje.mp3')

        audio = AudioSegment.from_mp3('mensaje.mp3')
        play(audio)

        # Eliminar el archivo de audio después de la reproducción para no acumular archivos
        os.remove('mensaje.mp3')
    except Exception as e:
        print(f"Error al reproducir el audio: {e}")

def report_access(status, breakPoint, person_id=None, photo=None):
    """Registra el acceso en la API"""
    url = "http://192.168.235.126/robolock/public/api/reporteaccesos"
    data = {
        "status": status,
        "breakPoint": breakPoint,
        "photo": photo
    }
    if person_id:
        data["person_id"] = person_id
    if photo:
        data["photo"] = photo

    try:
        response = requests.post(url, json=data)
        response.raise_for_status()
        print("Registro de acceso exitoso.")
    except requests.exceptions.RequestException as e:
        print(f"Error al registrar el acceso: {e}")

def revisar_api_accesos(pwm):
    """Revisa la API cada 5 segundos para abrir la puerta si encuentra un acceso permitido"""
    while True:
        try:
            url = "http://192.168.235.126/robolock/public/api/accessPermitidosByAdmin"
            response = requests.get(url)
            response.raise_for_status()
            data = response.json()

            # Imprimir la respuesta en consola
            print(f"Respuesta de la API: {data}")

            # Verificar si la respuesta es una lista o un solo diccionario
            if isinstance(data, list):
                if data:
                    for acceso in data:
                        if acceso['status'] == "Permitido":
                            id_acceso = acceso['id']
                            print(f"Abriendo puerta para acceso ID: {id_acceso}")
                            reproducir_audio("Acceso Directo, Bienvenido a casa")
                            set_servo_angle(pwm, 180)  # Abrir la puerta a 180 grados

                            # Registrar acceso permitido inmediatamente
                            report_access(status="Permitido", breakPoint="API", person_id=id_acceso)
                            
                            time.sleep(5)  # Mantener la puerta abierta por 5 segundos
                            set_servo_angle(pwm, 0)   # Cerrar la puerta

                            update_url = f"http://192.168.235.126/robolock/public/api/notificationLeidaAbierta/{id_acceso}"
                            update_response = requests.put(update_url)
                            update_response.raise_for_status()
                            print(f"Acceso ID {id_acceso} actualizado como leído y abierto.")
                    
                    
                    # La línea de código adicional
                    report_access(person_id="1",status="Solicitado y Permitido", breakPoint="Acceso Directo")
                else:
                    print("No hay accesos permitidos pendientes.")
            elif isinstance(data, dict):
                # Si la respuesta es un solo diccionario
                if data.get('status') == "Permitido":
                    id_acceso = data.get('id')
                    print(f"Abriendo puerta para acceso ID: {id_acceso}")
                    
                    set_servo_angle(pwm, 180)  # Abrir la puerta a 180 grados

                    # Registrar acceso permitido inmediatamente
                    report_access(status="Permitido", breakPoint="API", person_id=id_acceso)
                    
                    time.sleep(5)  # Mantener la puerta abierta por 5 segundos
                    set_servo_angle(pwm, 0)   # Cerrar la puerta

                    update_url = f"http://192.168.235.126/robolock/public/api/notificationLeidaAbierta/{id_acceso}"
                    update_response = requests.put(update_url)
                    update_response.raise_for_status()
                    print(f"Acceso ID {id_acceso} actualizado como leído y abierto.")
                else:
                    print("No hay accesos permitidos pendientes.")

        except requests.RequestException as e:
            print(f"Error al revisar la API de accesos: {e}")

        time.sleep(5)  # Esperar 5 segundos antes de volver a revisar

def capturar_foto():
    """Captura una foto clara del rostro humano y espera obtener una buena toma"""
    cap = None
    try:
        cap = cv2.VideoCapture(0)
        if not cap.isOpened():
            print("Error: no se puede acceder a la camara.")
            return None

        # Configurar cámara para que sea más eficiente
        cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
        cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)
        cap.set(cv2.CAP_PROP_FPS, 30)  # Establecer FPS para una captura más rápida
        cap.set(cv2.CAP_PROP_BUFFERSIZE, 1)  # Minimizar el buffer para respuesta más rápida

        foto_capturada = False
        while not foto_capturada:
            ret, frame = cap.read()
            if not ret:
                print("Error: no se puede acceder a la camara.")
                break

            face_locations = face_recognition.face_locations(frame)
            if face_locations:
                now = datetime.now()
                timestamp = now.strftime("%Y%m%d_%H%M%S")
                
                # Guardar la foto
                photo_directory = '/var/www/html/robolock/public/storage/photosDirect/'
                if not os.path.exists(photo_directory):
                    os.makedirs(photo_directory)

                filename = f'photo_{timestamp}.jpg'
                photo_path = os.path.join(photo_directory, filename)
                cv2.imwrite(photo_path, frame)
                print(f"Foto tomada y guardada en {photo_path}")

                foto_capturada = True
                return photo_path
            else:
                print("No se detecto ningun rostro, esperando...")
            time.sleep(0.5)  # Espera entre intentos para obtener una mejor imagen

    except Exception as e:
        print(f"Error al capturar la foto: {e}")
        return None

    finally:
        if cap is not None:
            cap.release()

def enviar_foto_a_api(photo_path):
    """Envía la foto a la API"""
    try:
        url = "http://192.168.235.126/robolock/public/api/storeNotification"
        data = {"photoPath": photo_path}
        response = requests.post(url, json=data)
        response.raise_for_status()
        print("Foto enviada a la API correctamente.")
    except Exception as e:
        print(f"Error al enviar la foto a la API: {e}")

def search_by_uid(uid):
    """Consulta la API con el UID"""
    url = f"http://192.168.235.126/robolock/public/api/searchByUid/{uid}"
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error al conectar con la API: {e}")
        return None

def main(user_id):
    """Realiza el reconocimiento facial basado en las fotos almacenadas para el usuario"""
    cap = None
    try:
        base_path = f'/var/www/html/robolock/storage/app/public/photos/{user_id}'

        reference_encodings = []
        for filename in os.listdir(base_path):
            image_path = os.path.join(base_path, filename)
            if os.path.isfile(image_path) and image_path.endswith(('.png', '.jpg', '.jpeg')):
                image = face_recognition.load_image_file(image_path)
                encodings = face_recognition.face_encodings(image)
                if encodings:
                    reference_encodings.append(encodings[0])
                else:
                    print(f"No se encontró rostro en la imagen de referencia: {image_path}")

        if not reference_encodings:
            print("No se pudieron cargar rostros de referencia.")
            reproducir_audio("Lo sentimos, no se pudo verificar su identidad.")
            return False

        cap = cv2.VideoCapture(0)
        if not cap.isOpened():
            print("Error: no se puede acceder a la cámara.")
            reproducir_audio("Error, no se puede acceder a la cámara.")
            return False

        cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
        cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)
        cap.set(cv2.CAP_PROP_FPS, 30)  # Establecer FPS para una captura más rápida
        cap.set(cv2.CAP_PROP_BUFFERSIZE, 1)  # Minimizar el buffer para respuesta más rápida

        while True:
            ret, frame = cap.read()
            if not ret:
                print("Error: no se puede acceder a la cámara.")
                reproducir_audio("Error, no se puede acceder a la cámara.")
                break

            rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            face_locations = face_recognition.face_locations(rgb_frame)
            face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

            if not face_locations:
                print("No se detectó ningún rostro.")
            else:
                for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
                    matches = face_recognition.compare_faces(reference_encodings, face_encoding, tolerance=0.5)
                    face_distances = face_recognition.face_distance(reference_encodings, face_encoding)
                    best_match_index = np.argmin(face_distances)
                    if matches[best_match_index] and face_distances[best_match_index] < 0.4:
                        print(f"Persona autorizada. Distancia: {face_distances[best_match_index]}")
                        reproducir_audio("Bienvenido a Casa")

                        # Captura de foto y envío a la API
                        photo_path = capturar_foto()
                        if photo_path:
                            report_access(status="Permitido", breakPoint="CAMARA", person_id=user_id, photo=photo_path)

                        return True
                    else:
                        print(f"Persona NO autorizada. Distancia: {face_distances[best_match_index]}")
                        reproducir_audio("Acceso Denegado")

            cv2.imshow('Face Detection', frame)

            key = cv2.waitKey(1) & 0xFF
            if key == ord('s'):
                cv2.imwrite('face_detection_photo.png', frame)
                print("Foto tomada y guardada como 'face_detection_photo.png'.")
            elif key == ord('q'):
                break

    except Exception as e:
        print(f"Se produjo un error: {e}")
        reproducir_audio("Se produjo un error inesperado.")

    finally:
        if cap is not None:
            cap.release()
        cv2.destroyAllWindows()

    return False

def escuchar_palabra_clave():
    recognizer = sr.Recognizer()
    mic = sr.Microphone()

    print("Esperando para escuchar la palabra 'solicitud'...")

    with mic as source:
        recognizer.adjust_for_ambient_noise(source)  # Ajusta al ruido ambiente
        while True:
            print("Escuchando...")
            audio = recognizer.listen(source)  # Captura el audio

            try:
                # Reconocer el texto en el audio
                texto = recognizer.recognize_google(audio, language="es-ES")
                print(f"Se dijo: {texto}")

                # Verificar si se dijo la palabra clave
                if "solicitud" in texto.lower():
                    print("Acerquese a la Camara para solictar Acceso")

                    procesar_solicitud()

            except sr.UnknownValueError:
                print("No se entendió el audio, intenta de nuevo.")
            except sr.RequestError as e:
                print(f"Error con el servicio de reconocimiento de voz: {e}")

def procesar_solicitud():
    """Realiza las acciones cuando se detecta la palabra 'solicitud' o se presiona '1' en consola"""
    lcd_string("Solicitud escuchada", LCD_LINE_1)
    photo_path = capturar_foto()
    if photo_path:
        enviar_foto_a_api(photo_path)
        reproducir_audio("Petición Enviada")

def consola_escuchar():
    """Escucha la entrada de consola y ejecuta la misma función que la palabra clave 'solicitud'"""
    while True:
        entrada = input()
        if entrada == '1':
            print("Se presionó '1' en la consola.")
            procesar_solicitud()

pwm = None

try:
    lcd_init()
    pwm = setup_servo()  # Configura el servomotor

    # Iniciar la escucha de la palabra clave en un hilo separado
    thread_audio = Thread(target=escuchar_palabra_clave)
    thread_audio.daemon = True
    thread_audio.start()

    # Iniciar la escucha de la consola en un hilo separado
    thread_consola = Thread(target=consola_escuchar)
    thread_consola.daemon = True
    thread_consola.start()

    # Iniciar la revisión de accesos permitidos en un hilo separado
    thread_revisar_accesos = Thread(target=revisar_api_accesos, args=(pwm,))
    thread_revisar_accesos.daemon = True
    thread_revisar_accesos.start()

    while True:
        print("Acerca tu tarjeta")

        # Lee la tarjeta
        uid, text = reader.read()
        uid_str = str(uid)
        print(f"UID leído: {uid_str}")
        
        # Muestra el UID en el display
        # lcd_string("TARJETA RECONOCIDA", LCD_LINE_1)
        # lcd_string(uid_str, LCD_LINE_2)

        # Consulta la API con el UID y muestra la respuesta
        api_response = search_by_uid(uid_str)
        if api_response:
            print(f"Respuesta de la API: {api_response}")
            if "names" in api_response and "id" in api_response:
                names = api_response["names"]
                user_id = api_response["id"]
                
                # Mostrar mensaje de que se acerque a la cámara
                reproducir_audio(f"Hola {names}. Por favor, acérquese a la cámara.")
                lcd_string(f"Hola, {names}", LCD_LINE_1)
                lcd_string("Acérquese a la cámara", LCD_LINE_2)
                
                
                # Esperar un momento para que el usuario vea el mensaje
                time.sleep(1)
                
                # Llamar a la función main para verificar el reconocimiento facial
                if main(user_id):
                    # Si la persona es autorizada, activar el servomotor
                    set_servo_angle(pwm, 180)
                    time.sleep(5)
                    set_servo_angle(pwm, 0)

                    # Mostrar mensaje de bienvenida
                    lcd_string("Bienvenido, " + names, LCD_LINE_1)
                    lcd_string("Acceso permitido", LCD_LINE_2)
                    reproducir_audio(f"Bienvenido {names}, puede ingresar.")

                    # Registrar acceso permitido
                    report_access(status="Permitido", breakPoint="CAMARA", person_id=user_id)
                else:
                    # Si el reconocimiento facial falla
                    print("Acceso denegado")
                    lcd_string("Acceso denegado", LCD_LINE_1)
                    time.sleep(2)
                    reproducir_audio("Lo sentimos, no puede ingresar a esta casa por su seguridad.")

                    # Registrar acceso denegado
                    report_access(status="Denegado", breakPoint="CAMARA", person_id=user_id)
            else:
                print("El formato de la respuesta de la API no es el esperado.")
                reproducir_audio("Lo sentimos, no se pudo verificar su identidad.")
        else:
            print("No se pudo obtener respuesta de la API.")
            lcd_string("UID desconocido", LCD_LINE_1)
            lcd_string("Acceso denegado", LCD_LINE_2)
            time.sleep(2)
            reproducir_audio("Lo sentimos, no se reconoce su tarjeta.")

            # Registrar acceso denegado en el punto de ruptura RFID
            report_access(status="Denegado", breakPoint="RFID")

        # Limpia el display
        lcd_string("", LCD_LINE_1)
        lcd_string("", LCD_LINE_2)

        # Espera antes de la siguiente lectura
        print("Esperando al siguiente usuario...")
        time.sleep(2)

except KeyboardInterrupt:
    print("Programa terminado por el usuario")

finally:
    if pwm is not None:
        pwm.stop()  # Detiene el PWM del servomotor
    GPIO.cleanup()
