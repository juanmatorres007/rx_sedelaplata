<?php
include "conexion.php";

if (isset($_POST['id_factura'])) {
    $id_factura = $_POST['id_factura'];

    $sql = "DELETE FROM Factura WHERE id_factura = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $id_factura);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {

            $msg = "La factura con código fue eliminada correctamente.";
            header("Location: factura.php?msg=" . urlencode($msg));
        } else {

            $msg = "Error: No se encontró una factura con el código proporcionado.";
            header("Location: factura.php?msg=" . urlencode($msg));
        }

        $stmt->close();
    } else {

        $msg = "Error al preparar la consulta SQL.";
        header("Location: factura.php?msg=" . urlencode($msg));
        exit();
    }

    $conn->close();
} else {
  
    $msg = "No se recibió el código de factura.";
    header("Location: factura.php?msg=" . urlencode($msg));
    exit();
}
?>
