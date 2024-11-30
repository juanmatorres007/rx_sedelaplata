<?php
include 'conexion.php';

$query = "
    SELECT e.tipo_entidad, SUM(f.valor_unitario) AS total_facturado
    FROM Factura f
    INNER JOIN Entidades e ON f.id_entidad = e.id_entidad
    WHERE MONTH(f.fecha_procedimiento) = MONTH(CURRENT_DATE())
    AND YEAR(f.fecha_procedimiento) = YEAR(CURRENT_DATE())
    GROUP BY e.tipo_entidad
";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$jsonData = json_encode($data);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Gráfico Factura por Tipo de Entidad</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
            
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="chart-container" style="width: 45%; margin: 0 auto;">
        <canvas id="entityTypeChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('entityTypeChart').getContext('2d');
            const data = <?= $jsonData ?>;
            const labels = data.map(item => item.tipo_entidad);
            const values = data.map(item => item.total_facturado);

            const colors = labels.map(() => `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Facturación',
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Facturación por Tipo de Entidad'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
