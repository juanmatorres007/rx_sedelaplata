<?php
require 'vendor/autoload.php'; // Asegúrate de tener Composer configurado

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        // Usar solo mes_inicio si mes_fin está vacío
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

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $headers = [
        'Código Factura',
        'Nombre Archivo',
        'Código Procedimiento',
        'Procedimiento',
        'Marca',
        'Entidad',
        'Tipo Entidad',
        'Nombre Paciente',
        'ID Paciente',
        'Sexo',
        'Cantidad',
        'Valor Unitario',
        'Descuento',
        'Valor con Descuento',
        'Fecha del Procedimiento'
    ];
    $sheet->fromArray($headers, NULL, 'A1');

    // Filas de datos
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray(array_values($row), NULL, 'A' . $rowIndex);
        $rowIndex++;
    }

    $stmt->close();
    $conn->close();

    // Descargar el archivo
    $filename = "factura_" . date('Y-m-d_H-i-s') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
