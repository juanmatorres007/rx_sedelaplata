<?php
include "conexion.php";

if (isset($_POST['codigo_factura'])) {
    $codigo_factura = $_POST['codigo_factura'];

    $sql = "DELETE FROM Factura WHERE codigo_factura = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $codigo_factura);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Mensaje de éxito si se eliminó correctamente
            $msg = "La factura con código $codigo_factura fue eliminada correctamente.";
            header("Location: factura.php?msg=" . urlencode($msg));
        } else {
            // Mensaje de error si no se encontró la factura
            $msg = "Error: No se encontró una factura con el código proporcionado.";
            header("Location: factura.php?msg=" . urlencode($msg));
        }

        $stmt->close();
    } else {
        // Mensaje de error al preparar la consulta
        $msg = "Error al preparar la consulta SQL.";
        header("Location: factura.php?msg=" . urlencode($msg));
        exit();
    }

    $conn->close();
} else {
    // Mensaje de error si no se recibió el código
    $msg = "No se recibió el código de factura.";
    header("Location: factura.php?msg=" . urlencode($msg));
    exit();
}
?>
