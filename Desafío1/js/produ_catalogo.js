document.addEventListener("DOMContentLoaded", function () { 
    document.getElementById('searchInput').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);
});

// FUNCIÓN DE FILTRO Y BÚSQUEDA
function filterProducts() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const categoryValue = document.getElementById('categoryFilter').value.toLowerCase().trim();
    
    const products = document.querySelectorAll('.product');

    products.forEach(product => {
        const productName = product.querySelector('.card-title').textContent.toLowerCase().trim();
        const productCategory = product.getAttribute('data-category') ? product.getAttribute('data-category').toLowerCase().trim() : '';

        const matchesSearch = productName.includes(searchValue);
        const matchesCategory = categoryValue === "" || productCategory === categoryValue;

        if (matchesSearch && matchesCategory) {
            product.classList.remove("d-none");
        } else {
            product.classList.add("d-none");
        }
    });
}

// FUNCIÓN PARA VER DETALLE DE PRODUCTOS
function showProductDetail(productId) {
    fetch(`../productos.xml?nocache=${new Date().getTime()}`)
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, 'text/xml'))
        .then(data => {
            const productos = data.getElementsByTagName('producto');
            let productoEncontrado = null;
            
            for (let producto of productos) {
                if (producto.getElementsByTagName('codigo')[0].textContent === productId) {
                    productoEncontrado = producto;
                    break;
                }
            }
            
            if (productoEncontrado) {
                const nombre = productoEncontrado.getElementsByTagName('nombre')[0].textContent;
                const imagen = productoEncontrado.getElementsByTagName('imagen')[0].textContent;
                const precio = productoEncontrado.getElementsByTagName('precio')[0].textContent;
                const stock = parseInt(productoEncontrado.getElementsByTagName('existencias')[0].textContent, 10);
                const disponibilidad = stock > 0 ? "Disponible" : "Producto no disponible";
                
                document.getElementById('modalContent').innerHTML = `
                    <img src="${imagen}" class="img-fluid" alt="${nombre}">
                    <h5 class="mt-3">${nombre}</h5>
                    <p><strong>Precio:</strong> $${precio}</p>
                    <p><strong>Disponibilidad:</strong> ${disponibilidad}</p>
                `;
            }
        });
}
