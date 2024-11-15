<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Filtrar Procedimientos y Precios por Entidad</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

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

        .table-container {
            margin-top: 20px;
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
    <div class="container">
        <h1>Filtrar Procedimientos y Precios por Entidad</h1>

        <!-- Selección de Tipo de Entidad -->
        <div class="form-group">
            <label for="tipo_entidad">Tipo de Entidad:</label>
            <select name="tipo_entidad" id="tipo_entidad" class="form-control" required>
                <option value="">Seleccionar Tipo de Entidad</option>
                <option value="EPS">EPS</option>
                <option value="SOAT">SOAT</option>
                <option value="Particular">Particular</option>
            </select>
        </div>

        <!-- Selección de Entidad -->
        <div class="form-group">
            <label for="eps">Selecciona la Entidad:</label>
            <select name="eps" id="eps" class="form-control" required>
                <option value="">Seleccionar Entidad</option>
                <!-- This will be dynamically populated based on the selected entity type -->
            </select>
        </div>

        <!-- Selección de Tipo de Procedimiento -->
        <div class="form-group">
            <label for="tipo_procedimiento">Tipo de Procedimiento:</label>
            <select name="tipo_procedimiento" id="tipo_procedimiento" class="form-control">
                <option value="todos">Todos</option>
                <option value="contrastado">Contrastado</option>
                <option value="sin_contraste">Sin Contraste</option>
            </select>
        </div>

        <!-- Contenedor para la tabla de procedimientos -->
        <div class="table-container mt-4" id="procedimientos-container">
            <p>Selecciona un tipo de entidad, una entidad y un tipo de procedimiento para ver los resultados.</p>
        </div>
    </div>

    <!-- Modal para editar precios -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-labelledby="editPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceModalLabel">Editar Precios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPriceForm">
                        <input type="hidden" id="editCodigoProcedimiento" name="codigo_procedimiento">
                        <div class="form-group">
                            <label for="precioHospitalario">Precio Hospitalario</label>
                            <input type="number" step="0.01" id="precioHospitalario" name="precio_hospitalario" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="precioAmbulatorio">Precio Ambulatorio</label>
                            <input type="number" step="0.01" id="precioAmbulatorio" name="precio_ambulatorio" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Incluir DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <!-- Incluir Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Load entities based on selected type of entity
            $('#tipo_entidad').on('change', function() {
                const tipoEntidad = $(this).val();

                if (tipoEntidad) {
                    // Fetch entities based on the selected type
                    $.ajax({
                        url: 'getEntidades.php',
                        type: 'POST',
                        data: {
                            tipo_entidad: tipoEntidad
                        },
                        success: function(response) {
                            $('#eps').html(response);
                        }
                    });
                } else {
                    $('#eps').html('<option value="">Seleccionar Entidad</option>');
                }
            });

            // Fetch procedures based on selected EPS and procedure type
            $('#eps, #tipo_procedimiento').on('change', function() {
                const epsId = $('#eps').val();
                const tipoProcedimiento = $('#tipo_procedimiento').val();

                if (epsId) {
                    // AJAX request to get filtered procedures
                    $.ajax({
                        url: 'consultaProcedimiento.php',
                        type: 'POST',
                        data: {
                            eps: epsId,
                            tipo_procedimiento: tipoProcedimiento
                        },
                        success: function(response) {
                            $('#procedimientos-container').html(response);

                            // Initialize DataTables if new table is loaded
                            if ($('#procedimientosTable').length) {
                                $('#procedimientosTable').DataTable({
                                    "paging": true,
                                    "searching": true,
                                    "ordering": true,
                                    "info": true,
                                    "language": {
                                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                                    }
                                });
                            }
                        }
                    });
                } else {
                    $('#procedimientos-container').html('<p>Selecciona una EPS y un tipo de procedimiento para ver los resultados.</p>');
                }

                
            });
        });

        $(document).on('click', '.edit-btn', function() {
                const codigo = $(this).data('id');
                const precioHospitalario = $(this).data('hospitalario');
                const precioAmbulatorio = $(this).data('ambulatorio');

                $('#editCodigoProcedimiento').val(codigo);
                $('#precioHospitalario').val(precioHospitalario);
                $('#precioAmbulatorio').val(precioAmbulatorio);

                $('#editPriceModal').modal('show');
            });

            // Enviar formulario para actualizar precios
            $('#editPriceForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: 'updateProcedimiento.php', // Archivo que procesará la actualización en la base de datos
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editPriceModal').modal('hide');
                        $('#eps').trigger('change'); // Refresca la tabla después de la actualización
                    },
                    error: function() {
                        alert('Error al actualizar los precios');
                    }
                });
            });
    </script>
</body>

</html>