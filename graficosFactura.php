<?php
include 'conexion.php';

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


$jsonData = json_encode($data);
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
        <select name="mes" id="mes" class="form-control">
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
        <select name="mes_fin" id="mes_fin" class="form-control" style="width: 6%">
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

        <select name="year" id="year" class="form-control" style="width: 5%"></select>
        <select id="chartType" class="form-control" style="width: 15%;">
            <option value="bar">Barra</option>
            <option value="line">Lineal</option>
            <option value="doughnut">Circular</option>
        </select>
    </div><br>
    <div class="chart-container" style="width: 70%;margin: 0 auto; padding: 20px;">
        <canvas id="proceduresChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('proceduresChart').getContext('2d');
            let chart;

            const createChart = (type, labels, values) => {
                if (chart) {
                    chart.destroy();
                }

                const colors = type === "doughnut" ?
                    createUniqueColors(labels.length) 
                    :
                    "rgba(54, 162, 235, 0.6)"; 

                chart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Procedimientos Realizados',
                            data: values,
                            backgroundColor: colors,
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
                                text: 'Procedimientos Realizados'
                            }
                        },
                    }
                });
            };

  
            const createUniqueColors = (count) => {
                const colors = new Set(); 

                while (colors.size < count) {
                    const color = `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
                    colors.add(color);
                }

                return Array.from(colors); 
            };

            const fetchData = () => {
                const mes = document.getElementById('mes').value;
                const mes_fin = document.getElementById('mes_fin').value || mes; 
                const year = document.getElementById('year').value;
                const chartType = document.getElementById('chartType').value;

                fetch(`getProceduresData.php?mes=${mes}&mes_fin=${mes_fin}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        const labels = data.map(item => item.nombre_procedimiento);
                        const values = data.map(item => item.total);
                        createChart(chartType, labels, values);
                    })
                    .catch(error => console.error('Error:', error));
            };

            const generateYears = () => {
                const yearSelect = document.getElementById('year');
                const currentYear = new Date().getFullYear();

                for (let year = currentYear; year >= 2020; year--) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearSelect.appendChild(option);
                }
                // Seleccionar el año actual
                yearSelect.value = currentYear;
            };


            const initializeFilters = () => {
                const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0');
                document.getElementById('mes').value = currentMonth;
                document.getElementById('mes_fin').value = "";
            };


            document.getElementById('chartType').addEventListener('change', fetchData);
            document.getElementById('mes').addEventListener('change', fetchData);
            document.getElementById('mes_fin').addEventListener('change', fetchData);
            document.getElementById('year').addEventListener('change', fetchData);


            generateYears();
            initializeFilters();
            fetchData();
        });
    </script>

</body>

</html>