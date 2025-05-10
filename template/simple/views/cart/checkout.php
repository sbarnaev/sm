<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$name = $email = $phone = null;

if (isset($_COOKIE['emnam'])) {
    $emnam = explode("=", htmlentities($_COOKIE['emnam']));
    if (isset($emnam[0])) {
        $email = $emnam[0];
    }

    if (isset($emnam[1])) {
        $name = $emnam[1];
    }
}

if (isset($is_auth) && $is_auth != false) {
    $user = User::getUserById($is_auth);
    $name = $user['user_name'];
    $email = $user['email'];
    $phone = $user['phone'];
}?>

<body class="cart-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>


    <div id="content">
        <div class="layout" id="checkout">
            <div class="container-cart" id="order_form">

                <ul class="container-crumbs">
                    <li class="crumbs-no-active crumbs-width-210"><em><span>1</span><a href="/cart"><?=System::Lang('CART');?></a></em></li>
                    <li class="two-active"><span>2</span><?=System::Lang('YOUR_DATES');?></li>
                    <li><em><span>3</span><?=System::Lang('PAYMENT_OPTION');?></em></li>
                </ul>

                <h2><?=System::Lang('ORDER_REGISTRATION');?></h2>

                <div class="order_data">
                    <h3><?=System::Lang('ITEM_ORDER');?>:</h3>

                    <div class="offer main">
                        <?php $total = 0;
                        $delivery = 0;
                        foreach($products as $product):
                            if ($product['type_id'] == 2) {
                                $delivery = 2;
                            }

                            $price = Price::getPriceinCatalog($product['product_id']);
                            $total += $price['real_price'];?>

                            <div class="order_item">
                                <div class="order_item-desc">
                                    <h4><?=$product['product_name'];?></h4>
                                </div>

                                <div class="order_item-price_box-right">
                                    <span class="font-bold"><?="{$price['real_price']} {$setting['currency']}";?></span>
                                    <a class="table-short-link__delete" href="<?=$setting['script_url'];?>/cart/del/<?=$product['product_id'];?>">
                                        <span class="icon-remove"></span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <hr>
                    <div class="payment-itogo">
                        <?=System::Lang('TOTAL_SUMM');?>: <?="{$total} {$setting['currency']}";?>
                    </div>
                </div>

                    <div class="payments_list">
                        <h3><?=System::Lang('YOUR_DATES');?>:</h3>
                            <form class="cart-form" action="" method="POST">
                            <ul class="cart-form-field">
                                <li class="cart-form-input-2"><label><?=System::Lang('YOUR_NAME');?>:</label> <input type="text" value="<?=$name?>" name="name" required="required"></li>
                                <li class="cart-form-input-2"><label><?=System::Lang('YOUR_EMAIL');?>:</label> <script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCI="));</script> value="<?=$email?>" required="required" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$"></li>

                                <?php if($setting['request_phone'] == 1):?>
                                    <li class="cart-form-input-2"><label><?=System::Lang('CLIENT_PHONE');?>:</label> <input type="text" name="phone" value="<?=$phone;?>" required="required"></li>
                                <?php endif; ?>

                                <?php if($delivery == 2):?>
                                    <li class="cart-form-input-2"><label><?=System::Lang('POSTCODE');?>:</label> <input type="text" name="index"></li>
                                    <li class="cart-form-input-2"><label><?=System::Lang('CITY');?>:</label> <input type="text" name="city" required="required"></li>
                                    <li class="cart-form-input-2"><label><?=System::Lang('ADDRESS');?>:</label> <input type="text" name="address" required="required"></li>
                                <?php endif; ?>

                                <li class="cart-form-input-2"><label><?=System::Lang('NOTE');?>:</label><textarea name="comment" rows="3" cols="49"></textarea></li>
                                <li>
                                    <label class="check_label">
                                        <input type="checkbox" name="politika" required="required">
                                        <span><?=System::Lang('LINK_CONFIRMED');?></span>
                                    </label>
                                    <input type="hidden" name="type_id" value="<?=$delivery;?>">
                                </li>
                                <li><input type="submit" class="order_button btn-blue" name="buy" value="<?=$total == 0 ? 'Скачать' : 'Заказать';?>"></li>
                            </ul>
                        </form>
                    </div>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>