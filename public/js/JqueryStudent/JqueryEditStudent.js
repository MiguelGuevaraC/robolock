function editPerson(id) {
    $("#registroPersonE")[0].reset(); // Reinicia el formulario para evitar datos residuales

    // Muestra el modal de edición
    $("#modalEditarPersonE").modal("show");
    $("#idE").val(id);
    
    // Petición AJAX para obtener los datos del estudiante por su ID
    $.ajax({
        url: "estudiante/" + id,
        type: "GET",
        dataType: "json",
        success: function (data) {
            // Rellena el formulario con los datos del estudiante
            $("#documentNumberE").val(data.documentNumber);
            $("#namesE").val(data.names);
            $("#fatherSurnameE").val(data.fatherSurname);
            $("#motherSurnameE").val(data.motherSurname);
            $("#dateOfBirthE").val(data.dateBirth);
            $("#telefonoE").val(data.telephone);
            $("#emailE").val(data.email);
            $("#UIDE").val(data.uid);

            // Limpiar fotos anteriores y actualizar la variable photosE
            photosE = []; // Reinicia el arreglo de fotos

            if (data.photos && data.photos.length > 0) {
                // Función asincrónica para cargar fotos
                const loadPhotos = async () => {
                    for (let photo of data.photos) {
                        try {
                            // Descarga la imagen como Blob
                            let response = await fetch('/robolock' + photo.photoPath); // Asume que photo.photoPath es la URL de la imagen
                            if (!response.ok) throw new Error('Network response was not ok');
                            let blob = await response.blob();

                            // Crea un objeto de foto con URL y Blob
                            let photoObj = {
                                url: '/robolock' + photo.photoPath,
                                blob: blob
                            };
                            console.log(photoObj);

                            photosE.push(photoObj); // Almacena la foto en el arreglo
                            
                        } catch (error) {
                            console.log("Error al cargar la foto:", error);
                        }
                    }
                    
                    // Actualiza la visualización de las fotos después de agregar todas las fotos
                    updateCapturedPhotosE();
                };

                loadPhotos();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al cargar datos del estudiante:", errorThrown);
        }
    });
}

// Función para eliminar el estudiante
$("#btnEliminarEstudianteE").click(function () {
    var id = $("#idE").val();

    $.ajax({
        url: "estudiante/" + id,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
            toastr.success("Estudiante eliminado exitosamente.");
            $("#modalEditarPersonE").modal("hide");
            // Aquí podrías recargar la lista de estudiantes o hacer cualquier otra acción necesaria
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("Error al eliminar estudiante.");
            console.log("Error al eliminar estudiante:", errorThrown);
        },
    });
});

$(document).on("click", "#cerrarModalE", function () {
    $("#modalEditarPersonE").modal("hide");
});

let videoE = document.getElementById("videoE");
let canvasE = document.getElementById("canvasE");
let cameraSelectE = document.getElementById("cameraSelectE");
let capturedPhotosE = document
    .getElementById("capturedPhotosE")
    .querySelector(".row");
let captureBtnE = document.getElementById("captureBtnE");
let photoUploadE = document.getElementById("photoUploadE");

let currentStreamE = null;

// Función para iniciar la cámara
function startCameraE() {
    if (currentStreamE) {
        currentStreamE.getTracks().forEach((track) => track.stop());
    }
    let constraints = {
        video: {
            deviceId: cameraSelectE.value
                ? { exact: cameraSelectE.value }
                : undefined,
        },
    };
    navigator.mediaDevices
        .getUserMedia(constraints)
        .then((stream) => {
            currentStreamE = stream;
            videoE.srcObject = stream;
            videoE.play();
            videoE.style.display = "block";
            captureBtnE.style.display = "block";
        })
        .catch((error) => {
            console.error(error);
        });
}
captureBtnE.addEventListener("click", function () {
    canvasE.width = videoE.videoWidth;
    canvasE.height = videoE.videoHeight;
    let context = canvasE.getContext("2d");
    context.drawImage(videoE, 0, 0, canvasE.width, canvasE.height);
    canvasE.toBlob(function (blob) {
        let url = URL.createObjectURL(blob);
        photosE.push({ url: url, blob: blob });
        updateCapturedPhotosE();
    }, "image/png");
});

// Función para detener la cámara
function stopCameraE() {
    if (currentStreamE) {
        currentStreamE.getTracks().forEach((track) => track.stop());
        currentStreamE = null;
    }
    videoE.style.display = "none";
    captureBtnE.style.display = "none";
}

// Enumerar dispositivos de vídeo y configurar el selector de cámara
navigator.mediaDevices.enumerateDevices()
    .then((devices) => {
        const videoDevices = devices.filter((device) => device.kind === "videoinput");
        const cameraSelectE = document.getElementById('cameraSelectE');  // Asegúrate de que el ID es correcto

        cameraSelectE.innerHTML = '';  // Limpiamos las opciones previas

        videoDevices.forEach((device, index) => {
            const option = document.createElement("option");
            option.value = device.deviceId;
            option.text = device.label || `Cámara ${index + 1}`;
            cameraSelectE.appendChild(option);
        });

        cameraSelectE.addEventListener("change", function () {
            const selectedDeviceId = cameraSelectE.value;
            if (selectedDeviceId) {
                startCameraE(selectedDeviceId);  // Pasar el deviceId a la función para iniciar la cámara
            } else {
                stopCameraE();  // Función para detener la cámara
            }
        });
    })
    .catch((error) => {
        console.error("Error al enumerar los dispositivos:", error);
    });


// Función para actualizar las fotos capturadas

function updateCapturedPhotosE() {
    capturedPhotosE.innerHTML = "";
    photosE.forEach((photo, index) => {
        let col = document.createElement("div");
        col.className = "col-6 mb-3"; // Configura para dos columnas
        let photoContainer = document.createElement("div");
        photoContainer.className = "d-flex align-items-center";

        let img = document.createElement("img");
        img.src = photo.url ? photo.url : "/robolock" + photo.photoPath;
        img.className = "captured-photoE mr-2";
        img.style.maxWidth = "100%";
        img.style.maxHeight = "100px";
        img.style.objectFit = "cover";

        let removeBtn = document.createElement("button");
        removeBtn.textContent = "Eliminar";
        removeBtn.className = "btn btn-danger btn-sm ml-2";
        removeBtn.style.marginLeft = "10px";
        removeBtn.addEventListener("click", () => {
            photosE.splice(index, 1);
            updateCapturedPhotosE();
        });

        photoContainer.appendChild(img);
        photoContainer.appendChild(removeBtn);
        col.appendChild(photoContainer);
        capturedPhotosE.appendChild(col);
    });
    console.log(photosE);
    document.getElementById("photosE").value = JSON.stringify(photosE);
}

// Manejar la carga de imágenes
photoUploadE.addEventListener("change", function () {
    let files = photoUploadE.files;
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let url = URL.createObjectURL(file);
        photosE.push({ url: url, blob: file });
    }
    updateCapturedPhotosE();
});

// Función para inicializar el modal
$("#modalEditarPersonE").on("shown.bs.modal", function () {
    if (cameraSelectE.value) {
        startCameraE();
    }
});

$("#modalEditarPersonE").on("hidden.bs.modal", function () {
    stopCameraE();
    photosE = [];
    updateCapturedPhotosE();
});








$("#registroPersonE").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    let formDataE = new FormData(this);
    formDataE.append('documentNumber', $("#documentNumberE").val());
    // Añadir las fotos al objeto FormData
    photosE.forEach((photo, index) => {
        formDataE.append(`photosEd[${index}]`, photo.blob, `photo${index}.png`);
    });

    let telefono = formDataE.get("telefonoE").replace(/\s+/g, "");

    // Validar que telefono tenga exactamente 9 dígitos
    let telefonoRegex = /^\d{9}$/;
    if (telefono != "" && telefono && !telefonoRegex.test(telefono)) {
        toastr.error(
            "El teléfono debe contener exactamente 9 dígitos sin espacios.",
            "Error en el teléfono"
        );
        return;
    }
    formDataE.set("telefonoE", telefono);

    $.ajax({
        url: "estudiante/" + $("#idE").val(), // Usar el ID del formulario para formar la URL
        type: "POST", // Usar POST y agregar el método PUT en el FormData
        data: formDataE,
        contentType: false,
        processData: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            $("#modalEditarPersonE").modal("hide");
            $("#tbStudents").DataTable().ajax.reload();

            $.niftyNoty({
                type: "purple",
                icon: "fa fa-check",
                message: "Persona actualizada correctamente",
                container: "floating",
                timer: 4000,
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al actualizar Persona:", errorThrown);
            $.niftyNoty({
                type: "danger",
                icon: "fa fa-times",
                message: "Error al actualizar Persona",
                container: "floating",
                timer: 4000,
            });
        },
    });
});
