<?php
include "conexion.php";

if (isset($_POST['id_factura'])) {
    $id_factura = $_POST['id_factura'];
    $codigo_archivo = $_POST['codigo_factura'];
    $id_procedimiento = $_POST['id_procedimiento'];
    $id_entidad = $_POST['id_entidad'];
    $nombre_paciente = $_POST['nombre_paciente'];
    $id_paciente = $_POST['id_paciente'];
    $sexo = $_POST['sexo'];
    $cantidad = $_POST['cantidad'];
    $valor_unitario = $_POST['valor_unitario'];
    $descuento = $_POST['descuento'] ?? 0;
    $fecha_procedimiento = $_POST['fecha_procedimiento'];

    $valorDescuento = $valor_unitario - ($descuento * $valor_unitario);

    $sql = "UPDATE Factura SET 
                codigo_factura = ?, 
                id_procedimiento = ?, 
                id_entidad = ?, 
                nombre_paciente = ?, 
                id_paciente = ?, 
                sexo = ?, 
                cantidad = ?, 
                valor_unitario = ?, 
                descuento = ?, 
                fecha_procedimiento = ?, 
                valor_descuento = ?
            WHERE id_factura = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "siisssiddsii", 
            $codigo_archivo,
            $id_procedimiento,
            $id_entidad,
            $nombre_paciente,
            $id_paciente,
            $sexo,
            $cantidad,
            $valor_unitario,
            $descuento,
            $fecha_procedimiento,
            $valorDescuento,
            $id_factura
        );

        if ($stmt->execute()) {
            $msg = "La factura con código $codigo_archivo fue actualizada correctamente.";
            header("Location: factura.php?msg=" . urlencode($msg));
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar la factura: " . $conn->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error al preparar la consulta SQL: " . $conn->error . "</div>";
    }

    $conn->close();
    exit();
} else {
    echo "<div class='alert alert-danger'>Error: Datos incompletos</div>";
}
?>
