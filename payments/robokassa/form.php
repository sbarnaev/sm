<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' '.$metriks : '';

$params = unserialize(base64_decode($payment['params']));

$mrh_login = $params['login'];
$mrh_pass1 = $params['pass1'];

$inv_id = $order['order_id'];
$inv_desc = 'Оплата заказа №'.$order['order_date'];

$out_summ = $total.'.00';
$shp_item = "2";
$in_curr = "";
$culture = "ru";
$payment_method = isset($params['payment_method']) ? $params['payment_method'] :'full_prepayment';
$payment_object = isset($params['payment_object']) ? $params['payment_object'] : 'commodity';
$tax = isset($params['tax']) ? $params['tax'] : 'none';
$sno = isset($params['sno']) ? $params['sno'] : 'osn';
$pay_object_delivery = isset($params['pay_object_delivery']) ? $params['pay_object_delivery'] : 'commodity';

$items = array();
$order_items = Order::getOrderItems($inv_id);

foreach($order_items as $item){
    $items[] = [
        'name' => $item['product_name'],
        'quantity' => 1,
        'sum' => $item['price'] . '.00',
        'tax' => $tax,
        'payment_method' => $payment_method,
        'payment_object' => $payment_object
    ];
}
$ship_method = !empty($order['ship_method_id']) ? System::getShipMethod($order['ship_method_id']) : null;
if ($ship_method) {
    $items[] = [
        'name' => $ship_method['title'],
        'quantity' => 1,
        'sum' => $ship_method['tax'] . '.00',
        'tax' => $tax,
        'payment_method' => $payment_method,
        'payment_object' => $pay_object_delivery
    ];
}

$receipt = json_encode(
    array(
        'sno' => $sno,
        'items' => $items
    ),
JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
);

$crc  = md5("$mrh_login:$out_summ:$inv_id:$receipt:$mrh_pass1:Shp_item=$shp_item");
?>
<form enctype="application/x-www-form-urlencoded" action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST"<?=$form_parameters?>>
    <input type="hidden" name="MrchLogin" value="<?php echo $mrh_login;?>">
    <input type="hidden" name="OutSum" value="<?php echo $out_summ;?>">
    <input type="hidden" name="InvId" value="<?php echo $inv_id;?>">
    <input type="hidden" name="Desc" value="<?php echo $inv_desc;?>">
    <input type="hidden" name="SignatureValue" value="<?php echo $crc;?>">
    <input type="hidden" name="Shp_item" value="<?php echo $shp_item;?>">
    <input type="hidden" name="IncCurrLabel" value="<?php echo $in_curr;?>">
    <input type="hidden" name="Culture" value="<?php echo $culture;?>">
    <input type="hidden" name="email" value="<?php echo $order['client_email'];?>">
    <input type="hidden" name="Receipt" value="<?php echo urlencode($receipt)?>">
    <input type="submit" class="payment_btn" value="Оплатить">
</form>