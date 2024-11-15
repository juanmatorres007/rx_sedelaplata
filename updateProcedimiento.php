<?php
include "conexion.php";

if (isset($_POST['codigo_procedimiento'], $_POST['precio_hospitalario'], $_POST['precio_ambulatorio'])) {
    $codigo_procedimiento = $_POST['codigo_procedimiento'];
    $precio_hospitalario = floatval($_POST['precio_hospitalario']);
    $precio_ambulatorio = floatval($_POST['precio_ambulatorio']);

    $sql = "UPDATE Precios SET precio_hospitalario = ?, precio_ambulatorio = ? WHERE codigo_procedimiento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dds", $precio_hospitalario, $precio_ambulatorio, $codigo_procedimiento);

    if ($stmt->execute()) {
        echo "Precios actualizados correctamente.";
    } else {
        echo "Error al actualizar los precios.";
    }

    $stmt->close();
} else {
    echo "Datos incompletos.";
}

$conn->close();
?>
