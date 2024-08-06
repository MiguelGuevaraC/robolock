// $(document).ready(function () {
//     $("#registroStudent").submit(function (event) {
//         event.preventDefault(); // Evita que el formulario se envíe por el método tradicional

//         var token = $('meta[name="csrf-token"]').attr("content");
//         var name = $("#name").val();

//         $.ajax({
//             url: "estudiante",
//             type: "POST",
//             data: {
//                 name: name,
//                 _token: token,
//             },
//             success: function (data) {
//                 console.log("Respuesta del servidor:", data);
//                 $.niftyNoty({
//                     type: "purple",
//                     icon: "fa fa-check",
//                     message: "Registro exitoso",
//                     container: "floating",
//                     timer: 4000,
//                 });
//                 var table = $("#tbRoles").DataTable();
//                 table.row
//                     .add({
//                         id: data.id,
//                         name: name,
//                     })
//                     .draw(false);
//                 $("#cerrarModal").click();
//             },
//             error: function (jqXHR, textStatus, errorThrown) {
//                 console.log("Error al registrar:", errorThrown);
//                 $.niftyNoty({
//                     type: "danger",
//                     icon: "fa fa-times",
//                     message: "Error al registrar: " + textStatus,
//                     container: "floating",
//                     timer: 4000,
//                 });
//             },
//         });
//     });
// });

$("#btonNuevo").click(function (e) {
    $("#registroPersona")[0].reset();
    $("#modalNuevoStudent").modal("show");
});

$(document).on("click", "#cerrarModal", function () {
    $("#modalNuevoStudent").modal("hide");
});

let video = document.getElementById("video");
let canvas = document.getElementById("canvas");
let cameraSelect = document.getElementById("cameraSelect");
let capturedPhotos = document
    .getElementById("capturedPhotos")
    .querySelector(".row");
let captureBtn = document.getElementById("captureBtn");
let photoUpload = document.getElementById("photoUpload");
let photos = [];
let currentStream = null;

// Función para iniciar la cámara
function startCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach((track) => track.stop());
    }
    let constraints = {
        video: {
            deviceId: cameraSelect.value
                ? { exact: cameraSelect.value }
                : undefined,
        },
    };
    navigator.mediaDevices
        .getUserMedia(constraints)
        .then((stream) => {
            currentStream = stream;
            video.srcObject = stream;
            video.play();
            video.style.display = "block";
            captureBtn.style.display = "block";
        })
        .catch((error) => {
            console.log(error);
        });
}

// Función para detener la cámara
function stopCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach((track) => track.stop());
        currentStream = null;
    }
    video.style.display = "none";
    captureBtn.style.display = "none";
}

if (navigator.mediaDevices && typeof navigator.mediaDevices.enumerateDevices === 'function') {
    console.log("MediaDevices API está disponible");

    // Verifica si la página está servida a través de HTTPS
    if (location.protocol !== 'https:') {
        // Mostrar un mensaje al usuario sobre la necesidad de HTTPS
        const confirmation = confirm("La conexión no es segura (no está en HTTPS). ¿Deseas habilitar la cámara de todos modos? La funcionalidad puede no funcionar correctamente en una conexión no segura.");
        
        if (!confirmation) {
            console.log("El usuario ha decidido no habilitar la cámara.");
            return;
        }
    }

    navigator.mediaDevices.enumerateDevices()
        .then((devices) => {
            console.log("Dispositivos encontrados:", devices);
            
            const videoDevices = devices.filter((device) => device.kind === "videoinput");
            console.log("Dispositivos de video encontrados:", videoDevices);
            
            const cameraSelect = document.getElementById('cameraSelect');
            if (!cameraSelect) {
                console.log("Elemento con ID 'cameraSelect' no encontrado.");
                return;
            }

            cameraSelect.innerHTML = '';

            videoDevices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = `Cámara ${index + 1} (${device.label || 'Desconocida'})`;
                cameraSelect.appendChild(option);
            });

            cameraSelect.addEventListener('change', function () {
                const selectedDeviceId = cameraSelect.value;
                console.log("Cámara seleccionada:", selectedDeviceId);
                if (selectedDeviceId) {
                    startCamera(selectedDeviceId);
                } else {
                    stopCamera();
                }
            });
        })
        .catch((error) => {
            console.log("Error al enumerar los dispositivos:", error);
        });
} else {
    console.log("La API de MediaDevices no está disponible.");
}





// Encender y apagar la cámara según el estado del modal
$("#modalNuevoStudent").on("shown.bs.modal", function () {
    if (cameraSelect.value) {
        startCamera();
    }
});

$("#modalNuevoStudent").on("hidden.bs.modal", function () {
    stopCamera();
});

// Capturar la foto
captureBtn.addEventListener("click", function () {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    let context = canvas.getContext("2d");
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(function (blob) {
        let url = URL.createObjectURL(blob);
        photos.push({ url: url, blob: blob });
        updateCapturedPhotos();
    }, "image/png");
});

// Actualizar la vista de las fotos capturadas
function updateCapturedPhotos() {
    capturedPhotos.innerHTML = "";
    photos.forEach((photo, index) => {
        let col = document.createElement("div");
        col.className = "col-6 mb-3"; // Configura para dos columnas
        let photoContainer = document.createElement("div");
        photoContainer.className = "d-flex align-items-center";

        let img = document.createElement("img");
        img.src = photo.url;
        img.className = "captured-photo mr-2";
        img.style.maxWidth = "100%";
        img.style.maxHeight = "100px";
        img.style.objectFit = "cover";

        let removeBtn = document.createElement("button");
        removeBtn.textContent = "Eliminar";
        removeBtn.className = "btn btn-danger btn-sm ml-2";
        removeBtn.style.marginLeft = "10px";
        removeBtn.addEventListener("click", () => {
            photos.splice(index, 1);
            updateCapturedPhotos();
        });

        photoContainer.appendChild(img);
        photoContainer.appendChild(removeBtn);
        col.appendChild(photoContainer);
        capturedPhotos.appendChild(col);
    });
}

// Manejar la carga de imágenes
photoUpload.addEventListener("change", function () {
    let files = photoUpload.files;
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let url = URL.createObjectURL(file);
        photos.push({ url: url, blob: file });
    }
    updateCapturedPhotos();
});

$(document).ready(function () {
    $("#registroPersona").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        let telefono = formData.get("telefono").replace(/\s+/g, "");

        // Validar que telefono tenga exactamente 9 dígitos
        let telefonoRegex = /^\d{9}$/;
        if (telefono != "" && telefono && !telefonoRegex.test(telefono)) {
            toastr.error(
                "El teléfono debe contener exactamente 9 dígitos sin espacios.",
                "Error en el teléfono"
            );
            return;
        }
        formData.set("telefono", telefono);

        // Validar que haya al menos 3 fotos
        if (photos.length < 3) {
            toastr.error("Debe enviar al menos 3 fotos.", "Error en las fotos");
            return;
        }

        photos.forEach((photo, index) => {
            formData.append(
                `photos[${index}]`,
                photo.blob,
                `photo${index}.png`
            );
        });

        $.ajax({
            type: "POST",
            url: "estudiante",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                toastr.success("Persona registrada con éxito.", "¡Éxito!");
                $("#modalNuevoStudent").modal("hide");
                $("#registroPersona")[0].reset();
                $("#tbStudents").DataTable().ajax.reload();
                photos = [];
                updateCapturedPhotos();
                stopCamera();
            },
            error: function (error) {
                if (error.status === 422) {
                    let errors = error.responseJSON.errors;
                    let errorMessage = "Errores de validación:";
                    for (let field in errors) {
                        errorMessage += `\n- ${errors[field].join(" ")}`;
                    }
                    toastr.error(errorMessage, "Error de Validación");
                } else {
                    console.log(error);
                    toastr.error("Error al guardar la persona.", "Error");
                }
            },
        });
    });
});
