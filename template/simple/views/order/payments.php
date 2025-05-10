<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');?>

<div id="order_form">
    <div class="container-cart">
        <ul class="container-crumbs <?php if($related_products && $setting['use_cart'] == 0) echo ''; else echo 'container-crumbs-two-steps'?> ">
            <li class="crumbs-no-active crumbs-order"><span>1</span><?=System::Lang('YOUR_DATES');?></li>
            <?php if($related_products && $setting['use_cart'] == 0) echo '<li class="crumbs-no-active crumbs-order"><span>2</span><a href="/related/'.$order_date.'">Корзина</a></li>'?>
            <li class="three-active"><span><?php if($related_products && $setting['use_cart'] == 0) echo '3'; else echo '2'?></span><?=System::Lang('PAYMENT_OPTION');?></li>
        </ul>

        <h2><?=System::Lang('REPAYMENT');?></h2>

        <div class="order_data">
            <h3><?=System::Lang('ORDER_DATES');?> <?=$order_date;?></h3>
            <div class="offer main offer-mb-35">
                <?php $full_price = 0;
                foreach($order_items as $item):
                    $product = Product::getMinProductById($item['product_id']);
                    $full_price += $product['price'];?>

                    <div class="order_item">
                        <?php if($product['product_cover']!= null):?>
                            <div class="order_item-left">
                                <img src="/images/product/<?=$product['product_cover'];?>" alt="">
                            </div>
                        <?php endif;?>

                        <div class="order_item-desc">
                            <h4><?=$item['product_name'];?></h4>
                        </div>

                        <div class="order_item-price_box-right">
                            <?php if($product['price'] > $item['price']):?>
                                <span class="old_price"><?=$product['price'];?> <?=$setting['currency'];?></span>
                            <?php endif;?>
                            <div class="font-bold"><?=$item['price'];?> <?=$setting['currency'];?></div>
                        </div>
                    </div><hr>
                <?php endforeach;?>

                <div class="order_item-bottom">
                    <div class="order_item-bottom__left">
                        <p><?=System::Lang('ORDER_NUMBER');?> <?=$order_date;?></p>
                        <?php if(!isset($hide_cl_email) || !$hide_cl_email):
                            $order_info = unserialize(base64_decode($order['order_info']));?>
                            <p><?=$order['client_name']; if(!empty($order_info['surname'])) echo ' '.$order_info['surname'];?> (<?=$order['client_email'];?>)</p>
                        <?php endif;?>

                        <?php if($tax != 0):?>
                            <p><?=System::Lang('DELIVERY');?> <?=$tax;?> <?=$setting['currency'];?></p>
                        <?php endif;?>
                    </div>

                    <div class="payment-bottom__right">
                        <p><?=System::Lang('ORDER_SUMM_TAG');?> <?="{$total} {$setting['currency']}";?></p>
                        <?php if($full_price > $total && $order['installment_map_id'] == 0):?>
                            <p><?=System::Lang('YOUR_SAVINGS');?> <?=$full_price - $total;?> <?=$setting['currency'];?></p>
                        <?php endif;?>
                    </div>
                </div>
                
                <div class="payment-itogo"><?=System::Lang('RESULT');?> <?="{$total} {$setting['currency']}";?></div>
            </div>

            <h3><?=System::Lang('CHOOSE_PAYMENT');?></h3>
            <div class="tabs tabs-payment">
                <?php if($order['installment_map_id'] == 0):?>
                    <ul class="tabs-payment-ul">
                        <?php if($installment_list || $prepayment_list):?>
						    <li><?=System::Lang('PAYMENT_IMMEDIATELY');?></li>
						<?php endif;

                        if($installment_list):?>
                            <li class="tabs-payment-small-pad"><?=System::Lang('INSTALLMENTS_PAYMENT');?></li>
                        <?php endif;

                        if ($prepayment_list):?>
                            <li class="tabs-payment-small-pad"><?=System::Lang('PREPAYMENT');?></li>
                        <?php endif;?>
                    </ul>
                <?php endif;?>

                <div class="tabs-payment-div">
                    <div>
                        <div class="tabs-payments_list">
                            <div class="payment_item">
                                <div class="offer main">
                                    <?php foreach($payments as $payment):
                                        if (isset($plane) && $plane['select_payments'] != null) {
                                            $selected = unserialize(base64_decode($plane['select_payments']));
                                            if(!in_array($payment['payment_id'], $selected)) {
                                                continue;
                                            }
                                        }
										
										// проверить платёжки для организации
                                        if(isset($_SESSION['org'])) {
                                            $payments_org = json_decode($_SESSION['org']['payments'], true);

                                            if($payments_org['cloud']['payment_id'] == $payment['payment_id']){
                                                if($payments_org['cloud']['enable'] != 1) continue;
                                            }

                                            if($payments_org['yookassa']['payment_id'] == $payment['payment_id']){
                                                if($payments_org['yookassa']['enable'] != 1) continue;
                                            }
                                        }?>

                                        <div class="order_item">
                                            <div class="order_item-left">
                                                <img src="<?=$setting['script_url'];?>/payments/<?=$payment['name']?>/<?=$payment['name']?>.png" alt="">
                                            </div>

                                            <div class="order_item-desc">
                                                <?php if($payment['public_title'] != null) echo '<h4>'.$payment['public_title'].'</h4>';?>
                                                <?=$payment['payment_desc'];?>
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

                    <?php if($list = $installment_list):?>
                        <div>
                            <div class="installment_pay offer main">
                                <h4 class="tabs-payment-subtitle"><?=System::Lang('CHOOSE_INSTALLMENT_VARIANT');?></h4>
                                <form class="install-form" id="install" action="/installment" method="POST">
                                    <?php require (__DIR__ .'/installment_list.php');?>
                                </form>
                            </div>
                        </div>
                    <?php endif;

                    if($list = $prepayment_list):?>
                        <div>
                            <div class="installment_pay offer main">
                                <form class="install-form" id="prepayment" action="/installment" method="POST">
                                    <?php require (__DIR__ .'/installment_list.php');?>
                                </form>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
<script src="<?=$setting['script_url'];?>/template/<?=$setting['template'];?>/js/tabs.js"></script>
</body>
</html>