// Evento submit del formulario de edición
$("#registroUsuarioE").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    // Obtener los datos del formulario
    var formData = {
        password: $("#passE").val(),
        id: $("#idE").val(),
        username: $("#usernameE").val(),
        typeofUser_id: $("#typeuserE").val(),
        _token: token,
    };

    var token = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: "user/" + formData.id,
        type: "PUT",
        data: formData,
        headers: {
            "X-CSRF-TOKEN": token,
        },
        success: function (response) {
            $("#modalNuevoUsuarioE").modal("hide");
            $("#tbUsuarios").DataTable().ajax.reload();

            $.niftyNoty({
                type: "purple",
                icon: "fa fa-check",
                message: "Tipo de usuario actualizado correctamente",
                container: "floating",
                timer: 4000,
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al actualizar tipo de usuario:", errorThrown);
            $.niftyNoty({
                type: "danger",
                icon: "fa fa-times",
                message: "Error al actualizar tipo de usuario",
                container: "floating",
                timer: 4000,
            });
        },
    });
});
