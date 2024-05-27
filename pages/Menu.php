<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $itemToAdd = $_POST['item_name'];
    $_SESSION['cart'][] = $itemToAdd;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    // Aquí puedes agregar el código para enviar los datos a la base de datos.
    // Por ahora, solo muestra una alerta.
    echo "<script>alert('¡Tu pedido ha sido realizado con éxito!');</script>";
    $_SESSION['cart'] = [];
}

// Handle remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $itemToRemove = $_POST['item_name'];
    if (($key = array_search($itemToRemove, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <script src="../tailwindcss.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .cart-item {
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid black;
            border-radius: 5px;
        }

        .cart-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: white;
        }

        .swiper-pagination-bullet {
            background-color: black; 
        }

        .swiper-pagination-bullet-active {
            background-color: purple; 
        }
    </style>
</head>
<body class="grid grid-cols-12 w-screen h-screen p-10">
    <form method="post" class="col-span-2 p-1 grid grid-rows-12 border border-black rounded-s-lg">
        <div class=" row-span-12 flex flex-col space-y-5 rounded-lg p-1">
            <?php
            $totalPrice = 0;
            $jsonData = file_get_contents('data.json');
            $items = json_decode($jsonData, true);

            foreach ($_SESSION['cart'] as $item) {
                foreach ($items as $product) {
                    if ($product['name'] === $item) {
                        $price = $product['price'];
                        $totalPrice += $price;
                        break;
                    }
                }
                echo ("
                    <div class='cart-item bg-white rounded flex items-center justify-between'>
                        $item - $$price
                        <form method='post'>
                            <input type='hidden' name='item_name' value='$item'>
                            <button type='submit' name='remove_item' class='bg-black text-md text-white border rounded-lg p-1'>BORRAR</button>
                        </form>
                    </div>
                ");
            }
            echo "<div class='text-center bg-white p-2 rounded mt-4'>Total: $$totalPrice</div>";
            ?>
            <button type="submit" name="order" class="cart-btn">Ordenar</button>
        </div>
    </form>
    <section class="col-span-10 p-4 rounded-e-lg border border-black">
        <div class="swiper mySwiper w-full h-full">
            <div class="swiper-wrapper">
                <?php
                $itemsPerSlide = 6; // 3 items per row, 2 rows per slide
                $totalItems = count($items);
                $totalSlides = ceil($totalItems / $itemsPerSlide);

                for ($slideIndex = 0; $slideIndex < $totalSlides; $slideIndex++) {
                    echo "<div class='swiper-slide bg-black text-xl grid grid-cols-3 grid-rows-2 gap-4 p-4'>";
                    for ($itemIndex = 0; $itemIndex < $itemsPerSlide; $itemIndex++) {
                        $currentIndex = ($slideIndex * $itemsPerSlide) + $itemIndex;
                        if ($currentIndex < $totalItems) {
                            $itemName = $items[$currentIndex]['name'];
                            $price = $items[$currentIndex]["price"];
                            echo "
                                <div class='item flex flex-col items-center border p-2 bg-white rounded'>
                                    <p class='item-name'>$itemName</p>
                                    <p class='item-price'>$$price</p>
                                    <form method='post'>
                                        <input type='hidden' name='item_name' value='$itemName'>
                                        <button type='submit' name='add_to_cart' class='cart-btn'>Añadir al carrito</button>
                                    </form>
                                </div>
                            ";
                        }
                    }
                    echo "</div>";
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.mySwiper', {
            slidesPerView: 1,
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            loop:true
        });
    </script>
</body>
</html>
