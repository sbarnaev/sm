<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="cart-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>


    <div id="content">
        <div class="layout" id="cart">
            <div class="container-cart">
                <?php if($product_in_cart):?>
                <ul class="container-crumbs  ">
                    <li class="first-active"><span>1</span><?=System::Lang('CART');?></li>
                    <li><span>2</span><?=System::Lang('YOUR_DATES');?></li>
                    <li><span>3</span><?=System::Lang('PAYMENT_OPTION');?></li>
                </ul>
                <?php endif;?>

                <h2><?=System::Lang('CART');?></h2>
                <?php if($product_in_cart):
                    $total = 0;?>
                    <div class="offer main">
                        <div class="order_items">
                            <?php foreach($products as $product):
                                $price = Price::getPriceinCatalog($product['product_id'], false);
                                $total += $price['real_price'];?>
    
                                <div class="order_item">
                                    <div class="order_item-left"><?php if(!empty($product['product_cover'])):?>
                                        <img src="/images/product/<?=$product['product_cover'];?>" alt="<?=$product['img_alt'];?>">
                                        <?php endif;?></div>
                                    <div class="order_item-desc">
                                        <h4><?=$product['product_name'];?></h4>
                                        <?php if($product['product_desc'] != null):?>
                                            <div class="product_desc"><?=nl2br($product['product_desc']);?></div>
                                        <?php endif;?>
                                    </div>
                                    <div class="order_item-price_box-right">
                                        <span class="font-bold"><?="{$price['real_price']} {$setting['currency']}";?></span>
                                        <a class="table-short-link__delete" href="/cart/del/<?php echo $product['product_id'];?>">
                                            <span class="icon-remove"></span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <hr>

                    <div><div class="payment-itogo">
                        <p><?=System::Lang('TOTAL_COAST');?>: <?="{$total} {$setting['currency']}";?></p>
                    </div></div>

                    <form action="" method="POST">
                        <p class="btn-next"><input type="submit" class="button btn-blue" name="checkout" value="<?=System::Lang('CHECKOUT');?>"></p>
                    </form>

                    <?php require_once (__DIR__.'/../common/add_promo_code.php')?>

                    <?php else:?>
                        <p><?=System::Lang('EMPTY_CART');?></p>
                        <p><a href="<?=$setting['script_url'];?>"><?=System::Lang('ON_MAIN');?></a></p>
                    <?php endif;?>
                </div>
            </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>