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
            if ([0, 1, 2, 3, 4, 5, 6].includes(colIdx)) {
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
            { data: "created_at", title: "Fecha" },
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
            }
        });
    }, 5000);
}

$(document).ready(function () {
    initialTableAccess();
});
