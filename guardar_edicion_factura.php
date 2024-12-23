<?php
include "conexion.php";

if (isset($_POST['id_factura'])) {
    $id_factura = $_POST['id_factura'];
    $nombre_archivo = $_POST['nombre_archivo'];
    $id_procedimiento = $_POST['id_procedimiento'];
    $id_entidad = $_POST['id_entidad'];
    $nombre_paciente = $_POST['nombre_paciente'];
    $id_paciente = $_POST['id_paciente'];
    $sexo = $_POST['sexo'];
    $cantidad = $_POST['cantidad'];
    $valor_unitario = $_POST['valor_unitario'];
    $descuento = $_POST['descuento'] ?? 0;
    $fecha_procedimiento = $_POST['fecha_procedimiento'];

    // Consulta SQL para actualizar los datos
    $sql = "UPDATE Factura SET 
                nombre_archivo = ?, 
                id_procedimiento = ?, 
                id_entidad = ?, 
                nombre_paciente = ?, 
                id_paciente = ?, 
                sexo = ?, 
                cantidad = ?, 
                valor_unitario = ?, 
                descuento = ?, 
                fecha_procedimiento = ?
            WHERE id_factura = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisssiddsi", 
        $nombre_archivo, 
        $id_procedimiento, 
        $id_entidad, 
        $nombre_paciente, 
        $id_paciente, 
        $sexo, 
        $cantidad, 
        $valor_unitario, 
        $descuento, 
        $fecha_procedimiento, 
        $id_factura
    );

    // Ejecutar y verificar
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Factura actualizada correctamente</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar la factura: " . $conn->error . "</div>";
    }

    $stmt->close();
    $conn->close();

    // Redirigir a la página de listado
    header("Location: factura.php");
    exit();
} else {
    echo "<div class='alert alert-danger'>Error: Datos incompletos</div>";
}
?>
