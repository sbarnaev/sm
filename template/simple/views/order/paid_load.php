<?php defined('BILLINGMASTER') or die; 

// Страница для скачивания платных и других продуктов по ссылке из письма.
$title = 'Скачать';
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <h1><?php echo System::Lang('THANK_YOU_FOR_PAID_ORDER');?></h1>
            
            <div class="order_data">
            <h3><?php echo System::Lang('YOUR_ORDER_COMPLECTION');?></h3>
            
            <table>
            
            <?php 
            foreach($items as $item): ?>
            <tr>
            <td>
                    <form action="" id="<?php echo $item['product_id'];?>" method="POST">
                    <strong><?php $product_data = Product::getProductName($item['product_id']);
                    echo $product_data['product_name'].$product_data['mess'];?></strong>
            </td>
            
            <td>
                <?php if($product_data['dwl'] == 1):?>
                <input type="hidden" name="item" value="<?php echo $item['product_id'];?>">
                <?php if($item['dwl_count'] < $dwl_count){?>
                <input type="submit" class="button" name="download" value="<?php echo System::Lang('DOWNLOAD');?>">
                <?php } else echo System::Lang('LINK_TIME_EXPIRED');?>
                <?php endif; ?>
                </form>
            </td>
                
            </tr>
            <?php endforeach; ?>
            </table>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>