	<?php defined('BILLINGMASTER') or die; 

// Страница спасибо при скачивании бесплатного продукта
$title = 'Подтвердите заказ';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <h1><?=System::Lang('CONFIRM_YOUR_ORDER');?></h1>
            
            <div class="order_data">
                <?=System::Lang('LETTER_FOR_ACCESS_ORDER');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>