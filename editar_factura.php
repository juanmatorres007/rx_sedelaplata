<?php
include "conexion.php";

if (isset($_POST['codigo_factura'])) {
    $codigo_factura = $_POST['codigo_factura'];

    // Consulta para obtener los datos actuales de la factura
    $sql = "SELECT f.id_factura, f.codigo_factura, f.nombre_archivo, f.id_procedimiento, 
                   f.id_entidad, f.nombre_paciente, f.id_paciente, f.sexo, f.cantidad, 
                   f.valor_unitario, f.descuento, f.fecha_procedimiento 
            FROM Factura f
            WHERE f.codigo_factura = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_factura);
    $stmt->execute();
    $result = $stmt->get_result();
    $factura = $result->fetch_assoc();

    // Obtener opciones dinámicas de procedimientos y entidades
    $procedimientos = $conn->query("SELECT id_procedimiento, nombre_procedimiento FROM Procedimientos");
    $entidades = $conn->query("SELECT id_entidad, nombre_entidad FROM Entidades");

    // Opciones para género
    $generos = ["M" => "Masculino", "F" => "Femenino"];

    if ($factura) {
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Editar Factura</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        </head>

        <body>
            <div class="container mt-4">
                <h2>Editar Factura</h2>
                <form action="guardar_edicion_factura.php" method="POST">
                    <input type="hidden" name="id_factura" value="<?php echo $factura['id_factura']; ?>">

                    <!-- Nombre del Archivo -->
                    <div style="display: flex;">
                        <div class="form-group">
                            <label for="nombre_archivo">codigo de factura</label>
                            <input type="text" name="nombre_archivo" class="form-control" value="<?php echo htmlspecialchars($factura['codigo_factura']); ?>" required style="width: 100px">
                        </div>

                        <!-- Procedimiento (Select) -->
                        <div class="form-group">
                            <label for="id_procedimiento">Procedimiento</label>
                            <select name="id_procedimiento" class="form-control" required style="width: 600px">
                                <?php while ($proc = $procedimientos->fetch_assoc()) { ?>
                                    <option value="<?php echo $proc['id_procedimiento']; ?>"
                                        <?php echo ($factura['id_procedimiento'] == $proc['id_procedimiento']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($proc['nombre_procedimiento']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Entidad (Select) -->
                    <div class="form-group">
                        <label for="id_entidad">Entidad</label>
                        <select name="id_entidad" class="form-control" required style="width: 200px">
                            <?php while ($entidad = $entidades->fetch_assoc()) { ?>
                                <option value="<?php echo $entidad['id_entidad']; ?>"
                                    <?php echo ($factura['id_entidad'] == $entidad['id_entidad']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($entidad['nombre_entidad']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Nombre del Paciente -->
                    <div class="form-group">
                        <label for="nombre_paciente">Nombre del Paciente</label>
                        <input type="text" name="nombre_paciente" class="form-control" value="<?php echo htmlspecialchars($factura['nombre_paciente']); ?>" required style="width: 600px">
                    </div>

                    <!-- Documento del Paciente -->
                    <div class="form-group">
                        <label for="id_paciente">Documento del Paciente</label>
                        <input type="text" name="id_paciente" class="form-control" value="<?php echo htmlspecialchars($factura['id_paciente']); ?>" required style="width: 200px">
                    </div>

                    <!-- Género (Select) -->
                    <div class="form-group">
                        <label for="sexo">Género</label>
                        <select name="sexo" class="form-control" required style="width: 150px">
                            <?php foreach ($generos as $key => $value) { ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo ($factura['sexo'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" value="<?php echo $factura['cantidad']; ?>" required style="width: 100px">
                    </div>

                    <!-- Valor Unitario -->
                    <div class="form-group">
                        <label for="valor_unitario">Valor Unitario</label>
                        <input type="number" step="0.01" name="valor_unitario" class="form-control" value="<?php echo $factura['valor_unitario']; ?>" required style="width: 150px">
                    </div>

                    <!-- Descuento -->
                    <div class="form-group">
                        <label for="descuento">Descuento</label>
                        <input type="number" step="0.01" name="descuento" class="form-control" value="<?php echo $factura['descuento']; ?>" style="width: 100px">
                    </div>

                    <!-- Fecha del Procedimiento -->
                    <div class="form-group">
                        <label for="fecha_procedimiento">Fecha del Procedimiento</label>
                        <input type="date" name="fecha_procedimiento" class="form-control" value="<?php echo $factura['fecha_procedimiento']; ?>" required style="width: 150px">
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="listado_facturas.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </body>

        </html>
<?php
    } else {
        echo "<div class='alert alert-danger'>Factura no encontrada</div>";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "<div class='alert alert-danger'>Código de factura no proporcionado</div>";
}
?>