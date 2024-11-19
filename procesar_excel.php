<?php
require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['archivo_excel'])) {
    $file = $_FILES['archivo_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ($worksheet->getRowIterator(2) as $row) {
        $nombreArchivo = trim($worksheet->getCell('A' . $row->getRowIndex())->getValue());
        $codigoFactura = trim($worksheet->getCell('B' . $row->getRowIndex())->getValue());
        $nombreEntidad = trim($worksheet->getCell('C' . $row->getRowIndex())->getValue());
        $idPaciente = trim($worksheet->getCell('E' . $row->getRowIndex())->getValue());
        $nombrePaciente = trim($worksheet->getCell('F' . $row->getRowIndex())->getValue());
        $sexo = strtoupper(trim($worksheet->getCell('H' . $row->getRowIndex())->getValue()));

        // Leer el código como texto y asegurarse de conservar caracteres especiales
        $codigoProcedimiento = trim($worksheet->getCell('K' . $row->getRowIndex())->getFormattedValue());
        $codigoProcedimiento = preg_replace('/[^A-Za-z0-9]/', '', $codigoProcedimiento); // Elimina caracteres no válidos

        $cantidad = (int) $worksheet->getCell('L' . $row->getRowIndex())->getValue();
        $valorUnitario = floatval(str_replace(['$', ','], '', $worksheet->getCell('M' . $row->getRowIndex())->getValue()));
        $descuento = floatval(str_replace(['$', ','], '', $worksheet->getCell('N' . $row->getRowIndex())->getValue()));
        $fechaProcedimiento = $worksheet->getCell('P' . $row->getRowIndex())->getFormattedValue();
        $fechaNacimiento = $worksheet->getCell('S' . $row->getRowIndex())->getFormattedValue();

        // Depuración: Verifica si el código de procedimiento se lee correctamente
        error_log("Código Procedimiento leído: " . $codigoProcedimiento);

        // Buscar el id_entidad usando nombre_entidad
        $stmt = $conn->prepare("SELECT id_entidad FROM Entidades WHERE nombre_entidad = ?");
        $stmt->bind_param("s", $nombreEntidad);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowEntidad = $result->fetch_assoc();

        if (!$rowEntidad) {
            // Insertar la nueva entidad si no existe
            $stmt = $conn->prepare("INSERT INTO Entidades (nombre_entidad) VALUES (?)");
            $stmt->bind_param("s", $nombreEntidad);
            $stmt->execute();
            $idEntidad = $conn->insert_id;
        } else {
            $idEntidad = $rowEntidad['id_entidad'];
        }

        // Verificar si el codigo_procedimiento existe en Procedimientos
        $stmt = $conn->prepare("SELECT codigo_procedimiento FROM Procedimientos WHERE codigo_procedimiento = ?");
        $stmt->bind_param("s", $codigoProcedimiento);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowProcedimiento = $result->fetch_assoc();

        if ($rowProcedimiento) {
            // Verificar si la factura ya existe
            $stmt = $conn->prepare(
                "SELECT 1 FROM Factura 
                WHERE codigo_factura = ? AND codigo_procedimiento = ? AND id_entidad = ? AND id_paciente = ? AND fecha_procedimiento = ?"
            );
            $stmt->bind_param("ssiis", $codigoFactura, $codigoProcedimiento, $idEntidad, $idPaciente, $fechaProcedimiento);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                // Calcular el valor con descuento
                $valorDescuento = $valorUnitario * (1 - $descuento);

                // Insertar en la tabla Factura
                $stmt = $conn->prepare(
                    "INSERT INTO Factura 
                    (nombre_archivo, sexo, nombre_paciente, codigo_factura, codigo_procedimiento, id_entidad, id_paciente, fecha_nacimiento, cantidad, valor_unitario, valor_descuento, descuento, fecha_procedimiento) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param(
                    "sssiisssiddds",
                    $nombreArchivo, $sexo, $nombrePaciente, $codigoFactura, $codigoProcedimiento, $idEntidad, $idPaciente,
                    $fechaNacimiento, $cantidad, $valorUnitario, $valorDescuento, $descuento, $fechaProcedimiento
                );
                $stmt->execute();
            } else {
                echo "Factura duplicada omitida: $codigoFactura <br>";
            }
        } else {
            echo "Procedimiento no encontrado: $codigoProcedimiento <br>";
        }
    }

    header("Location: factura.php?success=true");
    exit();
}
?>
