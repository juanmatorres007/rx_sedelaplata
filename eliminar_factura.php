<?php
include "conexion.php";

if (isset($_POST['id_factura'])) {
    $id_factura = $_POST['id_factura'];

    // Obtener el código de la factura antes de eliminarla
    $query = "SELECT codigo_factura FROM Factura WHERE id_factura = ?";
    $stmt_select = $conn->prepare($query);

    if ($stmt_select) {
        $stmt_select->bind_param("i", $id_factura);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $codigo_factura = $row['codigo_factura'];

            // Proceder a eliminar la factura
            $sql = "DELETE FROM Factura WHERE id_factura = ?";
            $stmt_delete = $conn->prepare($sql);

            if ($stmt_delete) {
                $stmt_delete->bind_param("i", $id_factura);
                $stmt_delete->execute();

                if ($stmt_delete->affected_rows > 0) {
                    $msg = "La factura con código $codigo_factura fue eliminada correctamente.";
                    header("Location: factura.php?msg=" . urlencode($msg));
                } else {
                    $msg = "Error: No se encontró una factura con el código proporcionado.";
                    header("Location: factura.php?msg=" . urlencode($msg));
                }

                $stmt_delete->close();
            } else {
                $msg = "Error al preparar la consulta SQL para eliminar la factura.";
                header("Location: factura.php?msg=" . urlencode($msg));
                exit();
            }
        } else {
            $msg = "No se encontró ninguna factura con el ID proporcionado.";
            header("Location: factura.php?msg=" . urlencode($msg));
        }

        $stmt_select->close();
    } else {
        $msg = "Error al preparar la consulta SQL para obtener el código de la factura.";
        header("Location: factura.php?msg=" . urlencode($msg));
        exit();
    }

    $conn->close();
} else {
    $msg = "No se recibió el ID de la factura.";
    header("Location: factura.php?msg=" . urlencode($msg));
    exit();
}
?>
