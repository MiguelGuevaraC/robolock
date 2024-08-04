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
//                 console.error("Error al registrar:", errorThrown);
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

$(document).ready(function () {
    $("#registroStudent").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        // Leer el archivo Excel
        let file = $("#excelFile")[0].files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, { type: "array" });

                // Suponiendo que los datos están en la primera hoja
                let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                let excelData = XLSX.utils.sheet_to_json(firstSheet, {
                    header: 1,
                });

                // Guardar el archivo en el servidor
                formData.append("excelFile", file);
                $("#modalNuevoStudent").modal("hide");
                // Mostrar alerta de espera
                Swal.fire({
                    title: "Por favor espera...",
                    text: "Estamos procesando tu solicitud.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Ajustar el z-index para que SweetAlert esté por encima del modal
                        $(".swal2-container").css("z-index", "2000");
                    },
                });

                // Realizar la solicitud AJAX
                $.ajax({
                    url: "importExcel", // Ajusta la ruta según tu configuración de Laravel
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        // Cerrar la alerta de SweetAlert
                        Swal.close();
                        // Aquí puedes manejar la respuesta del servidor

                        $("#tbStudents").DataTable().ajax.reload();
                    },
                    error: function (xhr, status, error) {
                        // Cerrar la alerta de SweetAlert
                        Swal.close();
                        // Mostrar mensaje de error
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Hubo un problema al procesar la solicitud.",
                        });
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                    },
                });
            };
            reader.readAsArrayBuffer(file);
        } else {
            Swal.fire({
                icon: "warning",
                title: "Archivo no seleccionado",
                text: "Por favor, selecciona un archivo Excel.",
            });
        }
    });
});

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
            console.error(error);
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

// Enumerar dispositivos de vídeo y configurar el selector de cámara
navigator.mediaDevices
    .enumerateDevices()
    .then((devices) => {
        devices = devices.filter((device) => device.kind === "videoinput");
        devices.forEach((device, index) => {
            let option = document.createElement("option");
            option.value = device.deviceId;
            option.text = device.label || `Cámara ${index + 1}`;
            cameraSelect.appendChild(option);
        });

        cameraSelect.addEventListener("change", function () {
            if (cameraSelect.value) {
                startCamera();
            } else {
                stopCamera();
            }
        });
    })
    .catch((error) => {
        console.error(error);
    });

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

// Enviar los datos al servidor
$("#registroPersona").on("submit", function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    photos.forEach((photo, index) => {
        formData.append(`photos[${index}]`, photo.blob, `photo${index}.png`);
    });

    $.ajax({
        type: "POST",
        url: "student",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            alert("Persona guardada exitosamente");
            $("#modalNuevoStudent").modal("hide");
            $("#registroPersona")[0].reset();
            photos = [];
            updateCapturedPhotos();
            stopCamera();
        },
        error: function (error) {
            console.error(error);
            alert("Error al guardar la persona");
        },
    });
});
