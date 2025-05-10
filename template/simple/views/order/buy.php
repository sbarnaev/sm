<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$name = null; $email = null; $phone = null; $surname = null;

if(isset($_COOKIE['emnam'])){
    $emnam = explode("=", htmlentities(urldecode($_COOKIE['emnam'])));
    if(isset($emnam[0])) $email = $emnam[0];
    if(isset($emnam[1])) $name = $emnam[1];
    if(isset($emnam[2])) $phone = $emnam[2];
}
if($is_auth){
    $user = User::getUserById($is_auth);
    $name = $user['user_name'];
	$surname = $user['surname'];
    $email = $user['email'];
    $phone = $user['phone'];
}


$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('CREATE_ORDER');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'create_order', 'submit');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onsubmit="'.$ya_goal.$ga_goal.' return true;"';

?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');?>

<div id="order_form">
    <div class="container-cart">
		<form action="" method="POST"<?php echo $metriks;?>>
            <?php if($price['real_price'] > 0):?>
                <ul class="container-crumbs <?php if($related_products) echo ''; else echo 'container-crumbs-two-steps'?> ">
                    <li class="first-active"><span>1</span><?=System::Lang('YOUR_DATES');?></li>
                    <?php if($related_products) echo '<li><span>2</span>Корзина</li>';?>
                    <li><span><?php if($related_products) echo '3'; else echo '2';?></span><?=System::Lang('PAYMENT_OPTION');?></li>
                </ul>
                <h2><?=System::Lang('ORDER_REGISTRATION');?></h2>

                <h3><?=System::Lang('ITEM_ORDER');?></h3>

                <div class="cart-item">
                    <?php if(!empty($product['product_cover'])):?>
                        <div class="cart-item-left">
                            <img src="<?php echo $setting['script_url'];?>/images/product/<?php echo $product['product_cover'];?>" alt="<?php echo $product['img_alt'];?>">
                        </div>
                    <?php endif;?>                    

                    <div class="cart-item-right">
                        <h4 class="cart-item-name"><?=$product['product_name'];?></h4>
                        <?php if($product['product_desc'] != null):?>
                            <p><?=nl2br($product['product_desc']);?></p>
                        <?php endif;?>

                        <?php if (empty($product['price_minmax'])):?>
                            <span><?=System::Lang('COAST');?>
                            <?php if($price['real_price'] < $price['price']):?>
                                <span class="old_price"><?="{$price['price']} {$setting['currency']}";?></span>&nbsp;&nbsp;
                            <?php endif;?>
                            <span class="font-bold current_price"><?="{$price['real_price']} {$setting['currency']}";?></span>
                        <?php else:
                            $price_mas = explode(":", $product['price_minmax']);?>
                            <span><?=System::Lang('COAST');?>
                            <input style="max-width: 350px;" type="number" name="user_price" min="<?=$price_mas[0];?>" max="<?=$price_mas[1];?>"
                                   placeholder="Укажите вашу цену от <?=$price_mas[0];?> до <?=$price_mas[1];?>" value="<?php if(isset($_GET['price'])) echo htmlentities($_GET['price']);?>">
                            <?php endif;?>
                        </span>
                    </div>
                </div>
            <?php else:?>
                <?/*<h2>Бесплатный продукт</h2>*/?>
            <?php endif;?>

            <div class="cart-form">
                <?/* <h3>Ваши данные</h3> */?>
                <?php if($price['real_price'] == 0):?>
                    <div class="cart-item-left" style="align-self: flex-start;">
                        <?php if(!empty($product['product_cover'])):?>
                            <img src="<?php echo $setting['script_url'];?>/images/product/<?php echo $product['product_cover'];?>" alt="<?php echo $product['img_alt'];?>">
                        <?php endif;?>
                    </div>
                <?php endif;?>

                <div class="cart-item-right">
                    <h3><?php echo $product['product_name'];?></h3>

                    <ul class="cart-form-field">
                        <li class="cart-form-input"><label><?=System::Lang('YOUR_NAME');?></label> <input type="text" value="<?php echo $name?>" name="name" required="required"></li>

                        <?php if($setting['show_surname'] == 2):?>
                            <li class="cart-form-input"><label><?=System::Lang('YOUR_SURNAME');?></label> <input type="text" name="surname" value="<?php echo $surname;?>" required="required"></li>
                        <?php elseif($setting['show_surname'] == 1 && $price['real_price'] > 0):?>
                            <li class="cart-form-input"><label><?=System::Lang('YOUR_SURNAME');?></label> <input type="text" name="surname" value="<?php echo $surname;?>" required="required"></li>
                        <?php endif;?>

                        <li class="cart-form-input"><label><?=System::Lang('YOUR_EMAIL');?></label>
                            <?php if($setting['email_protection']):?>
                                <script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCI="));</script>value="<?php echo $email?>" required="required" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$">
                            <?php else:?>
                                <input type="email" name="email" value="<?php echo $email?>" required="required" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$">
                            <?php endif;?>
                        </li>

                        <?php if($setting['request_phone'] == 1):?>
                            <li class="cart-form-input"><label><?=System::Lang('YOUR_PHONE');?></label>
                                <input type="text" autocomplete="off" name="phone" value="<?php echo $phone;?>" required="required">

                            </li>
                        <?php endif; ?>

                        <?php if($price['real_price'] == 0 && $setting['show_telegram_nick'] > 1):?>
                            <li class="cart-form-input"><label><?=System::Lang('TELEGRAM');?></label> <input type="text" name="nick_telegram" <?php if($setting['show_telegram_nick'] == 3) echo 'required="required"';?>></li>
                        <?php elseif($price['real_price'] > 0 && $setting['show_telegram_nick'] > 0):?>
                            <li class="cart-form-input"><label><?=System::Lang('TELEGRAM');?></label> <input type="text" name="nick_telegram" <?php if($setting['show_telegram_nick'] == 3) echo 'required="required"';?>></li>
                        <?php endif;?>

                        <?php if($price['real_price'] == 0 && $setting['show_instagram_nick'] > 1):?>
                            <li class="cart-form-input"><label><?=System::Lang('INSTAGRAM_NIK');?></label> <input type="text" name="nick_instagram" <?php if($setting['show_instagram_nick'] == 3) echo 'required="required"';?>></li>
                        <?php elseif($price['real_price'] > 0 && $setting['show_instagram_nick'] > 0):?>
                            <li class="cart-form-input"><label><?=System::Lang('INSTAGRAM_NIK');?></label> <input type="text" name="nick_instagram" <?php if($setting['show_instagram_nick'] == 3) echo 'required="required"';?>></li>
                        <?php endif;?>

                        <?php if($product['type_id'] == 2):?>
                            <li class="cart-form-input"><label><?=System::Lang('POSTCODE');?></label> <input type="text" name="index"></li>
                            <li class="cart-form-input"><label><?=System::Lang('CITY');?></label> <input type="text" name="city" required="required"></li>
                            <li class="cart-form-input"><label><?=System::Lang('ADDRESS');?></label> <input type="text" name="address" required="required"></li>
                        <?php endif;?>

                        <?php if($setting['show_order_note'] == 1):?>
                            <li class="cart-form-input"><label><?=System::Lang('NOTE');?></label><textarea name="comment" rows="3" cols="49"></textarea></li>
                        <?php endif;?>

                        <li><label class="check_label">
                                <input type="checkbox" name="politika" required="required">
                                <?php if(!isset($_SESSION['org'])):?>
                                    <span><?=System::Lang('LINK_CONFIRMED');?></span>
                                <?php else:?>
                                    <span><?=System::Lang('LINK_CONFIRMED_2');?></span>
                                <?php endif;?>
                            </label>
                        </li>
                        <li><input type="hidden" name="time" value="<?=$date;?>">
                        <input type="hidden" name="token" value="<?=md5($id.'s+m'.$date);?>">
                        <input type="submit" class="order_button btn-blue" name="buy" value="Продолжить"></li>
                    </ul>
                </form>
                <?php if($product['price']> 0):
                    require_once (__DIR__.'/../common/add_promo_code.php');
                endif;?>
            </div>
        </div>
    </div>
</div>


<?php // Разделение финпотоков
if(isset($_SESSION['org'])):?>
<div id="custom_doc" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-lightbox uk-slidenav-position" style="padding:20px">
        <a href="#" class="uk-modal-close uk-close uk-close-alt"></a>
        <?=$_SESSION['org']['oferta'];?>
    </div>
</div>
<?php endif;?>

<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>

</body>
</html>