<?php defined('BILLINGMASTER') or die; 
$title = 'Спасибо!';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); 
$params = unserialize(base64_decode($custom_data['params']));?>
<body id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="content">
<div id="order_form">
        <div class="container-cart">
            <h1><?php echo System::Lang('CUSTOM_SUCCESS_THANK');?></h1>
            
            <div class="order_data">
                
                <?php echo $params['thanks'];?>
            </div>
        </div>
    </div>
    </div>
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>