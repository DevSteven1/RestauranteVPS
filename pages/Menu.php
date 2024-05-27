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
            background-color: black; 
        }
    </style>
</head>
<body class="grid grid-cols-12 w-screen h-screen p-10 bg-gradient-to-r from-black">
    <form method="post" class="bg-white col-span-2 p-1 grid grid-rows-12 border border-black rounded-s-lg">
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
    <section class="bg-white col-span-10 p-4 rounded-e-lg border border-black">
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
                                    <img class='h-28 w-28' src='data:image/png;base64,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' />
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
