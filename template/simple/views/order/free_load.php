<?php defined('BILLINGMASTER') or die; 

// Страница для скачивания FREE продукта
$title = 'Скачать';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <h1><?=System::Lang('THANKS');?></h1>
            
            <div class="order_data">
                <p><?php $product = Product::getMinProductById($order['product_id']); echo $product['product_name'];?></p>
                <p><a href="<?php echo $product['link'];?>" class="order_button"><?=System::Lang('DOWNLOAD');?></a></p>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>