<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $codigo_factura = $_POST['codigo_factura'];
    $id_entidad = $_POST['id_entidad'];
    $id_paciente = $_POST['id_paciente'];
    $valor_unitario = $_POST['valor_unitario'];
    $id_procedimiento = $_POST['id_procedimiento'];
    $nombre_paciente = $_POST['nombre_paciente'];
    $sexo = $_POST['sexo'];
    $cantidad = $_POST['cantidad'];
    $descuento = $_POST['descuento'];
    $fecha_procedimiento = $_POST['fecha_procedimiento'];

    // Calcular el valor con descuento
    $valor_descuento = $valor_unitario * ($descuento / 100);
    $valor_con_descuento = $valor_unitario - $valor_descuento;

    // Insertar la factura en la base de datos
    $query = "
        INSERT INTO factura (
            codigo_factura, id_entidad, id_paciente, valor_unitario, id_procedimiento,
            nombre_paciente, sexo, cantidad, descuento, valor_descuento, fecha_procedimiento
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iisdisssdds",
        $codigo_factura, $id_entidad, $id_paciente, $valor_unitario, $id_procedimiento,
        $nombre_paciente, $sexo, $cantidad, $descuento, $valor_descuento, $fecha_procedimiento
    );

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: factura.php?msg=Factura+agregada+correctamente");
    } else {
        // Redirigir con mensaje de error
        header("Location: factura.php?msg=Error+al+agregar+la+factura");
    }

    $stmt->close();
    $conn->close();
} else {
    // Si no es POST, redirigir
    header("Location: factura.php");
}
?>
