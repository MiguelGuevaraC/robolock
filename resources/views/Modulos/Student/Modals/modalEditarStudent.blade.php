<style>
    /* Estilos generales para el modal */
    .modal-header {
        background-color: #ffffff;
        color: black;
    }

    .modal-dialog {
        padding: 10px;
    }

    .modal-title {
        font-weight: bold;
    }

    .modal-body {
        padding: 5px;
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
    #cameraWrapperE {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 8px;
        background-color: #f9f9f914;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #videoE {
        border-radius: 5px;
    }

    #captureBtnE {
        margin-top: 10px;
    }

    /* Estilos para las fotos capturadas */
    #capturedPhotosE {
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
    }

    .captured-photoE {
        border-radius: 8px;
        object-fit: cover;
        width: 50%;
        height: auto;
        padding: 10px;
    }

    /* Estilos para el contenedor de fotos */
    .captured-photoE-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .captured-photoE-container img {
        max-width: 100%;
        height: auto;
    }

    .captured-photoE-container button {
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

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditarPersonE" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabel"><strong>Editar Persona</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroPersonE" enctype="multipart/form-data">

                    @method('PUT')
                    @csrf

                    <div class="row">
                        <!-- Columna 1: Información Personal y Datos de Identidad -->
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="documentNumberE" class="form-label">N° IDENTIDAD:</label>
                                <input type="text" class="form-control" name="documentNumberE" id="documentNumberE"
                                    placeholder="Ingrese el número de identidad" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="namesE" class="form-label">Nombres:</label>
                                <input type="text" class="form-control" name="namesE" id="namesE"
                                    placeholder="Ingrese los nombres completos" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="fatherSurnameE" class="form-label">Apellido Paterno:</label>
                                <input type="text" class="form-control" name="fatherSurnameE" id="fatherSurnameE"
                                    placeholder="Ingrese el apellido paterno" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="motherSurnameE" class="form-label">Apellido Materno:</label>
                                <input type="text" class="form-control" name="motherSurnameE" id="motherSurnameE"
                                    placeholder="Ingrese el apellido materno" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="dateOfBirthE" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" name="dateOfBirthE" id="dateOfBirthE"
                                    placeholder="Seleccione la fecha de nacimiento" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="telefonoE" class="form-label">Teléfono:</label>
                                <input type="text" class="form-control" name="telefonoE" id="telefonoE"
                                    placeholder="Ingrese el número de teléfono">
                            </div>
                            <div class="form-group mb-4">
                                <label for="emailE" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="emailE" id="emailE"
                                    placeholder="Ingrese el correo electrónico">
                            </div>
                        </div>

                        <!-- Columna 2: Datos de Contacto y Fecha de Nacimiento -->
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="UIDE" class="form-label">UID:</label>
                                <input type="text" class="form-control" name="UIDE" id="UIDE"
                                    placeholder="Ingrese el UID" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="photoUploadE" class="form-label">Subir Imágenes (JPG, PNG):</label>
                                <input type="file" class="form-control" id="photoUploadE" name="photoUpload[]"
                                    accept="image/jpeg, image/png" multiple>
                            </div>
                            <div class="form-group mb-4">
                                <label for="cameraSelectE" class="form-label">Elegir Cámara:</label>
                                <select id="cameraSelectE" class="form-control">
                                    <option value="">Seleccionar cámara</option>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Capturar Fotos:</label>
                                <div id="cameraWrapperE" class="mb-3">
                                    <video id="videoE" width="100%" autoplay style="display: none;"></video>
                                    <canvas id="canvasE" style="display: none;"></canvas>
                                </div>
                                <button type="button" id="captureBtnE" class="btn btn-primary mb-2"
                                    style="display: none;">Tomar Foto</button>
                            </div>
                        </div>

                        <!-- Columna 3: Captura y Visualización de Fotos -->
                        <div class="col-md-4">
                            <div class="form-group mb-4 text-center">
                                <label class="form-label">Fotos Capturadas:</label>
                                <div id="capturedPhotosE" class="overflow-auto"
                                    style="max-height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                    <!-- Aquí se mostrarán las fotos capturadas -->
                                    <div class="row">
                                        <!-- Fotos capturadas se añadirán aquí dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="idE">
                    <input type="hidden" class="form-control" name="photosE" id="photosE">

                    <div class="modal-footer">
              
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
