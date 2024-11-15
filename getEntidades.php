<?php
include "conexion.php";

if (isset($_POST['tipo_entidad'])) {
    $tipo_entidad = $_POST['tipo_entidad'];

    $sql = "SELECT id_entidad, nombre_entidad FROM Entidades WHERE tipo_entidad = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tipo_entidad);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<option value=''>Seleccionar Entidad</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['id_entidad']) . "'>" . htmlspecialchars($row['nombre_entidad']) . "</option>";
        }
    } else {
        echo "<option value=''>No hay entidades disponibles</option>";
    }
    $stmt->close();
}
$conn->close();
