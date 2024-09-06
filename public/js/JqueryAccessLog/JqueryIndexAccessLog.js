var lenguag = {
    lengthMenu: "Mostrar _MENU_ Registros por paginas",
    zeroRecords: "No hay Registros",
    info: "Mostrando la pagina _PAGE_ de _PAGES_",
    infoEmpty: "",
    infoFiltered: "Filtrado de _MAX_ entradas en total",
    search: "Buscar:",
    paginate: {
        next: "Siguiente",
        previous: "Anterior",
    },
};

var search = {
    regex: true,
    caseInsensitive: true,
    type: "html-case-insensitive",
};

$("#tbAccess thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbAccess thead");

var lastFilteredCount = 0; // Variable para almacenar el último recuento de registros filtrados

var init = function (settings, json) {
    var api = this.api();
    var table = api.table().node(); // Asegúrate de obtener la referencia de la tabla

    api.columns()
        .eq(0)
        .each(function (colIdx) {
            var column = api.column(colIdx);
            var header = $(column.header());

            // Configurar filtro para columnas específicas
            if ([0, 1, 2, 3, 4, 5].includes(colIdx)) {
                var cell = $(".filters th").eq(header.index());
                var title = header.text();

                $(cell).html(
                    '<input type="text" placeholder="Escribe aquí..." />'
                );

                // Evento para filtrar cuando se escriba en el input
                $("input", cell)
                    .off("keyup change")
                    .on("keyup change", function (e) {
                        e.stopPropagation();

                        if (this.type === "text") {
                            var cursorPosition = this.selectionStart;
                            column.search(this.value, true, false).draw();
                        }
                    });
            } else {
                $(header).html("");
            }
        });
};

function initialTableAccess() {
    $("#tbAccess").DataTable().destroy();
    var table = $("#tbAccess").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "reporteaccesosAll",
            type: "GET",
            data: function (d) {},
            debounce: 500,
        },
        drawCallback: function (settings) {
            var api = this.api();
            var json = api.ajax.json();

            console.log(json.recordsFiltered);
            console.log(json.data);
            $("#countAccessTotal").text(json.recordsFiltered);
            $("#countAccess").text(json.countAccessed);
            $("#countNotAccessed").text(json.countNotAccessed);

            // Actualizar la variable lastFilteredCount con el valor actual de recordsFiltered
            lastFilteredCount = json.recordsFiltered;
        },
        orderCellsTop: true,
        fixedHeader: true,
        columns: [
            { data: "authorized_person.names", title: "Persona" },
            { data: "authorized_person.telephone", title: "Teléfono" },
            { data: "authorized_person.email", title: "Correo" },
            { data: "status", title: "Estado" },
            { data: "breakPoint", title: "Alcance" },
            { 
                data: "created_at", 
                title: "Fecha", 
                render: function(data, type, row) {
                    // Crear un objeto de fecha a partir del valor de 'created_at'
                    var date = new Date(data);
                    
                    // Formatear la fecha como "DD-MM-YY, HH:mm:ss"
                    var day = String(date.getDate()).padStart(2, '0');
                    var month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses empiezan desde 0
                    var year = String(date.getFullYear()).slice(-2);
                    var hours = String(date.getHours()).padStart(2, '0');
                    var minutes = String(date.getMinutes()).padStart(2, '0');
                    var seconds = String(date.getSeconds()).padStart(2, '0');
                    
                    return `${day}-${month}-${year}, ${hours}:${minutes}:${seconds}`;
                }
            },
            // { 
            //     data: "photo", 
            //     title: "Foto", 
            //     render: function(data, type, row) {
            //         return '<button class="btn btn-primary btn-sm view-photo" data-image-url="' + data + '">Ver Foto</button>';
            //     }
            // }
        ],
        dom: "Brtip",
        buttons: [],
        language: lenguag,
        search: search,
        initComplete: init,
        rowId: "id",
        stripeClasses: ["odd-row", "even-row"],
        scrollY: "300px",
        scrollX: true,
        autoWidth: true,
        pageLength: 50,
        lengthChange: false,
    });

    $('#tbAccess tbody').on('click', '.view-photo', function() {
        var imageUrl = $(this).data('image-url');

        Swal.fire({
            imageUrl: imageUrl,
            imageAlt: 'Foto Capturada',
            imageWidth: 400,
            imageHeight: 200,
            showCloseButton: true,

        });
    });

    // Configurar el temporizador para verificar nuevos registros cada 5 segundos
    setInterval(function () {
        $.ajax({
            url: "countRecords",
            type: "GET",
            success: function (data) {
                if (data > lastFilteredCount) {
                    table.ajax.reload(null, false); // Recargar la tabla sin cambiar de página
                    lastFilteredCount = data; // Actualizar el último recuento conocido
                }
            },
        });
    }, 5000);
}

$(document).ready(function () {
    initialTableAccess();
});
$(document).ready(function () {
    const checkInterval = 5000; // Intervalo de 5 segundos para actualizar notificaciones
    let currentAlert = null; // Variable para guardar la alerta activa
    let currentModal = null; // Variable para guardar el modal activo
    let notificationsToMarkAsSeen = []; // Array para guardar las notificaciones que deben marcarse como vistas

    // Función para cargar notificaciones nuevas
    function loadNotifications() {
        if (currentModal) return; // No cargar notificaciones si hay un modal activo

        $.get("notificationsNew", function (data) {
            console.log('Datos de notificaciones:', data); // Añade esto para verificar los datos
            data.forEach(function (notification) {
                if (notification.state === 1) {
                    if (currentAlert) {
                        console.log('Cerrando alerta anterior');
                        currentAlert.close();
                    }

                    const createdAt = new Date(notification.created_at);
                    const datePart = createdAt.toISOString().split("T")[0];
                    const timePart = createdAt.toTimeString().split(" ")[0];
                    const formattedDate = `${datePart},${timePart}`;
                    const text = `Solicitud recibida el ${formattedDate}`;

                    currentAlert = Swal.fire({
                        title: "Nueva Solicitud de Acceso",
                        text: text,
                        icon: "info",
                        position: "bottom-end",
                        showConfirmButton: true,
                        confirmButtonText: "Ver Detalles",
                        timer: 10000,
                        didOpen: (toast) => {
                            toast.addEventListener("click", () => {
                                console.log('Alerta clickeada, mostrando detalles');
                                showDetails(notification.id);
                            });
                        },
                    });

                    notificationsToMarkAsSeen.push(notification.id);
                }
            });
        });
    }

    // Mostrar modal con detalles de la notificación
    function showDetails(notificationId) {
        if (currentModal) return; // No abrir un nuevo modal si ya hay uno activo
    
        $.get(`notifications/${notificationId}`, function (data) {
            console.log('Datos de la notificación:', data); // Añade esto para verificar los datos
            const createdAt = new Date(data.created_at);
            const datePart = createdAt.toISOString().split("T")[0];
            const timePart = createdAt.toTimeString().split(" ")[0];
            const formattedDate = `${datePart}, ${timePart}`;
            const text = `${formattedDate}`;
    
            currentModal = Swal.fire({
                title: "Detalles de la Solicitud de Acceso",
                html: `
                    <div style="text-align: center;">
                        <img src="${data.photoPath}" alt="Imagen del Ingresante" style="width: 300px; height: 200px; object-fit: cover; border-radius: 8px;">
                        <p style="margin-top: 10px;">Solicitud recibida el ${text}</p>
                        <p style="font-weight: bold; margin-top: 10px;">Ingrese su contraseña para validar:</p>
                        <input type="password" id="access-password-${data.id}" class="swal2-input" placeholder="Contraseña" style="margin-top: 10px;">
                        <div style="margin-top: 20px;">
                            <button class="btn btn-success btn-sm action-button" data-id="${data.id}" data-action="grant">Dar Acceso</button>
                            <button class="btn btn-danger btn-sm deny-button">No Permitir</button>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: "swal-notifications-popup",
                },
                didOpen: () => {
                    $(".action-button").on("click", function () {
                        let action = $(this).data("action");
                        let password = $(`#access-password-${data.id}`).val();
    
                        if (password === "") {
                            Swal.showValidationMessage("Por favor ingrese la contraseña");
                            return;
                        }
    
                        $.ajax({
                            url: `notifications/${$(this).data("id")}`,
                            method: "PUT",
                            data: {
                                status: "Permitido",
                                action: action,
                                password: password,
                            },
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function (response) {
                                Swal.fire("Hecho", response.message, "success")
                                    .then(() => {
                                        currentModal = null;
                                        markNotificationsAsSeen();
                                    });
                            },
                            error: function (xhr) {
                                if (xhr.status === 422 && xhr.responseJSON.message === "Contraseña Incorrecta") {
                                    alert("Contraseña Incorrecta");
                                } else {
                                    Swal.fire("Error", "Se produjo un error al procesar la solicitud", "error");
                                }
                            },
                        });
                    });
    
                    $(".deny-button").on("click", function () {
                        $.ajax({
                            url: `notifications/${data.id}`,
                            method: "PUT",
                            data: {
                                status: "No Permitido",
                            },
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function (response) {
                                Swal.fire("Hecho", response.message, "success")
                                    .then(() => {
                                        currentModal = null;
                                        markNotificationsAsSeen();
                                    });
                            },
                            error: function (xhr) {
                                Swal.fire("Error", "Se produjo un error al procesar la solicitud", "error");
                            },
                        });
                    });
                },
                didClose: () => {
                    currentModal = null;
                    markNotificationsAsSeen();
                },
            });
        });
    }
    
    

    // Función para marcar las notificaciones como vistas
    function markNotificationsAsSeen() {
        if (notificationsToMarkAsSeen.length === 0) return;

        $.ajax({
            url: "notifications/mark-as-seen",
            method: "PUT",
            data: {
                ids: notificationsToMarkAsSeen,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function () {
                notificationsToMarkAsSeen = [];
            },
        });
    }

    // Llamar a la función para cargar notificaciones nuevas cada 6 segundos
    setInterval(loadNotifications, 6000);
});
