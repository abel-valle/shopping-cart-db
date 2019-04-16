<?php
// Instrucciones para activar y mostrar los mensajes de error.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Archivo que contiene la función execute().
include 'connection.php';

// Función en PHP que crea cada item de la galería de productos.
function createProductGallery() {
    // El query solo devolverá los registros con valor 1 en la columna 'visible'.
    $q = "select * from products where visible = 1 order by name";
    $recordSet = execute($q);

    // Iteración sobre cada registro del recordset.
    while($row = mysqli_fetch_array($recordSet)) {
        // A partir del renglón de datos $row, se obtienen los valores por cada columna.
        $id_product = $row['id_product'];
        $name       = $row['name'];
        $description = $row['description'];
        $price      = $row['price'];
        $brand      = $row['brand'];
        $image      = $row['image'];
        
        echo "
            <div class='items' id='item$id_product'>
                <img id='item$id_product-img' src='$image'>
                <input type='button' value='Agregar al Carrito' onclick='cart(\"item$id_product\")'>
                <p>$name</p>
                <p>Precio - $$price</p>
                <input type='hidden' id='item$id_product-name' value='$description'>
                <input type='hidden' id='item$id_product-price' value='$price'>
            </div>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    
    <!-- Estilo personalizado y para Bootstrap -->
    <link rel="stylesheet" type="text/css" href="cart-style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- CSS para íconitos coquetos -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script type="text/javascript">
        // Mediante jquery se detecta cuando el documento está listo.
        $(document).ready(function () {

            // Invocación ajax por método post.
            $.ajax({
                type: 'post',
                url: 'store-items.php',
                data: {
                    // En este caso, no importa el valor sino el nombre de variable.
                    // Se usará xxx para indicar que el valor no es significativo.
                    total_cart_items: 'xxx'
                },
                success: function (response) {
                    // response: conteo de items en sesión.
                    $('#total-items').val(response);
                }
            });

            $('#info-cart').slideUp();

        });

        // Función que recibe parte del id de cada elemento html.
        function cart(id) {
            // Se contruye el id completo por cada elemento relacionado con el item.
            var img_src = $("#" + id + "-img").prop("src");
            var name = $("#" + id + "-name").val();
            var price = $("#" + id + "-price").val();

            $.ajax({
                type: 'post',
                url: 'store-items.php',
                data: {
                    item_src: img_src,
                    item_name: name,
                    item_price: price
                },
                success: function (response) {
                    // response: conteo de items en sesión después de agregar un item.
                    $("#total-items").val(response);
                    // false: significa que no va a aplicar la animación slideToggle().
                    showCart(false);
                }
            });

        }

        // Función que invoca store-items.php y recibe como respuesta código HTML
        // con los <div> de cada item en sesión.
        // alternateToggle: es true ó false dependiendo si se quiere aplicar la animación. 
        function showCart(alternateToggle) {
            $.ajax({
                type: 'post',
                url: 'store-items.php',
                data: {
                    showcart: 'xxx'
                },
                success: function (response) {
                    // La respuesta es código HTML que se coloca dentro de #mycart.
                    $('#mycart').html(response);
                    if (alternateToggle) {
                        // Función de jquery que aplica una animación alternando entre
                        // slideUp() y slideDown().
                        $('#mycart').slideToggle();
                    }
                }
            });
        }

        function removeOneItem(pos) {
            $.ajax({
                type: 'post',
                url: 'store-items.php',
                data: {
                    remove: 'xxx',
                    position: pos
                },
                success: function (response) {
                    $("#total-items").val(response);
                    showCart(false);
                }
            });
        }

        // Función que manda por POST el dato 'destroy', store-items.php lo recibe y elimina la sesión. 
        function sessionDestroy() {
            $.ajax({
                type: 'post',
                url: 'store-items.php',
                data: {
                    destroy: 'xxx'
                },
                success: function (response) {
                    $("#total-items").val(response);
                    showCart(false);
                }
            });
        }

    </script>

</head>

<body>
    <button type='button' class='btn btn-danger session-button' onclick='sessionDestroy()'>
       Destruir sesión
    </button>

    <br>
    <p id="cart-button" onclick="showCart(true)">
        <img src="images/cart-icon.png">
        <input type="button" id="total-items" value="">
    </p>

    <!-- Aquí se despliegan todos los items usando jquery, generados en el store-items.php. -->
    <div id="mycart"></div>

    <h1>Carrito de compra simple con jquery, ajax y PHP</h1>

    <div id="item-div">
    <?php 
        // Invocación de la función PHP que crea cada <div> por producto.
        createProductGallery();
    ?>
    </div>

    <!-- JavaScript para Bootstrap. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>