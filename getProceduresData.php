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
    SELECT p.nombre_procedimiento, COUNT(f.id_factura) AS total 
    FROM Factura f
    INNER JOIN Procedimientos p ON f.id_procedimiento = p.id_procedimiento
    WHERE YEAR(f.fecha_procedimiento) = ?
    AND MONTH(f.fecha_procedimiento) BETWEEN ? AND ?
    GROUP BY p.nombre_procedimiento
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

