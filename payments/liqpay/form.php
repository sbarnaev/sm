<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

// настройки LiqPay
$payment_name = 'liqpay';
$liqpay = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($liqpay['params']));
$public_key = trim($params['public_key']);
$private_key = trim($params['private_key']);
$currency = $params['currency'];
$inv_desc = 'Order '.$order['order_date'];

$data = base64_encode(
    json_encode(
        array(
            'version' => '3',
            'public_key' => $public_key,
            'action' => 'pay',
            'amount' => $total,
            'currency' => $currency,
            'description' => $inv_desc,
            'order_id' => $order['order_id'],
            'language'    => $setting['lang'],
            'result_url' => $setting['script_url'] . '/payments/liqpay/success.php',
            'server_url' => $setting['script_url'] . '/payments/liqpay/result.php',
        )
    )
);
$signature = base64_encode(sha1($private_key.$data.$private_key, 1));
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"' . $metriks : '';
?>

<form enctype="application/x-www-form-urlencoded" accept-charset='UTF-8' method="POST" action="https://www.liqpay.ua/api/3/checkout"<?=$form_parameters;?>>
    <input type="hidden" name="data" value="<?=$data?>" />
    <input type="hidden" name="signature" value="<?=$signature?>" />
    <button type="submit" class="payment_btn">Оплатить</button>
</form>
