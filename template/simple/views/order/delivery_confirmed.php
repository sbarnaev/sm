<?php defined('BILLINGMASTER') or die; 

// Страница спасибо при скачивании бесплатного продукта
$title = 'Спасибо, заказ передан в обработку';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <h1><?=System::Lang('ORDER_IN_PROCESS');?></h1>
            
            <div class="order_data">
                <?=System::Lang('ORDER_CONFIRMED');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>