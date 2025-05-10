<?php defined('BILLINGMASTER') or die; 

// Страница спасибо для рассрочки
$title = 'Спасибо!';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <div class="maincol-inner-white">
            <h1><?=System::Lang('APPLICATION_SENDED');?></h1>
            
            <div class="order_data">
                <?=$letters['waiting'];?>
            </div>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>