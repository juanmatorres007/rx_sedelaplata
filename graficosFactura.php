<?php
// Conexión a la base de datos
include 'conexion.php';

// Consulta 1: Procedimientos realizados
$queryProcedures = "
    SELECT p.nombre_procedimiento, COUNT(f.id_factura) AS total 
    FROM Factura f
    INNER JOIN Procedimientos p ON f.id_procedimiento = p.id_procedimiento
    WHERE MONTH(f.fecha_procedimiento) = MONTH(CURRENT_DATE())
    AND YEAR(f.fecha_procedimiento) = YEAR(CURRENT_DATE())
    GROUP BY p.nombre_procedimiento
";
$resultProcedures = $conn->query($queryProcedures);
$dataProcedures = [];
while ($row = $resultProcedures->fetch_assoc()) {
    $dataProcedures[] = $row;
}
$jsonDataProcedures = json_encode($dataProcedures);

// Consulta 2: Facturación por tipo de entidad
$queryEntities = "
    SELECT e.tipo_entidad, SUM(f.valor_unitario) AS total_facturado, SUM(f.cantidad) AS total_cant
    FROM Factura f
    INNER JOIN Entidades e ON f.id_entidad = e.id_entidad
    WHERE MONTH(f.fecha_procedimiento) = MONTH(CURRENT_DATE())
    AND YEAR(f.fecha_procedimiento) = YEAR(CURRENT_DATE())
    GROUP BY e.tipo_entidad
";
$resultEntities = $conn->query($queryEntities);
$dataEntities = [];
while ($row = $resultEntities->fetch_assoc()) {
    $dataEntities[] = $row;
}
$jsonDataEntities = json_encode($dataEntities);

// Consulta 3: Facturación mensual
$queryFacturacion = "SELECT DATE_FORMAT(fecha_procedimiento, '%Y-%m') AS mes, SUM(valor_descuento) AS total FROM factura GROUP BY mes ORDER BY mes";
$resultFacturacion = $conn->query($queryFacturacion);
$dataFacturacion = [];
while ($row = $resultFacturacion->fetch_assoc()) {
    $dataFacturacion[] = $row;
}

$meses = [];
$totales = [];

foreach ($dataFacturacion as $row) {
    $meses[] = $row['mes'];
    $totales[] = $row['total'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Graficos Factura</title>
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

        .form-group {
            margin-left: 200px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-control {
            width: 15%;
        }
    </style>
</head>

<body>
    <br><button style="border-radius: 5px;">
        <a href="factura.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16" style="color: black;">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg>
        </a>
    </button>

    <!--Grafico 1 prodecimientos realida-->

    <div class="form-group">
        <select name="mes" id="mes" class="form-control" style="width: 10%">
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>
        <select name="mes_fin" id="mes_fin" class="form-control" style="width: 10%">
            <option value="">Mes Fin</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>

        <select name="year" id="year" class="form-control" style="width: 7%"></select>
        <select id="chartType" class="form-control" style="width: 15%;">
            <option value="bar">Barra</option>
            <option value="line">Lineal</option>
            <option value="doughnut">Circular</option>
        </select>

    </div><br>

    <div class="Gráficos">

        <div class="chart-container" style="width: 70%; margin-bottom: 50px; margin-left: 15%;">
            <canvas id="proceduresChart"></canvas>
        </div>

        <div class="chart-container" style="width: 45%; margin-left: 25%;">
            <canvas id="entityTypeChart"></canvas>
        </div>
        <div class="chart-container" style="width: 45%; margin-left: 25%;">
        <canvas id="facturacionChart"></canvas>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctxProcedures = document.getElementById('proceduresChart').getContext('2d');
            const ctxEntities = document.getElementById('entityTypeChart').getContext('2d');
            let proceduresChart, entitiesChart;


            // Función para crear gráficos
            const createChart = (ctx, type, labels, values, title, colors = []) => {
                if (ctx.chart) {
                    ctx.chart.destroy();
                }
                ctx.chart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: values,
                            backgroundColor: colors.length ? colors : 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: title
                            }
                        }
                    }
                });
            };

            // Genera colores únicos
            const createUniqueColors = (count) => {
                const colors = new Set();
                while (colors.size < count) {
                    const color = `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
                    colors.add(color);
                }
                return Array.from(colors);
            };

            //  actualizar el gráfico de 
            const updateProceduresChart = () => {
                const filters = getFilters();
                const chartType = document.getElementById('chartType').value;
                fetch(`getProceduresData.php?mes=${filters.mes}&mes_fin=${filters.mes_fin}&year=${filters.year}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        const labels = data.map(item => item.nombre_procedimiento);
                        const values = data.map(item => item.total);
                        createChart(ctxProcedures, chartType, labels, values, 'Procedimientos Realizados');
                    })
                    .catch(error => console.error('Error:', error));
            };

            // actualizar el gráfico de entidades
            const updateEntitiesChart = () => {
                const filters = getFilters();
                fetch(`getEntitiesData.php?mes=${filters.mes}&mes_fin=${filters.mes_fin}&year=${filters.year}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        const labels = data.map(item => item.tipo_entidad);
                        const values = data.map(item => item.total_facturado);
                        const colors = createUniqueColors(labels.length);
                        createChart(ctxEntities, 'pie', labels, values, 'Facturación por Tipo de Entidad', colors);
                    })
                    .catch(error => console.error('Error:', error));
            };

            //valores de los filtros
            const getFilters = () => ({
                mes: document.getElementById('mes').value,
                mes_fin: document.getElementById('mes_fin').value || document.getElementById('mes').value,
                year: document.getElementById('year').value
            });

            const generateYears = () => {
                const yearSelect = document.getElementById('year');
                const currentYear = new Date().getFullYear();
                for (let year = currentYear; year >= 2020; year--) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearSelect.appendChild(option);
                }
                yearSelect.value = currentYear;
            };

            const initializeFilters = () => {
                const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0');
                document.getElementById('mes').value = currentMonth;
                document.getElementById('mes_fin').value = '';
            };

            const addFilterEvents = () => {

                ['mes', 'mes_fin', 'year'].forEach(filterId => {
                    document.getElementById(filterId).addEventListener('change', () => {
                        updateProceduresChart();
                        updateEntitiesChart();
                    });
                });

                document.getElementById('chartType').addEventListener('change', updateProceduresChart);
            };

            generateYears();
            initializeFilters();
            addFilterEvents();
            updateProceduresChart();
            updateEntitiesChart();
        });

        
   
        const ctx = document.getElementById('facturacionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($meses); ?>,
                datasets: [{
                    label: 'Facturación mensual',
                    data: <?php echo json_encode($totales); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>