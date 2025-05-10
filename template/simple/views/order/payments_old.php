<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <ul class="container-crumbs <?php if($related_products && $setting['use_cart'] == 0) echo ''; else echo 'container-crumbs-two-steps'?> ">
                <li class="crumbs-no-active crumbs-order"><span>1</span><?=System::Lang('YOUR_DATES');?></li>
                <?php if($related_products && $setting['use_cart'] == 0) echo '<li class="crumbs-no-active crumbs-order"><span>2</span><a href="/related/'.$order_date.'">Корзина</a></li>'?>
                <li class="three-active"><span><?php if($related_products && $setting['use_cart'] == 0) echo '3'; else echo '2'?></span><?=System::Lang('PAYMENT_OPTION');?></li>
            </ul>
            <h2><?=System::Lang('REPAYMENT');?></h2>

            <div class="order_data">
                <h3><?=System::Lang('ORDER_DATES');?> <?php echo $order_date;?></h3>

                <div class="offer main">

                    <?php $total = 0 + $tax;
                    $full_price = 0;
                    foreach($order_items as $item):?>
                    <div class="order_item">
                        <div class="order_item-left">
                            <?php $product = Product::getMinProductById($item['product_id']);
                            if($product['product_cover']!= null):?>
                            <img src="/images/product/<?php echo $product['product_cover'];?>" alt="">
                            <?php endif;?>
                        </div>

                        <div class="order_item-desc">
                            <h4><?php echo $item['product_name'];?></h4>
                        </div>

                        <div class="order_item-price_box-right">
                            <?php if($product['price'] > $item['price']){?>
                            <span class="old_price"><?php echo $product['price'];?> <?php echo $setting['currency'];?></span>
                            <div class="font-bold"><?php echo $item['price'];?> <?php echo $setting['currency'];?></div>
                            <?php } else {?>
                            <div class="font-bold"><?php echo $item['price'];?> <?php echo $setting['currency'];?></div>
                            <?php }?>
                        </div>
                    </div>
                    <hr>
                    <?php $total = $total + $item['price'];
                    $full_price = $full_price + $product['price'];
                    if($item['product_id'] == 18) $rs = true; // рассрочка
                    endforeach; ?>

                    <div class="order_item-bottom">
                        <div class="order_item-bottom__left">
                            <p><?=System::Lang('ORDER_NUMBER');?> <?php echo $order_date;?></p>
                            <p><?php echo $order['client_name'];?> (<?php echo $order['client_email'];?>)</p>
                            <?php if($tax != 0):?>
                            <p><?=System::Lang('DELIVERY');?> <?php echo $tax;?> <?php echo $setting['currency'];?></p>
                            <?php endif;?>
                        </div>
                        <div class="payment-bottom__right">
                            <p><?=System::Lang('ORDER_SUMM_TAG');?> <?php echo $total; ?> <?php echo $setting['currency'];?></p>
                            <?php if($full_price > $total):?>
                        <p><?=System::Lang('YOUR_SAVINGS');?> <?php echo $full_price - $total;?> <?php echo $setting['currency'];?></p>
                        <?php endif;?>
                        </div>
                    </div>
                    <div class="payment-itogo"><?=System::Lang('RESULT');?> <?php echo $total; ?> <?php echo $setting['currency'];?></div>
                </div>
            </div>

            <div class="payments_list">
                <h3><?=System::Lang('CHOOSE_PAYMENT');?></h3>
                <div class="payment_item">
                    <div class="offer main">
                <?php foreach($payments as $payment):
				if(isset($plane) && $plane['select_payments'] != null) {
                        $selected = unserialize(base64_decode($plane['select_payments']));
                        if(!in_array($payment['payment_id'], $selected)) continue;
                    }
                if((isset($rs) && $payment['name'] != 'yakassapi')) continue;
                    ?>
                        <div class="order_item">
                            <div class="order_item-left"><img src="<?php echo $setting['script_url'];?>/payments/<?php echo $payment['name']?>/<?php echo $payment['name']?>.png" alt=""></div>
                            <div class="order_item-desc">
                                <h4><?php if($payment['public_title'] != null) echo $payment['public_title'];
								else echo $payment['title'];?></h4>
                                <?php echo $payment['payment_desc'];?>
                            </div>
                            <div class="payment_button">
                                <?php require_once(ROOT . '/payments/'.$payment['name'].'/form.php');?>
                            </div>
                        </div>
                <?php endforeach; ?>
            </div>
        </div>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>