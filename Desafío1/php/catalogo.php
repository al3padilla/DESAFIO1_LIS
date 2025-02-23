<?php
// Carga el archivo XML
$xml = simplexml_load_file("../productos.xml") or die("Error: No se pudo cargar el archivo XML.");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - TextilExport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estiloprodu.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

    <header>
    <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand">
            <img src="../imagenes/logo.png" alt="Logo" width="50" height="50">
            TextilExport
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.html">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/al3padilla/DESAFIO1_LIS" target="_blank">
                        <img src="../imagenes/git.png" width="25">
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <img src="../imagenes/login.png" width="25">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    </header>

    <!-- Filtro y busqueda -->
    <div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar producto...">
        </div>
        <div class="col-md-6">
            <select id="categoryFilter" class="form-control">
                <option value="">Todas las categorías</option>
                <option value="Promocional">Promocional</option>
                <option value="Textil">Textil</option>
            </select>
        </div>
    </div>
</div>


    <!-- Modal para iniciar sesión -->
    <div class="modal fade" id="loginModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Iniciar sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form onsubmit="validarLogin(event)">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validarLogin(event) {
        event.preventDefault(); 

        let usuario = document.getElementById("username").value;
        let contraseña = document.getElementById("password").value;

        let usuarioCorrecto = "admin";
        let contraseñaCorrecta = "1234";

        if (usuario === usuarioCorrecto && contraseña === contraseñaCorrecta) {
            window.location.href = "index.php"; 
        } else {
            alert("Usuario o contraseña incorrectos. Inténtalo de nuevo.");
        }
    }
    </script>

    <!-- Título del Catálogo -->
    <div class="container mt-5">
        <h2 class="text-center">Catálogo de Productos</h2>

        <!-- Fila de los productos -->
        <div class="row" id="productList">
            <?php foreach ($xml->producto as $producto): ?>
                <div class="col-md-4 product" data-category="<?php echo $producto->categoria; ?>" data-stock="<?php echo $producto->existencias; ?>">
                <div class="card">
                <img src="<?php echo $producto->imagen; ?>" class="card-img-top" alt="<?php echo $producto->nombre; ?>">
                <div class="card-body">
                <h5 class="card-title"><?php echo $producto->nombre; ?></h5>
                <p class="card-text"><?php echo "$" . $producto->precio; ?></p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="showProductDetail('<?php echo $producto->codigo; ?>')">Ver Detalles</button>
            </div>
        </div>
    </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal de detalles del producto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                   
                </div>
            </div>
        </div>
    </div> 

    <footer>
        <div class="social-buttons">
            <a href="https://www.facebook.com/UDBelsalvador" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://x.com/UDBelsalvador" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/UDBelsalvador/" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.linkedin.com/school/udbelsalvador/" target="_blank"><i class="fab fa-linkedin"></i></a>
        </div>
        <div class="copyright">
            &copy; TextilExport 2025. Todos los derechos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/produ_catalogo.js"></script>
    <script src="../js/he_fo.js"></script>
</body>
</html>
