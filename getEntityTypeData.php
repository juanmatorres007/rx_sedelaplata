<?php
include 'conexion.php';

$mes = $_GET['mes'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$query = "
    SELECT e.tipo_entidad, SUM(f.valor_unitario) AS total_facturado
    FROM Factura f
    INNER JOIN Entidades e ON f.id_entidad = e.id_entidad
    WHERE YEAR(f.fecha_procedimiento) = ?
    AND MONTH(f.fecha_procedimiento) = ?
    GROUP BY e.tipo_entidad
";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $year, $mes);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);