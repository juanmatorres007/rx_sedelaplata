<?php
include 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_procedimiento = $_POST['id_procedimiento'];
    $id_entidad = $_POST['id_entidad'];
    $precio_ambulatorio = $_POST['precio_ambulatorio'];
    $precio_hospitalario = $_POST['precio_hospitalario'];

    // Validar datos
    if (empty($id_procedimiento) || empty($id_entidad) || !is_numeric($precio_ambulatorio) || !is_numeric($precio_hospitalario)) {
        die("Por favor complete todos los campos correctamente.");
    }

    // Insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO precios (id_procedimiento, id_entidad, precio_ambulatorio, precio_hospitalario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iidd", $id_procedimiento, $id_entidad, $precio_ambulatorio, $precio_hospitalario);

    if ($stmt->execute()) {
        echo "Precios guardados exitosamente.";
    } else {
        echo "Error al guardar los datos: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
