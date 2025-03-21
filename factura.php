<?php
include 'conexion.php';?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Consulta de Facturación</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="img/logorayos-removebg-preview.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/facturacion.css">

    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <?php if (isset($_GET['msg'])): ?>
        <div class="success" id="alerta">
            <div class="success__icon">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" d="m12 1c-6.075 0-11 4.925-11 11s4.925 11 11 11 11-4.925 11-11-4.925-11-11-11zm4.768 9.14c.0878-.1004.1546-.21726.1966-.34383.0419-.12657.0581-.26026.0477-.39319-.0105-.13293-.0475-.26242-.1087-.38085-.0613-.11844-.1456-.22342-.2481-.30879-.1024-.08536-.2209-.14938-.3484-.18828s-.2616-.0519-.3942-.03823c-.1327.01366-.2612.05372-.3782.1178-.1169.06409-.2198.15091-.3027.25537l-4.3 5.159-2.225-2.226c-.1886-.1822-.4412-.283-.7034-.2807s-.51301.1075-.69842.2929-.29058.4362-.29285.6984c-.00228.2622.09851.5148.28067.7034l3 3c.0983.0982.2159.1748.3454.2251.1295.0502.2681.0729.4069.0665.1387-.0063.2747-.0414.3991-.1032.1244-.0617.2347-.1487.3236-.2554z" fill="#393a37" fill-rule="evenodd"></path>
                </svg>
            </div>
            <div class="success__title"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <div class="success__close">
                <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
                    <path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path>
                </svg>
            </div>
        </div>
    <?php endif; ?>

    <br><button style="border-radius: 5px;">
        <a href="index.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16" style="color: black;">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg>
        </a>
    </button>

    <h1>Consulta de Facturación</h1>

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
        <select name="mes_fin" id="mes_fin" class="form-control" style="width: 6%">
            <option value="">Mes Fin</option>
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
        <a href="graficosFactura.php"><button class="btn-grafics  btn btn-info"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clipboard2-data-fill" viewBox="0 0 16 16">
                    <path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5" />
                    <path d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585q.084.236.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5q.001-.264.085-.5M10 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1" />
                </svg> Graficos
            </button></a>
        <form action="exportar_excel.php" method="post">
            <input type="hidden" name="tipo_procedimiento" value="" id="export_tipo_procedimiento">
            <input type="hidden" name="mes_inicio" value="" id="export_mes_inicio">
            <input type="hidden" name="mes_fin" value="" id="export_mes_fin">
            <input type="hidden" name="tipo_entidad" value="" id="export_tipo_entidad">
            <input type="hidden" name="year" value="" id="export_year">
            <button type="submit" class="btn btn-outline-success" style="border-color:#4A936B  ">
                Exportar a Excel
            </button>
        </form>
    </div>

    <div class="summary-info mb-4">
        <p><strong>Número de Procedimientos:</strong> <span id="totalProcedimientos">0</span></p>
        <p><strong>Total Facturado Hospital:</strong> <span id="totalFacturado">0</span></p>
        <p><strong>Total Facturado RX:</strong> <span id="totalRx">0</span></p>
    </div>

    <div class="load-file">
        <form action="procesar_excel.php" method="post" enctype="multipart/form-data">
            <input type="file" name="archivo_excel" accept=".xls, .xlsx" required="" id="file-input">
            <button class="container-btn-file" type="submit" name="submit">
                <svg
                    fill="#fff"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 50 50">
                    <path
                        d="M28.8125 .03125L.8125 5.34375C.339844 
    5.433594 0 5.863281 0 6.34375L0 43.65625C0 
    44.136719 .339844 44.566406 .8125 44.65625L28.8125 
    49.96875C28.875 49.980469 28.9375 50 29 50C29.230469 
    50 29.445313 49.929688 29.625 49.78125C29.855469 49.589844 
    30 49.296875 30 49L30 1C30 .703125 29.855469 .410156 29.625 
    .21875C29.394531 .0273438 29.105469 -.0234375 28.8125 .03125ZM32 
    6L32 13L34 13L34 15L32 15L32 20L34 20L34 22L32 22L32 27L34 27L34 
    29L32 29L32 35L34 35L34 37L32 37L32 44L47 44C48.101563 44 49 
    43.101563 49 42L49 8C49 6.898438 48.101563 6 47 6ZM36 13L44 
    13L44 15L36 15ZM6.6875 15.6875L11.8125 15.6875L14.5 21.28125C14.710938 
    21.722656 14.898438 22.265625 15.0625 22.875L15.09375 22.875C15.199219 
    22.511719 15.402344 21.941406 15.6875 21.21875L18.65625 15.6875L23.34375 
    15.6875L17.75 24.9375L23.5 34.375L18.53125 34.375L15.28125 
    28.28125C15.160156 28.054688 15.035156 27.636719 14.90625 
    27.03125L14.875 27.03125C14.8125 27.316406 14.664063 27.761719 
    14.4375 28.34375L11.1875 34.375L6.1875 34.375L12.15625 25.03125ZM36 
    20L44 20L44 22L36 22ZM36 27L44 27L44 29L36 29ZM36 35L44 35L44 37L36 37Z"></path>
                </svg>
                Cargar factura
            </button>
        </form>

        <!-- Botón para abrir el modal -->
        <button id="agregarfatura" class="icon-btn add-btn" data-toggle="modal" data-target="#agregarFacturaModal">
            <div class="add-icon"></div>
            <div class="btn-txt">Agregar</div>
        </button>

    </div>
    <?php
$procedimientos = $conn->query("SELECT id_procedimiento, nombre_procedimiento FROM Procedimientos");
$entidades = $conn->query("SELECT id_entidad, nombre_entidad FROM Entidades");

// Opciones para género
$generos = ["M" => "Masculino", "F" => "Femenino"];
?>

<div class="modal fade" id="agregarFacturaModal" tabindex="-1" role="dialog" aria-labelledby="agregarFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarFacturaModalLabel">Agregar Factura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="agregar_factura.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <!-- Código de factura -->
                            <div class="form-group">
                                <label for="codigo_factura">Código de factura</label>
                                <input type="text" name="codigo_factura" class="form-control" style="width: 100%;" required>
                            </div>

                            <!-- Entidad -->
                            <div class="form-group">
                                <label for="id_entidad">Entidad</label>
                                <select name="id_entidad" class="form-control select" style="width: 100%;" required>
                                    <option value="">Seleccione una entidad</option>
                                    <?php while ($entidad = $entidades->fetch_assoc()): ?>
                                        <option value="<?php echo $entidad['id_entidad']; ?>">
                                            <?php echo htmlspecialchars($entidad['nombre_entidad']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Documento del Paciente -->
                            <div class="form-group">
                                <label for="id_paciente">Documento del Paciente</label>
                                <input type="text" name="id_paciente" class="form-control" style="width: 100%;" required>
                            </div>

                            <!-- Valor Unitario -->
                            <div class="form-group">
                                <label for="valor_unitario">Valor Unitario</label>
                                <input type="number" step="0.01" name="valor_unitario" class="form-control" style="width: 100%;" required>
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <!-- Procedimiento -->
                            <div class="form-group">
                                <label for="id_procedimiento">Procedimiento</label>
                                <select name="id_procedimiento" class="form-control select" style="width: 100%;" required>
                                    <option value="">Seleccione un procedimiento</option>
                                    <?php while ($procedimiento = $procedimientos->fetch_assoc()): ?>
                                        <option value="<?php echo $procedimiento['id_procedimiento']; ?>">
                                            <?php echo htmlspecialchars($procedimiento['nombre_procedimiento']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Nombre del Paciente -->
                            <div class="form-group">
                                <label for="nombre_paciente">Nombre del Paciente</label>
                                <input type="text" name="nombre_paciente" class="form-control" style="width: 100%;" required>
                            </div>

                            <!-- Género -->
                            <div class="form-group">
                                <label for="sexo">Género</label>
                                <select name="sexo" class="form-control select" style="width: 100%;" required>
                                    <option value="">Seleccione el género</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>

                            <!-- Cantidad -->
                            <div class="form-group">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" style="width: 100%;" required>
                            </div>
                        </div>
                    </div>

                    <!-- Fila inferior -->
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Descuento -->
                            <div class="form-group">
                                <label for="descuento">Descuento (%)</label>
                                <input type="number" step="0.01" name="descuento" class="form-control" style="width: 100%;" value="0.15">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Fecha del Procedimiento -->
                            <div class="form-group">
                                <label for="fecha_procedimiento">Fecha del Procedimiento</label>
                                <input type="date" name="fecha_procedimiento" class="form-control" style="width: 100%;" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <th> </th>
                </tr>
            </thead>
            <tbody id="facturaBody">
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().toISOString().slice(5, 7);

            for (let year = currentYear; year >= currentYear - 5; year--) {
                $('#year').append(new Option(year, year));
            }
            $('#year').val(currentYear);
            $('#mes').val(currentMonth);
            const table = $('#facturaTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json",
                },
            });

            function loadFacturaData(tipoProcedimiento, tipoEntidad, mesInicio, mesFin, year) {
                $.ajax({
                    url: 'consultaFactura.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tipo_procedimiento: tipoProcedimiento,
                        tipo_entidad: tipoEntidad,
                        mes_inicio: mesInicio,
                        mes_fin: mesFin,
                        year: year,
                    },
                    success: function(response) {
                        table.clear().draw();
                        table.rows.add($(response.tableRows)).draw();

                        $('#totalProcedimientos').text(response.totalProcedimientos);
                        $('#totalFacturado').text(response.totalFacturado.toLocaleString('es-ES', {
                            minimumFractionDigits: 3,
                            maximumFractionDigits: 3,
                        }));
                        $('#totalRx').text(response.totalRx.toLocaleString('es-ES', {
                            minimumFractionDigits: 3,
                            maximumFractionDigits: 3,
                        }));
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX: ", status, error);
                    }
                });
            }

            loadFacturaData('todos', 'todos', currentMonth, '', currentYear);

            $('#tipo_procedimiento, #tipo_entidad, #mes, #mes_fin, #year').on('change', function() {
                const tipoProcedimiento = $('#tipo_procedimiento').val();
                const tipoEntidad = $('#tipo_entidad').val();
                const mesInicio = $('#mes').val();
                const mesFin = $('#mes_fin').val();
                const year = $('#year').val();

                if (!mesFin) {
                    loadFacturaData(tipoProcedimiento, tipoEntidad, mesInicio, mesInicio, year);
                } else {
                    loadFacturaData(tipoProcedimiento, tipoEntidad, mesInicio, mesFin, year);
                }
            });
        });

        // Oculta la alerta después de 5 segundos y limpia la URL
        setTimeout(() => {
            const alerta = document.getElementById('alerta');
            if (alerta) {
                alerta.style.transition = 'opacity 0.5s';
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 500);
            }

            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState(null, '', url);
        }, 5000);

        $(document).ready(function() {
            // Llenar los valores del formulario de exportación con los filtros actuales
            function sincronizarFiltrosExportacion() {
                $('#export_tipo_procedimiento').val($('#tipo_procedimiento').val());
                $('#export_mes_inicio').val($('#mes').val());
                $('#export_mes_fin').val($('#mes_fin').val());
                $('#export_tipo_entidad').val($('#tipo_entidad').val());
                $('#export_year').val($('#year').val());
            }

            // Sincronizar cada vez que cambia un filtro
            $('#tipo_procedimiento, #tipo_entidad, #mes, #mes_fin, #year').on('change', function() {
                sincronizarFiltrosExportacion();
            });

            // Inicializar con valores actuales
            sincronizarFiltrosExportacion();
        });

        function confirmDelete() {
            return confirm('¿Estás seguro de que quieres eliminar esta factura?');
        }
    </script>
</body>

</html>