<?php
require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['archivo_excel'])) {
    $file = $_FILES['archivo_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ($worksheet->getRowIterator(2) as $row) {
        $nombreArchivo = $worksheet->getCell('A' . $row->getRowIndex())->getValue();
        $codigoFactura = $worksheet->getCell('B' . $row->getRowIndex())->getValue();
        $nombreEntidad = $worksheet->getCell('C' . $row->getRowIndex())->getValue();
        $idPaciente = $worksheet->getCell('E' . $row->getRowIndex())->getValue();
        $nombrePaciente = $worksheet->getCell('F' . $row->getRowIndex())->getValue();
        $sexo = $worksheet->getCell('G' . $row->getRowIndex())->getValue();
        $codigoProcedimiento = $worksheet->getCell('J' . $row->getRowIndex())->getValue();
        $cantidad = (int) $worksheet->getCell('K' . $row->getRowIndex())->getValue();
        $valorUnitario = floatval(str_replace(['$', ','], '', $worksheet->getCell('L' . $row->getRowIndex())->getValue()));
        $descuento = str_replace('%', '', $worksheet->getCell('M' . $row->getRowIndex())->getValue()) / 100;
        $fechaProcedimiento = $worksheet->getCell('N' . $row->getRowIndex())->getFormattedValue();
        $fechaNacimiento = $worksheet->getCell('O' . $row->getRowIndex())->getFormattedValue();

        // Buscar el id_entidad usando nombre_entidad
        $stmt = $conn->prepare("SELECT id_entidad FROM Entidades WHERE nombre_entidad = ?");
        $stmt->bind_param("s", $nombreEntidad);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowEntidad = $result->fetch_assoc();
        
        if ($rowEntidad) {
            $idEntidad = $rowEntidad['id_entidad'];

            // Calcular el valor con descuento
            $valorDescuento = $valorUnitario * (1 - $descuento);

            // Insertar en la tabla Factura
            $stmt = $conn->prepare("INSERT INTO Factura (nombre_archivo, codigo_factura, codigo_procedimiento, id_entidad, nombre_paciente, id_paciente, sexo, fecha_nacimiento, cantidad, valor_unitario, valor_descuento, descuento, fecha_procedimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiisssidds", $nombreArchivo, $codigoFactura, $codigoProcedimiento, $idEntidad, $nombrePaciente, $idPaciente, $sexo, $fechaNacimiento, $cantidad, $valorUnitario, $valorDescuento, $descuento, $fechaProcedimiento);
            $stmt->execute();
        } else {
            echo "Entidad no encontrada: $nombreEntidad <br>";
        }
    }

    echo "Datos subidos exitosamente.";
}
?>
