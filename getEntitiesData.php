<?php
include 'conexion.php';

$mes = $_GET['mes'] ?? null;
$mes_fin = $_GET['mes_fin'] ?? $mes;
$year = $_GET['year'] ?? null;

if (!$mes || !$year) {
    echo json_encode(['error' => 'Faltan parámetros obligatorios']);
    exit;
}

$query = "
    SELECT e.tipo_entidad, SUM(f.valor_unitario) AS total_facturado
    FROM Factura f
    INNER JOIN Entidades e ON f.id_entidad = e.id_entidad
    WHERE YEAR(f.fecha_procedimiento) = ?
    AND MONTH(f.fecha_procedimiento) BETWEEN ? AND ?
    GROUP BY e.tipo_entidad
";
$stmt = $conn->prepare($query);
$stmt->bind_param('iii', $year, $mes, $mes_fin);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
