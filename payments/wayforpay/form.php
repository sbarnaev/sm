<?php defined('BILLINGMASTER') or die;

require_once __DIR__ . '/wayforpay.php';

$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));


$codesCurrency = array(
    980 => 'UAH',
    840 => 'USD',
    978 => 'EUR',
    643 => 'RUB',
);

$mrh_merchant = $params['merchant'];

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

$w4p = new WayForPay();
$key = $params['secretkey'];
$w4p->setSecretKey($key);


$serviceUrl =  $setting['script_url'] . '/payments/wayforpay/result.php';
$returnUrl = $setting['script_url'] . '/payments/wayforpay/success.php';

$currency = $params['currency'];
$order_items = Order::getOrderItems($inv_id);

$amount = 0;
foreach($order_items as $item){
    $amount = $amount + $item['price'];
}

$productNames = array();
$productQty = array();
$productPrices = array();

//$products = $this->model_account_order->getOrderProducts($order_id);


foreach($order_items as $item){
    $productNames[] = str_replace(["'", '"', '&#39;', '&'], '', htmlspecialchars_decode($item['product_name']));
    $productPrices[] = round($item['price'], 2);
    $productQty[] = 1;

}
$ship_method = !empty($order['ship_method_id']) ? System::getShipMethod($order['ship_method_id']) : null;
if ($ship_method) {
    $productNames[] = str_replace(["'", '"', '&#39;', '&'], '', htmlspecialchars_decode($ship_method['title']));
    $productPrices[] = round($ship_method['tax'], 2);
    $productQty[] = 1;
}

$fields = [
    'orderReference' => $inv_id . WayForPay::ORDER_SEPARATOR . time(),
    'merchantAccount' => $params['merchant'],
    'orderDate' => $order['order_date'],
    'merchantAuthType' => 'simpleSignature',
    'merchantDomainName' => $_SERVER['HTTP_HOST'],
    'merchantTransactionSecureType' => 'AUTO',
    'amount' => $amount,
    'currency' => $currency,
    'serviceUrl' => $serviceUrl,
    'returnUrl' => $returnUrl,
    'productName' => $productNames,
    'productPrice' => $productPrices,
    'productCount' => $productQty,
    'clientFirstName' => 'testclientFirstName',
    'clientLastName' => 'testclientLastName',
    'clientEmail' => $order['client_email'],
    'clientPhone' => '',
    'clientCity' => '',
    'clientAddress' => '',
    'clientCountry' => '',
];
$fields['merchantSignature'] = $w4p->getRequestSignature($fields);

$data = [
    'fields' => $fields,
    'action' => WayForPay::URL,
];?>

<form action="<?=$data['action'];?>" method="post" id="payments">
    <?php foreach ($fields as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $vv) {
                echo "<input type=\"hidden\" name=\"{$k}[]\" value=\"{$vv}\" />";
            }
        } else {
            echo "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />";
        }
    }?>
    <input type="submit" class="payment_btn" value="Оплатить">
</form>
