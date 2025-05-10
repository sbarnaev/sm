<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));
$inv_desc = 'Оплата заказа №'.$order['order_date'];
$inv_id = $order['order_id'];
$amount = number_format($total, 2, '.', '');
$sign = md5($params['purse_number'].$amount.$inv_id.$params['test_mode'].$params['secret_key'].$order['client_email']);
?>

<form enctype="application/x-www-form-urlencoded" action="https://merchant.webmoney.ru/lmi/payment_utf.asp" accept-charset="UTF-8" method="POST"<?=$form_parameters?>>
    <input type="hidden" name="LMI_PAYEE_PURSE" value="<?=$params['purse_number'];?>">
    <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?=$amount;?>">
    <input type="hidden" name="LMI_PAYMENT_NO" value="<?=$inv_id;?>">
    <input type="hidden" name="LMI_PAYMENT_DESC" value="<?=$inv_desc;?>">
    <input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="<?=base64_encode($inv_desc);?>">
    <input type="hidden" name="LMI_SIM_MODE" value="<?=$params['sim_mode'];?>">
    <input type="hidden" name="LMI_MODE" value="<?=$params['test_mode'];?>">
    <input type="hidden" name="LMI_RESULT_URL" value="<?=$setting['script_url'] . "payments/webmoney/result.php";?>">
    <input type="hidden" name="LMI_SUCCESS_URL" value="<?=$setting['script_url'] . "payments/webmoney/success.php";?>">
    <input type="hidden" name="LMI_SUCCESS_METHOD" value="0">
    <input type="hidden" name="LMI_FAIL_URL" value="<?=$setting['script_url'] . "payments/webmoney/fail.php";?>">
    <input type="hidden" name="LMI_FAIL_METHOD" value="0">
    <input type="hidden" name="LMI_PAYMER_EMAIL" value="<?=$order['client_email'];?>">
    <input type="hidden" name="SBM_SIGN" value="<?=$sign;?>">
    <input type="hidden" name="SBM_EMAIL" value="<?=$order['client_email'];?>">
    <input type="hidden" name="SBM_DESC" value="<?=urlencode($inv_desc);?>">
    <input type="submit" class="payment_btn" value="Оплатить">
</form>