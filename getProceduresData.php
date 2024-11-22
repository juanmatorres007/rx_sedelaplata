<?php
include 'conexion.php';

// Obtener la cantidad de procedimientos realizados por tipo en el mes actual
$query = "
    SELECT p.nombre_procedimiento, COUNT(f.id_factura) AS total 
    FROM Factura f
    INNER JOIN Procedimientos p ON f.id_procedimiento = p.id_procedimiento
    WHERE MONTH(f.fecha_procedimiento) = MONTH(CURRENT_DATE())
    AND YEAR(f.fecha_procedimiento) = YEAR(CURRENT_DATE())
    GROUP BY p.nombre_procedimiento
";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
