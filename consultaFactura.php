<?php
include "conexion.php";

if (isset($_POST['tipo_procedimiento']) && isset($_POST['mes']) && isset($_POST['tipo_entidad'])) {
    $tipo_procedimiento = $_POST['tipo_procedimiento'];
    $mes = $_POST['mes'];
    $tipo_entidad = $_POST['tipo_entidad'];
    $year = $_POST['year'] ?? 'todos';

    // Construir la consulta base
    $sql = "SELECT f.codigo_factura, f.nombre_archivo, f.codigo_procedimiento, p.nombre_procedimiento, p.marca,
                   e.nombre_entidad, e.tipo_entidad, f.nombre_paciente, f.id_paciente, f.sexo, f.cantidad,
                   f.valor_unitario, f.descuento, f.valor_descuento,
                   f.fecha_procedimiento
            FROM Factura f
            JOIN Procedimientos p ON f.codigo_procedimiento = p.codigo_procedimiento
            JOIN Entidades e ON f.id_entidad = e.id_entidad
            WHERE 1=1";

    // Inicializar variables para parámetros
    $paramTypes = "";
    $params = [];

    // Filtrar por tipo de procedimiento
    if ($tipo_procedimiento === "contrastado") {
        $sql .= " AND p.es_contraste = 1";
    } elseif ($tipo_procedimiento === "sin_contraste") {
        $sql .= " AND p.es_contraste = 0";
    }

    // Filtrar por año
    if ($year !== 'todos') {
        $sql .= " AND YEAR(f.fecha_procedimiento) = ?";
        $paramTypes .= "i";
        $params[] = $year;
    }

    // Filtrar por mes
    if ($mes !== "todos") {
        $sql .= " AND MONTH(f.fecha_procedimiento) = ?";
        $paramTypes .= "i";
        $params[] = $mes;
    }

    // Filtrar por tipo de entidad
    if ($tipo_entidad !== "todos") {
        $sql .= " AND e.tipo_entidad = ?";
        $paramTypes .= "s";
        $params[] = $tipo_entidad;
    }

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    if (!empty($paramTypes)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Variables para acumular totales
    $totalRx = 0;
    $totalProcedimientos = 0;
    $totalFacturado = 0;
    $tableRows = "";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Aplicar el descuento
            $valor_unitario_con_descuento = $row['valor_unitario'];
            
            // Ajusta el cálculo según el tipo de descuento
            if ($row['descuento'] > 0) {
                // Si `descuento` es un valor absoluto
                $valor_unitario_con_descuento = $row['valor_unitario'] - $row['descuento'];

            }

            // Calcular el total para el registro actual y sumar a los totales
            $totalRx += $row['valor_descuento'];
            $totalProcedimientos += $row['cantidad'];
            $totalFacturado  += $row['valor_unitario'] ;



            $tableRows .= "<tr>
                <td>{$row['codigo_factura']}</td>
                <td>{$row['nombre_archivo']}</td>
                <td>{$row['codigo_procedimiento']}</td>
                <td>{$row['nombre_procedimiento']}</td>
                <td>{$row['marca']}</td>
                <td>{$row['nombre_entidad']}</td>
                <td>{$row['tipo_entidad']}</td>
                <td>{$row['nombre_paciente']}</td>
                <td>{$row['id_paciente']}</td>
                <td>{$row['sexo']}</td>
                <td>{$row['cantidad']}</td>
                <td>$" . number_format($row['valor_unitario'],0, '.', '.'). "</td>
                <td>{$row['descuento']}</td>
                <td>$" . number_format($row['valor_descuento'],0, '.', '.'). "</td>
                <td>{$row['fecha_procedimiento']}</td>
            </tr>";
        }
    } else {
        $tableRows = "<tr><td colspan='15'>No se encontraron registros para los filtros seleccionados.</td></tr>";
    }

    $stmt->close();
    $conn->close();


    echo json_encode([
        "tableRows" => $tableRows,
        "totalProcedimientos" => $totalProcedimientos,
        "totalFacturado" => number_format($totalFacturado, 0, '.', '.'),
        "totalRx" => number_format($totalRx, 0, '.', '.')
    ]);
    
}
?>

