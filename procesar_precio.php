<?php
// Incluir archivo de conexión a la base de datos
include "conexion.php";

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_procedimiento = $_POST['procedimiento'];
    $id_entidad = $_POST['eps'];
    $precio_ambulatorio = $_POST['precio_ambulatorio'];
    $precio_hospitalario = $_POST['precio_hospitalario'];

    // Validar que los precios sean números válidos
    if (is_numeric($precio_ambulatorio) && is_numeric($precio_hospitalario)) {
        // Consulta para insertar los precios en la tabla Precios
        $sql = "INSERT INTO Precios (codigo_procedimiento, id_entidad, precio_ambulatorio, precio_hospitalario) 
                VALUES (?, ?, ?, ?)";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidd", $codigo_procedimiento, $id_entidad, $precio_ambulatorio, $precio_hospitalario);

        // Ejecutar la consultap
        if ($stmt->execute()) {
            echo "Precios registrados correctamente.";
        } else {
            echo "Error al registrar los precios: " . $stmt->error;
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
        $conn->close();
    } else {
        echo "Por favor ingrese precios válidos.";
    }
}
?>
