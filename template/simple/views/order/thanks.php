<?php defined('BILLINGMASTER') or die; 

// Страница спасибо при скачивании бесплатного продукта
$title = 'Спасибо!';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <div class="maincol-inner-white">
            <h1><?=System::Lang('THANKS');?></h1>
            
            <div class="order_data">
                <?=System::Lang('INSTRUCTIONS_ON_EMAIL');?>
            </div>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>