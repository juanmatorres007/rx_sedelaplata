<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Precio y Entidad a Procedimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
 
<button id="openModalBtn" class="button">
        <svg viewBox="0 0 448 512" class="bell">
            <path d="M224 0c-17.7 0-32 14.3-32 32V49.9C119.5 61.4 64 124.2 64 200v33.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416H424c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4V200c0-75.8-55.5-138.6-128-150.1V32c0-17.7-14.3-32-32-32zm0 96h8c57.4 0 104 46.6 104 104v33.4c0 47.9 13.9 94.6 39.7 134.6H72.3C98.1 328 112 281.3 112 233.4V200c0-57.4 46.6-104 104-104h8zm64 352H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7s18.7-28.3 18.7-45.3z"></path>
        </svg>
    </button>

    <div id="Modal" class="modal">
        <div class="modal-content-1">
        <form action="guardarPrecio.php" method="POST" class="needs-validation" novalidate>
            <!-- Select Procedimiento -->
            <div class="mb-3">
                <label for="procedimiento" class="form-label">Procedimiento</label>
                <select class="form-select" id="procedimiento" name="id_procedimiento" required>
                    <option value="" disabled selected>Seleccione un procedimiento</option>
                    <!-- PHP para cargar procedimientos -->
                    <?php
                    include 'conexion.php'; 
                    $result = $conn->query("SELECT id_procedimiento, nombre_procedimiento, marca, es_contraste 
                                            FROM procedimientos");
                    while ($row = $result->fetch_assoc()) {
                        $contraste = $row['es_contraste'] ? ' (Con contraste)' : '';
                        echo "<option value='{$row['id_procedimiento']}'>
                                {$row['nombre_procedimiento']} - {$row['marca']}{$contraste}
                              </option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Por favor seleccione un procedimiento.</div>
            </div>

            <!-- Select Entidad -->
            <div class="mb-3">
                <label for="entidad" class="form-label">Entidad</label>
                <select class="form-select" id="entidad" name="id_entidad" required>
                    <option value="" disabled selected>Seleccione una entidad</option>
                    <?php
                    $result = $conn->query("SELECT id_entidad, nombre_entidad, tipo_entidad 
                                            FROM entidades");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_entidad']}'>
                                {$row['nombre_entidad']} ({$row['tipo_entidad']})
                              </option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Por favor seleccione una entidad.</div>
            </div>

            <!-- Precio Ambulatorio -->
            <div class="mb-3">
                <label for="precio_ambulatorio" class="form-label">Precio Ambulatorio</label>
                <input type="number" step="0.001" class="form-control" id="precio_ambulatorio" name="precio_ambulatorio" required>
                <div class="invalid-feedback">Por favor ingrese un precio válido.</div>
            </div>

            <!-- Precio Hospitalario -->
            <div class="mb-3">
                <label for="precio_hospitalario" class="form-label">Precio Hospitalario</label>
                <input type="number" step="0.001" class="form-control" id="precio_hospitalario" name="precio_hospitalario" required>
                <div class="invalid-feedback">Por favor ingrese un precio válido.</div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
        </form> 
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const btnAbrirModal = document.getElementById("openModalBtn");
            const btnCerrarModal = document.getElementById("btn-cerrar-modal");
            const modal = document.getElementById("Modal");

            btnAbrirModal.addEventListener("click", () => {
                modal.style.display = "block";
            });

            btnCerrarModal.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", (event) => {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            });
        });

        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>
