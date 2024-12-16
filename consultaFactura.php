
<?php
include "conexion.php";

if (isset($_POST['tipo_procedimiento']) && isset($_POST['mes_inicio']) && isset($_POST['tipo_entidad'])) {
    $tipo_procedimiento = $_POST['tipo_procedimiento'];
    $mes_inicio = $_POST['mes_inicio'];
    $mes_fin = $_POST['mes_fin'] ?? null;
    $tipo_entidad = $_POST['tipo_entidad'];
    $year = $_POST['year'] ?? 'todos';

    $sql = "SELECT f.codigo_factura, f.nombre_archivo, p.codigo_procedimiento, p.nombre_procedimiento, p.marca,
                   e.nombre_entidad, e.tipo_entidad, f.nombre_paciente, f.id_paciente, f.sexo, f.cantidad,
                   f.valor_unitario, f.descuento, f.valor_descuento, f.fecha_procedimiento
            FROM Factura f
            JOIN Procedimientos p ON f.id_procedimiento = p.id_procedimiento
            JOIN Entidades e ON f.id_entidad = e.id_entidad
            WHERE 1=1";

    $paramTypes = "";
    $params = [];

    if ($tipo_procedimiento === "contrastado") {
        $sql .= " AND p.es_contraste = 1";
    } elseif ($tipo_procedimiento === "sin_contraste") {
        $sql .= " AND p.es_contraste = 0";
    }

    if ($year !== 'todos') {
        $sql .= " AND YEAR(f.fecha_procedimiento) = ?";
        $paramTypes .= "i";
        $params[] = $year;
    }


    if (!empty($mes_fin)) {

        $sql .= " AND MONTH(f.fecha_procedimiento) BETWEEN ? AND ?";
        $paramTypes .= "ii";
        $params[] = $mes_inicio;
        $params[] = $mes_fin;
    } else {
        // Solo mes_inicio
        $sql .= " AND MONTH(f.fecha_procedimiento) = ?";
        $paramTypes .= "i";
        $params[] = $mes_inicio;
    }

    if ($tipo_entidad !== "todos") {
        $sql .= " AND e.tipo_entidad = ?";
        $paramTypes .= "s";
        $params[] = $tipo_entidad;
    }

    $stmt = $conn->prepare($sql);
    if (!empty($paramTypes)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $totalRx = 0;
    $totalProcedimientos = 0;
    $totalFacturado = 0;
    $tableRows = "";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valor_unitario_con_descuento = $row['valor_unitario'];
            if ($row['descuento'] > 0) {
                $valor_unitario_con_descuento = $row['valor_unitario'] - $row['descuento'];
            }

            $totalRx += $row['valor_descuento'];
            $totalProcedimientos += $row['cantidad'];
            $totalFacturado += $row['valor_unitario'];
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
            <td>$" . number_format($row['valor_unitario'], 0, '.', '.') . "</td>
            <td>{$row['descuento']}</td>
            <td>$" . number_format($row['valor_descuento'], 0, '.', '.') . "</td>
            <td>{$row['fecha_procedimiento']}</td>
            <td>
                <form method='POST' action='eliminar_factura.php' style='display:inline;'>
                    <input type='hidden' name='codigo_factura' value='{$row['codigo_factura']}'>
                    <button type='submit' style='background: none; border: none; cursor: pointer;' aria-label='Eliminar'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                            <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                        </svg>
                    </button>
                </form>
               <form method='POST' action='editar_factura.php' style='display:inline;'>
    <input type='hidden' name='codigo_factura' value='{$row['codigo_factura']}'>
    <button type='submit' style='background: none; border: none; cursor: pointer;' aria-label='editar'>
        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
            <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z'/>
        </svg>
    </button>
</form>
            </td>
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
        "totalRx" => number_format($totalRx, 0, '.', '.'),
    ]);
}
?>
