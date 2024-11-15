<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Factura Médica</title>
    <style>
        /* Estilos de diseño */
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #3498DB;
            --success-color: #27AE60;
            --error-color: #E74C3C;
            --gray-light: #f4f4f4;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 2rem;
            background-color: var(--gray-light);
            color: var(--primary-color);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--success-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #219a52;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('fecha_procedimiento').valueAsDate = new Date();

            // Función para autocompletar valor unitario según procedimiento y tipo
            document.getElementById('procedimiento').addEventListener('change', actualizarValorUnitario);
            document.getElementById('tipo_procedimiento').addEventListener('change', actualizarValorUnitario);

            function actualizarValorUnitario() {
                const codigoProcedimiento = document.getElementById('procedimiento').value;
                const tipoProcedimiento = document.getElementById('tipo_procedimiento').value;

                if (codigoProcedimiento && tipoProcedimiento) {
                    fetch('getProcedimientoPrecio.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `codigo_procedimiento=${codigoProcedimiento}&tipo_procedimiento=${tipo_procedimiento}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valor_unitario) {
                            document.getElementById("valor_unitario").value = data.valor_unitario;
                            calcularDescuento(); // Calcular descuento automáticamente
                        } else {
                            alert("Error: No se encontró el procedimiento o tipo seleccionado.");
                        }
                    })
                    .catch(error => console.error("Error al obtener datos:", error));
                }
            }

            function calcularDescuento() {
                const valorUnitario = parseFloat(document.getElementById("valor_unitario").value) || 0;
                const cantidad = parseInt(document.getElementById("cantidad").value) || 1;
                const descuentoPorcentaje = 0.15;
                
                const subtotal = valorUnitario * cantidad;
                const descuento = subtotal * descuentoPorcentaje;
                const valorConDescuento = subtotal - descuento;

                document.getElementById("descuento").value = descuento.toFixed(2);
                document.getElementById("valor_con_descuento").value = valorConDescuento.toFixed(2);
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Registro de Factura Médica</h1>
        <form id="facturaForm" method="POST" action="guardarFactura.php">
            <?php
            include "conexion.php";

            // Consulta de Procedimientos
            $sql_procedimientos = "SELECT codigo_procedimiento, nombre_procedimiento, marca, es_contraste 
                                 FROM Procedimientos 
                                 ORDER BY nombre_procedimiento ASC";
            $result_procedimientos = $conn->query($sql_procedimientos);

            // Consulta de Entidadess
            $sql_entidades = "SELECT id_entidad, nombre_entidad, tipo_entidad 
                            FROM Entidades 
                            ORDER BY nombre_entidad ASC";
            $result_entidades = $conn->query($sql_entidades);
            ?>

            <div class="form-container">
                <div class="form-group">
                    <label for="procedimiento">Procedimiento:</label>
                    <select name="codigo_procedimiento" id="procedimiento" required>
                        <option value="">Seleccione un procedimiento</option>
                        <?php
                        while ($row = $result_procedimientos->fetch_assoc()) {
                            $contraste = $row["es_contraste"] ? " (Contraste)" : "";
                            echo "<option value='" . htmlspecialchars($row["codigo_procedimiento"]) . "'>" 
                                 . htmlspecialchars($row["nombre_procedimiento"]) 
                                 . " - " . htmlspecialchars($row["marca"])
                                 . $contraste . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_procedimiento">Tipo de Procedimiento:</label>
                    <select id="tipo_procedimiento" name="tipo_procedimiento" required>
                        <option value="hospitalario">Hospitalario</option>
                        <option value="ambulatorio">Ambulatorio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="valor_unitario">Valor Unitario:</label>
                    <input type="number" step="0.01" name="valor_unitario" id="valor_unitario" 
                           required onchange="calcularDescuento()">
                </div>

                <div class="form-group">
                    <label for="nombre_paciente">Nombre del Paciente:</label>
                    <input type="text" name="nombre_paciente" id="nombre_paciente" required>
                </div>

                <div class="form-group">
                    <label for="tipo_id">Tipo de Identificación:</label>
                    <select name="tipo_id" id="tipo_id" required>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="TI">Tarjeta de Identidad</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PS">Pasaporte</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_paciente">Número de Identificación:</label>
                    <input type="text" name="id_paciente" id="id_paciente" required>
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select name="sexo" id="sexo" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" 
                           value="1" min="1" required onchange="calcularDescuento()">
                </div>

                <div class="form-group">
                    <label for="entidad">Entidad:</label>
                    <select name="id_entidad" id="entidad" required>
                        <option value="">Seleccione una entidad</option>
                        <?php
                        while ($row = $result_entidades->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row["id_entidad"]) . "'>" 
                                 . htmlspecialchars($row["nombre_entidad"]) 
                                 . " (" . htmlspecialchars($row["tipo_entidad"]) . ")</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_procedimiento">Fecha del Procedimiento:</label>
                    <input type="date" name="fecha_procedimiento" id="fecha_procedimiento" required>
                </div>

                <div class="summary-section">
                    <h3>Resumen de Factura</h3>
                    <div class="summary-details">
                        <p><strong>Procedimiento:</strong> <span id="resumen-procedimiento">-</span></p>
                        <p><strong>Cantidad:</strong> <span id="resumen-cantidad">1</span></p>
                        <p><strong>Valor Unitario:</strong> <span id="resumen-valor-unitario">$0.00</span></p>
                        <p><strong>Descuento (15%):</strong> <span id="resumen-descuento">$0.00</span></p>
                        <p><strong>Total a Pagar:</strong> <span id="resumen-total">$0.00</span></p>
                    </div>
                </div>

                <input type="hidden" id="descuento" name="descuento">
                <input type="hidden" id="valor_con_descuento" name="valor_descuento">

                <button type="submit" class="btn">Registrar Factura</button>
            </div>
        </form>
    </div>
</body>
</html>
