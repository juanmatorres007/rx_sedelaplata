<?php
include "conexion.php";

if (isset($_POST['eps']) && isset($_POST['tipo_procedimiento'])) {
    $id_eps = intval($_POST['eps']);
    $tipo_procedimiento = $_POST['tipo_procedimiento'];

    // Construir consulta base
    $sql = "SELECT p.codigo_procedimiento, p.nombre_procedimiento, p.marca, pr.precio_hospitalario, pr.precio_ambulatorio
            FROM Procedimientos p
            JOIN Precios pr ON p.id_procedimiento = pr.id_procedimiento
            WHERE pr.id_entidad = ?";

    // Ajustar consulta según el tipo de procedimiento seleccionado
    if ($tipo_procedimiento === "contrastado") {
        $sql .= " AND p.es_contraste = 1";
    } elseif ($tipo_procedimiento === "sin_contraste") {
        $sql .= " AND p.es_contraste = 0";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_eps);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table id='procedimientosTable' class='table table-striped table-bordered'>
                <thead>
                    <tr>
                        <th>Código CUPS</th>
                        <th>Nombre del Procedimiento</th>
                        <th>Marca</th>
                        <th>Precio Hospitalario</th>
                        <th>Precio Ambulatorio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <td>" . htmlspecialchars($row['codigo_procedimiento']) . "</td>
            <td>" . htmlspecialchars($row['nombre_procedimiento']) . "</td>
            <td>" . htmlspecialchars($row['marca']) . "</td>
            <td>$" . number_format($row['precio_hospitalario'],0, '.', '.'). "</td>
            <td>$" . number_format($row['precio_ambulatorio'],0, '.', '.'). "</td>
            <td><button class='btn btn-primary btn-sm edit-btn' data-id='{$row['codigo_procedimiento']}' data-hospitalario='{$row['precio_hospitalario']}' data-ambulatorio='{$row['precio_ambulatorio']}'>Editar</button></td>
          </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No se encontraron procedimientos para esta EPS y tipo de procedimiento.</p>";
    }

    $stmt->close();
}
$conn->close();
