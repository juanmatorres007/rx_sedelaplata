<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Precios por Procedimiento y EPS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin: 15px 0 5px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Registrar Precios de Procedimientos por EPS</h1>

    <form action="procesar_precio.php" method="POST">
        <label for="procedimiento">Seleccione Procedimiento:</label>
        <select id="procedimiento" name="procedimiento" required>
            <option value="">Seleccione un procedimiento</option>
            <?php
            // Conectar a la base de datos para obtener la lista de procedimientos
            include "conexion.php";

            // Consulta para obtener los procedimientos
            $sql = "SELECT codigo_procedimiento, nombre_procedimiento FROM Procedimientos";
            $result = $conn->query($sql);

            // Mostrar los procedimientos en el formulario
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['codigo_procedimiento'] . "'>" . $row['nombre_procedimiento'] . "</option>";
                }
            } else {
                echo "<option value=''>No hay procedimientos registrados</option>";
            }

            $conn->close();
            ?>
        </select>

        <label for="eps">Seleccione EPS:</label>
        <select id="eps" name="eps" required>
            <option value="">Seleccione una EPS</option>
            <?php
           
            include "conexion.php";

            $sql = "SELECT id_entidad, nombre_entidad FROM Entidades WHERE tipo_entidad = 'EPS'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id_entidad'] . "'>" . $row['nombre_entidad'] . "</option>";
                }
            } else {
                echo "<option value=''>No hay EPS registradas</option>";
            }

            $conn->close();
            ?>
        </select>

        <label for="precio_ambulatorio">Precio Ambulatorio:</label>
        <input type="text" id="precio_ambulatorio" name="precio_ambulatorio" required>

        <label for="precio_hospitalario">Precio Hospitalario:</label>
        <input type="text" id="precio_hospitalario" name="precio_hospitalario" required>

        <input type="submit" value="Registrar Precio">
    </form>

</body>
</html>
