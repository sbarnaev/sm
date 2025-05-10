<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));
$purse_number = $params['purse_number'];
$inv_desc = 'Оплата заказа №'.$order['order_date'];
$inv_id = $order['order_id'];
$form_type = 'shop';
$amount = number_format($total, 2, '.', '');
$need_fio = false;
$need_email = false;
$need_phone = false;
$need_address = false;
?>

<form enctype="application/x-www-form-urlencoded" action="https://yoomoney.ru/quickpay/confirm.xml" method="POST"<?=$form_parameters?>>
    <input type="hidden" name="receiver" value="<?=$purse_number?>">
    <input type="hidden" name="formcomment" value="<?=$inv_desc?>">
    <input type="hidden" name="label" value="order_id:<?=$inv_id?>|client_email:<?=$order['client_email']?>">
    <input type="hidden" name="quickpay-form" value="<?=$form_type?>">
    <input type="hidden" name="targets" value="<?=$inv_desc?>">
    <input type="hidden" name="sum" value="<?=$amount?>">
    <input type="hidden" name="need-fio" value="<?=$need_fio?>">
    <input type="hidden" name="need-email" value="<?=$need_email?>">
    <input type="hidden" name="need-phone" value="<?=$need_phone?>">
    <input type="hidden" name="need-address" value="<?=$need_address?>">
    <input type="hidden" name="successURL" value="<?=$setting['script_url']."/payments/yamoney/success.php"?>">
    <input type="submit" class="payment_btn" value="Оплатить">
</form>