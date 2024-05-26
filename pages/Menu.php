<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $itemToAdd = $_POST['item_name'];
    if (!in_array($itemToAdd, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $itemToAdd;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    $_SESSION['cart'] = [];
    echo "<script>alert('¡Tu pedido ha sido realizado con éxito!');</script>";
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
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .cart-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body class="grid grid-cols-12 w-screen h-screen p-10">
    <form method="post" class="col-span-2 p-1 grid grid-rows-12 border rounded-s-lg">
        <div class="row-span-11 flex flex-col space-y-5 rounded-lg w-full p-1">
            <?php
            foreach ($_SESSION['cart'] as $item) {
                echo ("
                    <div class='cart-item bg-white rounded flex items-center justify-between'>
                        $item
                        <form method='post'>
                            <input type='hidden' name='item_name' value='$item'>
                            <button type='submit' name='remove_item' class='bg-red-200'>borrar</button>
                        </form>
                    </div>
                ");
            }
            ?>
        </div>
    </form>
    <section class="col-span-10 p-4 rounded-e-lg border">
        <div class="swiper mySwiper w-full h-full">
            <div class="swiper-wrapper">
                <?php
                $jsonData = file_get_contents('data.json');
                $items = json_decode($jsonData, true);
                $itemsPerSlide = 6; // 3 items per row, 2 rows per slide
                $totalItems = count($items);
                $totalSlides = ceil($totalItems / $itemsPerSlide);

                for ($slideIndex = 0; $slideIndex < $totalSlides; $slideIndex++) {
                    echo "<div class='swiper-slide bg-pink-300 grid grid-cols-3 grid-rows-2 gap-4 p-4'>";
                    for ($itemIndex = 0; $itemIndex < $itemsPerSlide; $itemIndex++) {
                        $currentIndex = ($slideIndex * $itemsPerSlide) + $itemIndex;
                        if ($currentIndex < $totalItems) {
                            $itemName = $items[$currentIndex]['name'];
                            echo "
                                <div class='item flex flex-col items-center border p-2 bg-white rounded'>
                                    <span>$itemName</span>
                                    <form method='post'>
                                        <input type='hidden' name='item_name' value='$itemName'>
                                        <button type='submit' name='add_to_cart' class='mt-2 p-2 bg-blue-500 text-white rounded'>Agregar</button>
                                    </form>
                                </div>
                            ";
                        }
                    }
                    echo "</div>";
                }
                ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Ordenar fuera de la rejilla -->
    <div class="absolute z-10 bottom-4 right-4">
        <form method="post">
            <input type="submit" class="text-xl border rounded-lg h-[50px] w-[200px] bg-blue-500 text-white font-bold cart-btn" name="order" value="ORDENAR"></input>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            loop: true,
        });
    </script>
</body>
</html>
