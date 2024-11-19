<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Consulta de Facturación</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-control {
            width: 15%;
        }
    </style>
</head>

<body>
    <br><button style="border-radius: 5px;">
        <a href="index.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16" style="color: black;">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg>
        </a>
    </button>

    <h1>Consulta de Facturación</h1>

    <!-- Filtro de tipo de procedimiento, tipo de entidad y mes -->
    <div class="form-group">
        <select name="tipo_procedimiento" id="tipo_procedimiento" class="form-control">
            <option value="todos">Todos los Procedimientos</option>
            <option value="contrastado">Contrastado</option>
            <option value="sin_contraste">Sin Contraste</option>
        </select>

        <select name="tipo_entidad" id="tipo_entidad" class="form-control">
            <option value="todos">Todos los Tipos de Entidad</option>
            <option value="EPS">EPS</option>
            <option value="SOAT">SOAT</option>
            <option value="Particular">Particular</option>
        </select>

        <select name="mes" id="mes" class="form-control">
            <option value="todos">Todos los Meses</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>

        <select name="year" id="year" class="form-control" style="width: 5%"></select>
    </div>



    <!-- Información de resumen -->
    <div class="summary-info mb-4">
        <p><strong>Número de Procedimientos:</strong> <span id="totalProcedimientos">0</span></p>
        <p><strong>Total Facturado Hospital:</strong> <span id="totalFacturado">0</span></p>
        <p><strong>Total Facturado RX:</strong> <span id="totalRx">0</span></p>

    </div>

    <form action="procesar_excel.php" method="post" enctype="multipart/form-data">
        <input type="file" name="archivo_excel" accept=".xlsx">
        <button type="submit" name="submit">Cargar y Registrar Facturas</button>
    </form>


    <!-- Contenedor para la tabla -->
    <div class="table-container mt-4">
        <table id="facturaTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Código Factura</th>
                    <th>Nombre Archivo</th>
                    <th>Código Procedimiento</th>
                    <th>Procedimiento</th>
                    <th>Marca</th>
                    <th>Entidad</th>
                    <th>Tipo Entidad</th>
                    <th>Nombre Paciente</th>
                    <th>ID Paciente</th>
                    <th>Sexo</th>
                    <th>Cantidad</th>
                    <th>Valor Unitario</th>
                    <th>Descuento</th>
                    <th>Valor con Descuento</th>
                    <th>Fecha del Procedimiento</th>
                </tr>
            </thead>
            <tbody id="facturaBody">
                <!-- Contenido generado por AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Incluir jQuery y DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>


    <script>
        $(document).ready(function() {
            // Establecer el año actual por defecto en el selector de años
            var currentYear = new Date().getFullYear();

            // Llenar el selector de años con un rango (ejemplo: últimos 10 años hasta el actual)
            for (var year = currentYear; year >= currentYear - 5; year--) {
                $('#year').append(new Option(year, year));
            }
            $('#year').val(currentYear); // Seleccionar el año actual por defecto

            var currentMonth = new Date().toISOString().slice(5, 7);
            $('#mes').val(currentMonth);

            // Initialize DataTable
            var table = $('#facturaTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                }
            });

            function loadFacturaData(tipoProcedimiento, tipoEntidad, mes, year) {
                $.ajax({
                    url: 'consultaFactura.php',
                    type: 'POST',
                    dataType: 'json', // Espera una respuesta en formato JSON
                    data: {
                        tipo_procedimiento: tipoProcedimiento,
                        tipo_entidad: tipoEntidad,
                        mes: mes,
                        year: year // Enviar el filtro de año al servidor
                    },
                    success: function(response) {
                        // Limpiar la tabla antes de agregar las nuevas filas
                        table.clear().draw();

                        // Agregar las filas de la tabla al DataTable
                        table.rows.add($(response.tableRows)).draw();

                        // Actualizar los valores de resumen en la interfaz
                        $('#totalProcedimientos').text(response.totalProcedimientos);
                        $('#totalFacturado').text(response.totalFacturado.toLocaleString('es-ES', {
                            minimumFractionDigits: 3,
                            maximumFractionDigits: 3
                        }));
                        $('#totalRx').text(response.totalRx.toLocaleString('es-ES', {
                            minimumFractionDigits: 3,
                            maximumFractionDigits: 3
                        }));
                    }

                });
            }

            // Cargar datos iniciales para el mes y año actuales
            loadFacturaData('todos', 'todos', currentMonth, currentYear);

            // Actualizar tabla y resumen cuando cambian los filtros
            $('#tipo_procedimiento, #tipo_entidad, #mes, #year').on('change', function() {
                var tipoProcedimiento = $('#tipo_procedimiento').val();
                var tipoEntidad = $('#tipo_entidad').val();
                var mes = $('#mes').val();
                var year = $('#year').val();
                loadFacturaData(tipoProcedimiento, tipoEntidad, mes, year);
            });
        });
    </script>

    </script>

</body>

</html>