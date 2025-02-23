<?php
// Carga los productos desde el archivo XML
$xmlFile = '../productos.xml';

if (!file_exists($xmlFile)) {
    $xml = new SimpleXMLElement('<productos/>');
    $xml->asXML($xmlFile);
} else {
    $xml = simplexml_load_file($xmlFile);
}

// Elimina un producto si se solicita
if (isset($_GET['eliminar'])) {
    $codigoEliminar = $_GET['eliminar'];
    foreach ($xml->producto as $producto) {
        if ((string)$producto->codigo == $codigoEliminar) {
            $dom = dom_import_simplexml($producto);
            $dom->parentNode->removeChild($dom);
            break;
        }
    }
    $xml->asXML($xmlFile);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - TextilExport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/estiloadmin.css">
</head>
<body>
    <nav class="navbar custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold">TextilExport - Portal Administrativo</a>
            <a class="navbar-brand fw-bold" href="../index.html">Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2 class="text-center">Gestión de Inventario</h2>
        
        <a href="agregar_producto.php" class="btn btn-outline-primary mb-3">Añadir Producto</a>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Imagen</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Existencias</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    // Muestra los productos desde el archivo XML
                    foreach ($xml->producto as $producto) {
                        echo '<tr>';
                        echo '<td>' . $producto->codigo . '</td>';
                        echo '<td>' . $producto->nombre . '</td>';
                        echo '<td>' . $producto->descripcion . '</td>';
                        echo '<td><img src="' . $producto->imagen . '" alt="Imagen" width="100"></td>';
                        echo '<td>' . $producto->categoria . '</td>';
                        echo '<td>' . $producto->precio . '</td>';
                        echo '<td>' . $producto->existencias . '</td>';
                        echo '<td>';
                        echo '<a href="?eliminar=' . $producto->codigo . '" class="btn btn-danger btn-sm">Eliminar</a> ';
                        echo '<a href="modificar_producto.php?codigo=' . $producto->codigo . '" class="btn btn-warning btn-sm">Modificar</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>    
</body>
</html>
