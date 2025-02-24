<?php

function validarCodigo($codigo) {
    if (!preg_match('/^PROD\d{5}$/', $codigo)) {
        return "El código del producto debe tener el formato PROD#####.";
    }
    return null;
}

function validarNombre($nombre) {
    if (empty(trim($nombre))) {
        return "El nombre del producto es obligatorio.";
    }
    return null;
}

function validarDescripcion($descripcion) {
    if (empty(trim($descripcion))) {
        return "La descripción es obligatoria.";
    }
    return null;
}

function validarPrecio($precio) {
    if (!is_numeric($precio) || $precio <= 0) {
        return "El precio debe ser un número positivo.";
    }
    return null;
}

function validarExistencias($existencias) {
    if (!is_numeric($existencias) || $existencias < 0) {
        return "Las existencias deben ser un número entero no negativo.";
    }
    return null;
}

function validarImagen($imagen) {
    if ($imagen['error'] == 0) {
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($extension), ['jpg', 'png'])) {
            return "La imagen debe ser un archivo .jpg o .png.";
        }
    }
    return null;
}

function guardarImagen($imagen) {
    $directorioImagenes = '../imagenes/';
    if (!file_exists($directorioImagenes)) {
        mkdir($directorioImagenes, 0777, true);
    }

    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $rutaImagen = $directorioImagenes . uniqid() . '.' . $extension;
    move_uploaded_file($imagen['tmp_name'], $rutaImagen);

    return $rutaImagen;
}

$xmlFile = '../productos.xml';
if (file_exists($xmlFile)) {
    $xml = simplexml_load_file($xmlFile);
} else {
    die("No se pudo cargar el archivo XML.");
}

if (isset($_GET['codigo'])) {
    $codigoProducto = $_GET['codigo'];
    $productoEncontrado = null;
    foreach ($xml->producto as $producto) {
        if ($producto->codigo == $codigoProducto) {
            $productoEncontrado = $producto;
            break;
        }
    }
    if (!$productoEncontrado) {
        die("Producto no encontrado.");
    }
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $existencias = $_POST['existencias'] ?? '';
    $imagen = $_FILES['imagen'] ?? null;
    $categoria = $_POST['categoria'] ?? '';

    $errores['codigo'] = validarCodigo($codigo);
    $errores['nombre'] = validarNombre($nombre);
    $errores['descripcion'] = validarDescripcion($descripcion);
    $errores['precio'] = validarPrecio($precio);
    $errores['existencias'] = validarExistencias($existencias);
    $errores['imagen'] = validarImagen($imagen);

    $errores = array_filter($errores);

    if (empty($errores)) {
        $productoEncontrado->codigo = $codigo;
        $productoEncontrado->nombre = $nombre;
        $productoEncontrado->descripcion = $descripcion;
        $productoEncontrado->categoria = $categoria;
        $productoEncontrado->precio = $precio;
        $productoEncontrado->existencias = $existencias;

        if ($imagen['error'] == 0) {
            $productoEncontrado->imagen = guardarImagen($imagen);
        }

        $xml->asXML($xmlFile);

        header('Location:index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto - TextilExport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/estiloadmin.css">
</head>
<body>
    <nav class="navbar custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">TextilExport - Portal Administrativo</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Modificar Producto</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Código del Producto</label>
                <input type="text" class="form-control <?php echo isset($errores['codigo']) ? 'is-invalid' : ''; ?>" name="codigo" value="<?php echo htmlspecialchars($productoEncontrado->codigo); ?>" readonly>
                <div class="invalid-feedback"><?php echo $errores['codigo'] ?? ''; ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" name="nombre" value="<?php echo htmlspecialchars($productoEncontrado->nombre); ?>">
                <div class="invalid-feedback"><?php echo $errores['nombre'] ?? ''; ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control <?php echo isset($errores['descripcion']) ? 'is-invalid' : ''; ?>" name="descripcion"><?php echo htmlspecialchars($productoEncontrado->descripcion); ?></textarea>
                <div class="invalid-feedback"><?php echo $errores['descripcion'] ?? ''; ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input type="file" class="form-control <?php echo isset($errores['imagen']) ? 'is-invalid' : ''; ?>" name="imagen" accept=".jpg, .png">
                <small class="text-muted">Formatos permitidos: .jpg, .png</small>
                <div class="invalid-feedback"><?php echo $errores['imagen'] ?? ''; ?></div>
                <?php if (!empty($productoEncontrado->imagen)): ?>
                    <p>Imagen actual: <img src="<?php echo htmlspecialchars($productoEncontrado->imagen); ?>" width="100"></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select class="form-select" name="categoria">
                    <option value="Textil" <?php echo ($productoEncontrado->categoria == 'Textil') ? 'selected' : ''; ?>>Textil</option>
                    <option value="Promocional" <?php echo ($productoEncontrado->categoria == 'Promocional') ? 'selected' : ''; ?>>Promocional</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="text" class="form-control <?php echo isset($errores['precio']) ? 'is-invalid' : ''; ?>" name="precio" value="<?php echo htmlspecialchars($productoEncontrado->precio); ?>">
                <div class="invalid-feedback"><?php echo $errores['precio'] ?? ''; ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Existencias</label>
                <input type="text" class="form-control <?php echo isset($errores['existencias']) ? 'is-invalid' : ''; ?>" name="existencias" value="<?php echo htmlspecialchars($productoEncontrado->existencias); ?>">
                <div class="invalid-feedback"><?php echo $errores['existencias'] ?? ''; ?></div>
            </div>
            <button type="submit" class="btn btn-success">Actualizar Producto</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>