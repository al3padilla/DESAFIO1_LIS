<?php
// Funciones de validación
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
        mkdir($directorioImagenes, 0777, true);  // Crear la carpeta si no existe
    }

    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $rutaImagen = $directorioImagenes . uniqid() . '.' . $extension;
    move_uploaded_file($imagen['tmp_name'], $rutaImagen);

    return $rutaImagen;
}

// Procesar el formulario de agregar producto
$errores = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $existencias = $_POST['existencias'];
    $imagen = $_FILES['imagen'];

    // Validar los datos
    $errores[] = validarCodigo($codigo);
    $errores[] = validarNombre($nombre);
    $errores[] = validarDescripcion($descripcion);
    $errores[] = validarPrecio($precio);
    $errores[] = validarExistencias($existencias);
    $errores[] = validarImagen($imagen);

    // Filtrar errores nulos
    $errores = array_filter($errores);
    
    // Si no hay errores, guardar el producto
    if (empty($errores)) {
        $xmlFile = '../productos.xml';
        $xml = file_exists($xmlFile) ? simplexml_load_file($xmlFile) : new SimpleXMLElement('<productos></productos>');

        $producto = $xml->addChild('producto');
        $producto->addChild('codigo', $codigo);
        $producto->addChild('nombre', $nombre);
        $producto->addChild('descripcion', $descripcion);
        $producto->addChild('imagen', guardarImagen($imagen));
        $producto->addChild('categoria', $_POST['categoria']);
        $producto->addChild('precio', $precio);
        $producto->addChild('existencias', $existencias);

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
    <title>Agregar Producto - TextilExport</title>
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
        <h2 class="text-center">Agregar Producto</h2>

        <!-- Mostrar errores -->
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Código del Producto</label>
                <input type="text" class="form-control" name="codigo" value="<?php echo isset($codigo) ? $codigo : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" required><?php echo isset($descripcion) ? $descripcion : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input type="file" class="form-control" name="imagen" accept=".jpg, .png">
                <small class="text-muted">Formatos permitidos: .jpg, .png</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select class="form-select" name="categoria">
                    <option value="Textil">Textil</option>
                    <option value="Promocional">Promocional</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" class="form-control" name="precio" value="<?php echo isset($precio) ? $precio : ''; ?>" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Existencias</label>
                <input type="number" class="form-control" name="existencias" value="<?php echo isset($existencias) ? $existencias : ''; ?>" min="0" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar Producto</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
