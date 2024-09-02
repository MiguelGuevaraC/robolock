


$(document).ready(function () {
    var startDate, endDate;
    var mensajesEnviadosChart, analisisCostosChart;

    // Establecer las fechas por defecto al cargar la página
    function initializeDates() {
        var fechaInicio = new Date();
        fechaInicio.setDate(1); // Establece la fecha de inicio al primer día del mes
    
        var fechaFin = new Date(); 
        fechaFin.setDate(fechaFin.getDate() + 1); // Establece la fecha de fin al día siguiente
    
        startDate = fechaInicio.toISOString().split("T")[0];
        endDate = fechaFin.toISOString().split("T")[0];
    
        $("#fechaInicio").val(startDate);
        $("#fechaFin").val(endDate);
    
        // Inicializar los gráficos con fechas predeterminadas
        fetchDataAndUpdateCharts(startDate, endDate);
    }

    initializeDates();

    $("#filtrar").click(function () {
        var fechaInicioInput = $("#fechaInicio").val();
        var fechaFinInput = $("#fechaFin").val();

        if (fechaInicioInput) {
            startDate = new Date(fechaInicioInput).toISOString().split("T")[0];
        } else {
            var defaultStartDate = new Date();
            defaultStartDate.setDate(1);
            startDate = defaultStartDate.toISOString().split("T")[0];
        }

        if (fechaFinInput) {
            endDate = new Date(fechaFinInput).toISOString().split("T")[0];
        } else {
            endDate = new Date().toISOString().split("T")[0];
        }

        fetchDataAndUpdateCharts(startDate, endDate);
    });

    function fetchDataAndUpdateCharts(startDate, endDate) {
        $.ajax({
            url: "dataDashboard",
            method: "GET",
            data: {
                fechaStart: startDate,
                fechaEnd: endDate,
            },
            success: function (response) {
                updateCharts(response);
            },
        });
    }

    function updateCharts(data) {
        const { estadoData, alcanceData, totalAccesosPermitidos, totalAccesosDenegados } = data;
    
        // Verifica si los datos están en el formato esperado
        console.log("estadoData:", estadoData);
        console.log("alcanceData:", alcanceData);
        console.log("totalAccesosPermitidos:", totalAccesosPermitidos);
        console.log("totalAccesosDenegados:", totalAccesosDenegados);
    
        // Destruir gráficos anteriores si existen
        if (window.mensajesEnviadosChart) window.mensajesEnviadosChart.destroy();
        if (window.analisisCostosChart) window.analisisCostosChart.destroy();
    
        // Crear gráfico de mensajes enviados
        const ctx1 = document.getElementById("mensajesEnviadosChart").getContext("2d");
        window.mensajesEnviadosChart = new Chart(ctx1, {
            type: "line",
            data: {
                labels: estadoData.labels,  // ['Exitoso']
                datasets: [{
                    label: "Mensajes Enviados",
                    data: estadoData.data,    // [1]
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: getChartOptions(),
        });
    
        // Crear gráfico de costos
        const ctx2 = document.getElementById("analisisCostosChart").getContext("2d");
        window.analisisCostosChart = new Chart(ctx2, {
            type: "bar",
            data: {
                labels: alcanceData.labels,  // ['Tarjeta']
                datasets: [{
                    label: "Costo (S/)",
                    data: alcanceData.data,    // [1]
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1,
                    borderRadius: 5,
                }],
            },
            options: getChartOptions(),
        });
    
        // Actualizar valores de total de mensajes y costo total
        $("#accessPermitidos").text("S/ " + totalAccesosPermitidos);
        $("#accessDenegados").text(totalAccesosDenegados);
        $("#accessByDay").text("S/ " + data.accesosPorDia.reduce((a, b) => a + b, 0)); // Sumar todos los accesos por día
    }
    
});
