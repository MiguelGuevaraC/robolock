@php
    function namesPerson($person)
    {
        $nombre = '';
        if ($person->typeofDocument != 'RUC') {
            $nombre = $person->names . ' ' . $person->fatherSurname . ' ' . $person->motherSurname;
        } else {
            $nombre = $person->businessName;
        }
        return $nombre;
    }

@endphp

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php
    $vista = 'Home';
    $categoriaActual = 'home';
    $OpcionActual = 'vistaInicio';
    
    ?>

    <title>Mensajería | {{ $vista }}</title>
    <link type="image/png" href="plantillaNuevo\img\logo.png" rel="icon">


    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font [ OPTIONAL ]-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="plantillaNuevo\css\bootstrap.min.css" rel="stylesheet">


    <!--Nifty Stylesheet [ REQUIRED ]-->
    <link href="plantillaNuevo\css\nifty.min.css" rel="stylesheet">


    <!--Nifty Premium Icon [ DEMONSTRATION ]-->
    <link href="plantillaNuevo\css\demo\nifty-demo-icons.min.css" rel="stylesheet">


    <!--=================================================-->
    <!--Custom scheme [ OPTIONAL ]-->









    <!--=================================================-->




    <link rel="stylesheet" href="/manuelPardoWhatsapp/Cdn-Locales/pkgAwsome/css/all.css" />

    <!--Demo [ DEMONSTRATION ]-->
    <link href="plantillaNuevo\css\demo\nifty-demo.min.css" rel="stylesheet">



    <link href="plantillaNuevo\css\themes\type-c\theme-navy.min.css" rel="stylesheet">
    <!--Unite Gallery [ OPTIONAL ]-->
    <link href="plantillaNuevo\plugins\unitegallery\css\unitegallery.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/appPlantilla.css') }}">
    <style>
        #page-content1 {
            padding: 2px 50px;
        }

        .card-body-label,
        .card-body {
            background: #f9f9f9;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 128, 0, 0.2);
            border: 1px solid #c1e2c1;
            transition: all 0.3s ease;
            transform: perspective(1000px);
        }

        /* Efecto de hover con 3D */
        .card-body-label:hover {
            background: #f8fbf8;
            box-shadow: 0 8px 16px rgba(0, 128, 0, 0.3);
            transform: scale(1.02);
        }


        .card-body h4,
        .card-body p {
            color: #2e7d32;
        }

        .card-header {
            background: #ffffff0d;
            border-radius: 20px
        }

        .card {
            background: #ffffff0d;
            border-radius: 20px
        }

        .card-text {
            padding: 4px;
        }
    </style>


</head>

<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->

<body>
    <div id="container" class="effect aside-float aside-bright slide mainnav-out navbar-fixed">

        <!--NAVBAR-->
        <!--===================================================-->
        <header id="navbar">
            <div id="navbar-container" class="boxed">

                <!--Brand logo & name-->
                <!--================================-->
                <div class="navbar-header">
                    <a href='vistaInicio' class="navbar-brand">
                        {{-- <img src="plantillaNuevo\img\logo.png" alt="Nifty Logo" class="brand-icon"> --}}
                        <div class="brand-title">
                            <span class="brand-text">Mensajería</span>
                        </div>
                    </a>
                </div>
                <!--================================-->
                <!--End brand logo & name-->


                <!--Navbar Dropdown-->
                <!--================================-->
                <div class="navbar-content">
                    <ul class="nav navbar-top-links">

                        <!--Navigation toogle button-->
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                        <li class="tgl-menu-btn">
                            <a class="mainnav-toggle" href="#">
                                <i class="demo-pli-list-view"></i>

                            </a>
                        </li>


                    </ul>
                    <ul class="nav navbar-top-links">
                        <?php foreach ($groupMenu as $item): ?>

                        @foreach ($item['option_menus'] as $menu)
                            <li id="dropdown-<?php echo $item['id']; ?>" class="dropdown">
                                <a href="{{ $menu['route'] }}" data-tooltip="{{ $menu['name'] }}"
                                    class="btn-profesional dropdown-toggle text-right">
                                    <span class="ic-user pull-right">
                                        <i style="font-size:21px" class="{{ $menu['icon'] }}"></i>
                                    </span>
                                </a>
                            </li>
                        @endforeach

                        <?php endforeach; ?>

                        <li id="dropdown-user" class="dropdown">
                            <a href="#" data-tooltip="Perfil" data-toggle="dropdown"
                                class="btn-profesional dropdown-toggle text-right">
                                <span class="ic-user pull-right">
                                    <i style="font-size:21px" class="fa-solid fa-user"></i>
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right panel-default">
                                <ul class="head-list">
                                    <li>
                                        <a href="perfilD"><i class="demo-pli-male icon-lg icon-fw"></i> Perfil</a>
                                    </li>
                                    <li>
                                        <a href="logout"><i class="demo-pli-unlock icon-lg icon-fw"></i> Salir</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>



                </div>
                <!--================================-->
                <!--End Navbar Dropdown-->

            </div>
        </header>
        <!--===================================================-->
        <!--END NAVBAR-->

        <div class="boxed">

            <!--CONTENT CONTAINER-->
            <!--===================================================-->
            <div id="content-container">
                <div id="page-head">


                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End page title-->


                    <!--Breadcrumb-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <ol class="breadcrumb">
                        <li><a href='vistaInicio'><i class="demo-pli-home"></i></a></li>
                        <li><a href="{{ $OpcionActual }}">{{ $categoriaActual }}</a></li>

                    </ol>
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End breadcrumb-->

                </div>


                <div id="page-content1">


                    <!-- Widgets -->
                    <div class="row text-center ">
                        <!-- Total de Mensajes Enviados -->

                        <!-- Mensajes Pendientes -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body-label">
                                    <p style="text-align: left" class="card-title"><b>Filtro Fechas</b></p>

                                    <!-- Contenedor de la fila para las fechas -->
                                    <div class="row mt-3">
                                        <div class="col-6 mb-3">
                                            <input type="date" id="fechaInicio" class="form-control"
                                                placeholder="Fecha de Inicio">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <input type="date" id="fechaFin" class="form-control"
                                                placeholder="Fecha de Fin">
                                        </div>
                                    </div>

                                    <button id="filtrar" style="color:white;background: #7dc671"
                                        class="btn w-100 mt-3">Filtrar</button>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body-label">
                                    <div class="icon text-primary mb-3">
                                        <i class="fa fa-envelope fa-2x" style="color:#28a745"></i>
                                    </div>

                                    <h4 class="card-title">Accesos Permitidos</h4>
                                    <p class="card-text display-4" id="accessPermitidos">0</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mensajes Fallidos -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body-label">
                                    <div class="icon mb-3">
                                        <i class="fa-solid fa-ban fa-2x" style="color: #ff0000"></i>
                                    </div>
                                    <h4 class="card-title">Accesos Denegados</h4>
                                    <p class="card-text display-4" id="accessDenegados">0</p>
                                </div>
                            </div>
                        </div>

                        <!-- Costo Total -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body-label">
                                    <div class="icon text-success mb-3">

                                        <i class="fa-solid fa-calendar-day fa-2x"></i>
                                    </div>
                                    <h4 class="card-title">Accesos Hoy</h4>

                                    <p class="card-text display-4" id="accessByDay">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header" style="background:#ffffff0d;">
                                    <h4 class="card-title">Accesos por Estado</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="mensajesEnviadosChart" style="height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header" style="background:#ffffff0d;">
                                    <h4 class="card-title">Accesos por Alcance</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="analisisCostosChart" style="height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
            <nav id="mainnav-container">
                <div id="mainnav">

                    <!--Menu-->
                    <!--================================-->
                    <div id="mainnav-menu-wrap">
                        <div class="nano">
                            <div class="nano-content">

                                <!--Profile Widget-->



                                <!--Shortcut buttons-->
                                <!--================================-->

                                <!--================================-->
                                <!--End shortcut buttons-->
                                <div id="mainnav-profile" class="mainnav-profile">
                                    <div class="profile-wrap text-center">
                                        <div class="pad-btm">
                                            <img class="img-circle img-md"
                                                src="plantillaNuevo\img\profile-photos\1.png" alt="Profile Picture">
                                        </div>
                                        <a href="#profile-nav" class="box-block" data-toggle="collapse"
                                            aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                            <p class="mnp-name">{{ $user->typeUser->name }}</p>
                                            <span class="mnp-desc">{{ namesPerson($user->person) }}</span>
                                        </a>
                                    </div>
                                    <div id="profile-nav" class="collapse list-group bg-trans">
                                        <a href='vistaInicio' class="list-group-item">
                                            <i class="demo-pli-home icon-lg icon-fw"></i> Inicio
                                        </a>
                                        <a href="perfilD" class="list-group-item">
                                            <i class="demo-pli-male icon-lg icon-fw"></i> Ver Perfil
                                        </a>

                                        <a href="logout" class="list-group-item">
                                            <i class="demo-pli-unlock icon-lg icon-fw"></i> Salir
                                        </a>
                                    </div>
                                </div>

                                <ul id="mainnav-menu" class="list-group">
                                    <?php foreach ($groupMenuLeft as $categoria): ?>
                                    <?php if (!empty($categoria['option_menus']) && count($categoria['option_menus']) > 0): ?>
                                    <li class="<?= $categoria['nombre'] == $categoriaActual ? 'active-sub' : '' ?>">
                                        <a href="#">
                                            <i class="<?= $categoria['icon'] ?>"></i>
                                            <span class="menu-title"><?= strtoupper($categoria['name']) ?></span>
                                            <i class="arrow"></i>
                                        </a>
                                        <ul class="<?= $categoria['name'] == $categoriaActual ? 'collapse in' : '' ?>">
                                            <?php foreach ($categoria['option_menus'] as $item): ?>
                                            <li class="<?= $item['route'] == $OpcionActual ? 'active-link' : '' ?>">
                                                <a class="optionsMenu" href="<?= $item['route'] ?>">
                                                    <i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>






                            </div>
                        </div>
                    </div>
                    <!--================================-->
                    <!--End menu-->

                </div>
            </nav>
            <!--===================================================-->
            <!--END MAIN NAVIGATION-->

        </div>



        <!-- FOOTER -->
        <!--===================================================-->
        <footer id="footer">

            <!-- Visible when footer positions are fixed -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="show-fixed pad-rgt pull-right">
                You have <a href="#" class="text-main"><span class="badge badge-danger">3</span> pending
                    action.</a>
            </div>



            <!-- Visible when footer positions are static -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->




            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

            <p class="pad-lft">&#0169; 2024 Garzasoft</p>



        </footer>
        <!--===================================================-->
        <!-- END FOOTER -->


        <!-- SCROLL PAGE BUTTON -->
        <!--===================================================-->
        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>
        <!--===================================================-->
    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->





    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--jQuery [ REQUIRED ]-->
    <script src="plantillaNuevo\js\jquery.min.js"></script>


    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="plantillaNuevo\js\bootstrap.min.js"></script>


    <!--NiftyJS [ RECOMMENDED ]-->
    <script src="plantillaNuevo\js\nifty.min.js"></script>


    <!-- PARA MODAL DE ALERTAS SWEETALERT2-->
    <script src="/manuelPardoWhatsapp/Cdn-Locales/pkgSweetAlert/dist/sweetalert2.all.js"></script>

    <!--=================================================-->

    <!--Demo script [ DEMONSTRATION ]-->
    <script src="plantillaNuevo\js\demo\nifty-demo.min.js"></script>


    <!--Unite Gallery [ OPTIONAL ]-->
    <script src="plantillaNuevo\plugins\unitegallery\js\unitegallery.min.js"></script>
    <script src="plantillaNuevo\plugins\unitegallery\themes\tiles\ug-theme-tiles.js"></script>



    <!--Custom script [ DEMONSTRATION ]-->
    <!--===================================================-->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/JqueryInicio/JqueryInicio.js') }}"></script>
    <script src="/manuelPardoWhatsapp/Cdn-Locales/pkgAwsome/js/all.js"></script>
    <!-- Include Bootstrap CSS -->

    <!-- Include Chart.js -->
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa las fechas predeterminadas
            let startDate = '2024-01-01';
            let endDate = '2024-12-31';
    
            // Variables para almacenar el estado anterior de los datos
            let previousData = null;
    
            // Función para actualizar los gráficos
            function fetchDataAndUpdateCharts(startDate, endDate) {
                $.ajax({
                    url: "dataDashboard",
                    method: "GET",
                    data: { fechaInicio: startDate, fechaFin: endDate },
                    success: function(response) {
                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error,
                            });
                        } else {
                            // Comparar datos actuales con los datos anteriores
                            if (hasDataChanged(response)) {
                                updateCharts(response);
                                previousData = response; // Actualiza el estado anterior de los datos
                            }
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al recuperar los datos. Inténtalo de nuevo.',
                        });
                    }
                });
            }
    
            // Función para verificar si los datos han cambiado
            function hasDataChanged(currentData) {
                if (!previousData) return true; // Siempre actualizar si no hay datos anteriores
    
                // Comparar datos específicos (aquí estamos comparando las propiedades relevantes)
                return JSON.stringify(currentData) !== JSON.stringify(previousData);
            }
    
            // Función para actualizar los gráficos
            function updateCharts(data) {
                const { estadoData, alcanceData, accessPermitidos, accessDenegados, accessByDay } = data;
    
                // Destruir gráficos anteriores si existen
                if (window.mensajesEnviadosChart && typeof window.mensajesEnviadosChart.destroy === 'function') {
                    window.mensajesEnviadosChart.destroy();
                }
                if (window.analisisCostosChart && typeof window.analisisCostosChart.destroy === 'function') {
                    window.analisisCostosChart.destroy();
                }
    
                // Crear gráfico de mensajes enviados
                const ctx1 = document.getElementById("mensajesEnviadosChart").getContext("2d");
                window.mensajesEnviadosChart = new Chart(ctx1, {
                    type: "line",
                    data: {
                        labels: estadoData.labels,
                        datasets: [{
                            label: "N° Accesos",
                            data: estadoData.data,
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
                        labels: alcanceData.labels,
                        datasets: [{
                            label: "N° Accesos",
                            data: alcanceData.data,
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1,
                            borderRadius: 5,
                        }],
                    },
                    options: getChartOptions(),
                });
    
                // Actualizar valores de total de mensajes y costo total
                $("#accessPermitidos").text("S/ " + accessPermitidos);
                $("#accessDenegados").text(accessDenegados);
                $("#accessByDay").text("S/ " + accessByDay);
            }
    
            // Opciones comunes para los gráficos
            function getChartOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(200, 200, 200, 0.3)",
                            },
                        },
                        x: {
                            grid: {
                                color: "rgba(200, 200, 200, 0.3)",
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: "black",
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toLocaleString();
                                }
                            }
                        },
                    },
                };
            }
    
            // Evento para el botón "Filtrar"
            $("#filtrar").on("click", function() {
                // Obtener las fechas desde los campos de entrada
                startDate = $("#fechaInicio").val();
                endDate = $("#fechaFin").val();
    
                // Verificar que las fechas sean válidas
                if (startDate && endDate) {
                    // Actualizar los datos con las nuevas fechas
                    fetchDataAndUpdateCharts(startDate, endDate);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Por favor, selecciona un rango de fechas válido.',
                    });
                }
            });
    
            // Actualizar datos cada 5 segundos con las fechas actuales
            setInterval(() => {
                if (startDate && endDate) {
                    fetchDataAndUpdateCharts(startDate, endDate);
                }
            }, 5000); // 5000 milisegundos = 5 segundos
        });
    </script>
    
    
    
    




</body>

</html>
