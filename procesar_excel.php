<?php
require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['archivo_excel'])) {
    $file = $_FILES['archivo_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $nombreArchivo = trim($worksheet->getCell('A' . $row->getRowIndex())->getValue());
            $codigoFactura = trim($worksheet->getCell('B' . $row->getRowIndex())->getValue());
            $nombreEntidad = trim($worksheet->getCell('C' . $row->getRowIndex())->getValue());
            $idPaciente = trim($worksheet->getCell('E' . $row->getRowIndex())->getValue());
            $nombrePaciente = trim($worksheet->getCell('F' . $row->getRowIndex())->getValue());
            $sexo = strtoupper(trim($worksheet->getCell('H' . $row->getRowIndex())->getValue()));
            $codigoProcedimiento = trim($worksheet->getCell('K' . $row->getRowIndex())->getFormattedValue());
            $cantidad = (int)$worksheet->getCell('L' . $row->getRowIndex())->getValue();
            $valorUnitario = floatval(str_replace(['$', ','], '', $worksheet->getCell('M' . $row->getRowIndex())->getValue()));
            $descuento = floatval(str_replace(['$', ','], '', $worksheet->getCell('N' . $row->getRowIndex())->getValue()));
            $fechaProcedimiento = $worksheet->getCell('P' . $row->getRowIndex())->getFormattedValue();
            $fechaNacimiento = $worksheet->getCell('S' . $row->getRowIndex())->getFormattedValue();

            if (empty($nombreEntidad) || empty($codigoProcedimiento) || empty($codigoFactura)) {
                echo "Fila incompleta: {$row->getRowIndex()} <br>";
                continue;
            }

            // Buscar el id_entidad
            $stmt = $conn->prepare("SELECT id_entidad FROM Entidades WHERE nombre_entidad = ?");
            $stmt->bind_param("s", $nombreEntidad);
            $stmt->execute();
            $result = $stmt->get_result();
            $rowEntidad = $result->fetch_assoc();

            if (!$rowEntidad) {
                $stmt = $conn->prepare("INSERT INTO Entidades (nombre_entidad) VALUES (?)");
                $stmt->bind_param("s", $nombreEntidad);
                $stmt->execute();
                $idEntidad = $conn->insert_id;
            } else {
                $idEntidad = $rowEntidad['id_entidad'];
            }

            
            $stmt = $conn->prepare("SELECT id_procedimiento FROM Procedimientos WHERE codigo_procedimiento = ?");
            $stmt->bind_param("s", $codigoProcedimiento);
            $stmt->execute();
            $result = $stmt->get_result();
            $rowProcedimiento = $result->fetch_assoc();

            if ($rowProcedimiento) {
                $idProcedimiento = $rowProcedimiento['id_procedimiento'];

               
                $stmt = $conn->prepare(
                    "SELECT 1 FROM Factura 
                    WHERE codigo_factura = ? AND id_procedimiento = ? AND id_entidad = ? 
                    AND id_paciente = ? AND fecha_procedimiento = ? AND valor_unitario = ?"
                );
                $stmt->bind_param("ssiisd", $codigoFactura, $idProcedimiento, $idEntidad, $idPaciente, $fechaProcedimiento, $valorUnitario);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
              
                    $valorDescuento = $valorUnitario - ( $descuento * $valorUnitario);

                  
                    $stmt = $conn->prepare(
                        "INSERT INTO Factura 
                        (nombre_archivo, sexo, nombre_paciente, codigo_factura, id_procedimiento, id_entidad, id_paciente, fecha_nacimiento, cantidad, valor_unitario, valor_descuento, descuento, fecha_procedimiento) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->bind_param(
                        "sssiisssiddds",
                        $nombreArchivo, $sexo, $nombrePaciente, $codigoFactura, $idProcedimiento, $idEntidad, $idPaciente,
                        $fechaNacimiento, $cantidad, $valorUnitario, $valorDescuento, $descuento, $fechaProcedimiento
                    );
                    $stmt->execute();
                    $success++;
                } else {
                    $errors++;
                }
            } else {
                $errors++;
            }
        }

        $msg = ($success > 0 ? "Se registraron $success facturas correctamente." : "") . ($errors > 0 ? " $errors registros fueron omitidos por duplicados o errores." : "");
        header("Location: factura.php?msg=" . urlencode($msg));
        exit();
    } catch (Exception $e) {
        header("Location: factura.php?msg=" . urlencode("Error procesando el archivo: " . $e->getMessage()));
        exit();
    }
}
?>
