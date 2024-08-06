<style>
    /* Estilos generales para el modal */
    .modal-header {
        background-color: #ffffff;
        color: white;
    }
    .modal-dialog{
        padding: 60px;
    }

    .modal-title {
        font-weight: bold;
    }

    .modal-body {
        padding: 10px;
    }


    .modal-footer {
        background-color: #ffffff;
    }

    /* Estilos para el formulario */
    .form-label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-control {
        border-radius: 5px;
        margin-bottom: 10px;
        background: #f9f9f9;
    }

    /* Estilos para el área de captura de fotos */
    #cameraWrapper {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        background-color: #f9f9f9;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #video {
        border-radius: 5px;
    }

    #captureBtn {
        margin-top: 10px;
    }

    /* Estilos para las fotos capturadas */
    #capturedPhotos {
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
    }

    .captured-photo {
        border-radius: 8px;
        object-fit: cover;
        width: 50%;
        height: auto;
        padding: 10px;
    }

    /* Estilos para el contenedor de fotos */
    .captured-photo-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .captured-photo-container img {
        max-width: 100%;
        height: auto;
    }

    .captured-photo-container button {
        margin-top: 5px;
        width: 100%;
    }

    /* Estilo para el botón de eliminación */
    .remove-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        padding: 5px;
    }

    .remove-btn:hover {
        background-color: #c82333;
    }


</style>

<!-- Cargar jQuery primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Cargar Toastr -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Tu script que utiliza Toastr -->
<script src="path/to/your/script.js"></script>

<!-- Modal CREAR -->
<div class="modal fade" id="modalNuevoStudent" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoModalLabel"><strong>Registrar Persona</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroPersona" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Columna 1: Información Personal y Datos de Identidad -->
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="documentNumber" class="form-label">N° IDENTIDAD:</label>
                                <input type="text" class="form-control" name="documentNumber" id="documentNumber"
                                    placeholder="Ingrese el número de identidad" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="names" class="form-label">Nombres:</label>
                                <input type="text" class="form-control" name="names" id="names"
                                    placeholder="Ingrese los nombres completos" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="fatherSurname" class="form-label">Apellido Paterno:</label>
                                <input type="text" class="form-control" name="fatherSurname" id="fatherSurname"
                                    placeholder="Ingrese el apellido paterno" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="motherSurname" class="form-label">Apellido Materno:</label>
                                <input type="text" class="form-control" name="motherSurname" id="motherSurname"
                                    placeholder="Ingrese el apellido materno" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="dateOfBirth" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" name="dateOfBirth" id="dateOfBirth"
                                    placeholder="Seleccione la fecha de nacimiento" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="telefono" class="form-label">Telefono:</label>
                                <input type="text" class="form-control" name="telefono" id="telefono"
                                    placeholder="Ingrese el número de teléfono" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="email" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Ingrese el correo electrónico" >
                            </div>
                        </div>

                        <!-- Columna 2: Datos de Contacto y Fecha de Nacimiento -->
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="uid" class="form-label">UID:</label>
                                <input type="text" class="form-control" name="UID" id="uid"
                                    placeholder="Ingrese el UID" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="photoUpload" class="form-label">Subir Imágenes (JPG, PNG):</label>
                                <input type="file" class="form-control" id="photoUpload" name="photoUpload[]"
                                    accept="image/jpeg, image/png" multiple>
                            </div>
                            <div class="form-group mb-4">
                                <label for="cameraSelect" class="form-label">Elegir Cámara:</label>
                                <select id="cameraSelect" class="form-control">
                                    <option value="">Seleccionar cámara</option>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Capturar Fotos:</label>
                                <div id="cameraWrapper" class="mb-3">
                                    <video id="video" width="100%" autoplay style="display: none;"></video>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                </div>
                                <button type="button" id="captureBtn" class="btn btn-primary mb-2"
                                    style="display: none;">Tomar Foto</button>
                            </div>
                        </div>

                        <!-- Columna 3: Captura y Visualización de Fotos -->
                        <div class="col-md-4">
                            <div class="form-group mb-4 text-center">
                                <label class="form-label">Fotos Capturadas:</label>
                                <div id="capturedPhotos" class="overflow-auto"
                                    style="max-height: 450px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                    <!-- Aquí se mostrarán las fotos capturadas -->
                                    <div class="row">
                                        <!-- Fotos capturadas se añadirán aquí dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark m-2" data-dismiss="modal"
                            tabindex="3">Cancelar</button>
                        <button type="submit" class="btn btn-success m-2" tabindex="4">Guardar</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>
