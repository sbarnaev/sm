<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once(dirname(__FILE__) . '/../../components/db.php');
require_once(dirname(__FILE__) . '/../../config/config.php');

$root = dirname(__FILE__) . '/../../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');


$setting = System::getSetting();
$cookie = $setting['cookie'];
    
$payment_name = 'yakassapi';
$yakassa = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($yakassa['params']));

$order = isset($_POST['orderNumber']) ? Order::getOrderToAdmin(intval($_POST['orderNumber'])) : null;
if (!$order) {
    exit('order not found');
}
    

require (ROOT .'/lib/yakassa_sdk/autoload.php');

use YandexCheckout\Client;//Импортируем класс

$client = new Client();//Создаём экземпляр объекта
$client->setAuth($params['ya_shop_id'], $params['api_key']);
$idempotenceKey = uniqid('', true);//Создаём идентификатор платежа

$currency = $params['currency'];    
$summ = Order::getOrderTotalSum($order['order_id']);
$order_date = $order['order_date'];
$return_url = $setting['script_url'];

$order_items = Order::getOrderItems($order['order_id']);
if (!$order_items) {
    exit('Items no found');
}

$description = "Оплата заказа №$order_date";
$pay_data = array(
    "capture" => true,//после оплаты клиент будет перенаправлен на этот url
    "amount" => array(
        "value" => $summ,
        "currency" => $currency,//Валюта
    ),
    "metadata" => array(
        "order_id" => $order['order_id'],
    ),
    'confirmation' => array(
        "type" => "redirect",
        "return_url" => $return_url,
    ),
    "description" => $description//Яндекс-касса не обязует Вас делать description уникальным для каждого заказа, тем не менее лучше это сделать
);

if ($params['online_kassa'] == 1) {
    $items = [];
    foreach($order_items as $item) {
        $items[] = array(
            "description" => $item['product_name'],
            "quantity" => "1",
            "amount" => array (
                "value" => $item['price'].'.00',//Сумма платежа
                "currency" => $currency,//Валюта
            ),
            "vat_code" => $params['vat_code'],
            "payment_mode" => "full_payment",
            "payment_subject" => "commodity",

        );
    }

    $pay_data['receipt'] = array(
        "customer" => array(
            "full_name" => $order['client_name'],
            "email" => $order['client_email'],
            "phone" => $order['client_phone'],
        ),
        "items" => $items,
    );
}
if (!empty($_POST['pay_method'])) {
    $pay_data['payment_method_data'] = array(
        "type" => $_POST['pay_method'],
    );
    if ($_POST['pay_method'] == 'mobile_balance') {
        $pay_data['confirmation']['type'] = 'external';
    }

    if (!empty($order['client_phone'])) {
        $client_phone = str_replace('+7', '7', $order['client_phone']);
        if (in_array($_POST['pay_method'], array('qiwi', 'mobile_balance', 'cash'))) {
            $pay_data['payment_method_data']['phone'] = $client_phone;
        } elseif ($_POST['pay_method'] == 'alfabank') {
            $pay_data['payment_method_data']['login'] = $client_phone;
        }
    }

    if ($_POST['pay_method'] == 'b2b_sberbank') {
        $rates = array(1 => 'without', 2 => '0', 3 => '10', 4 => '20', 5 => '10/110', 6 => '20/120');
        $pay_data['payment_method_data']['payment_purpose'] = array(
            'payment_purpose' => $description,
        );
        $pay_data['payment_method_data']['vat_data'] = array(
            'type' => 'calculated',
            'rate' => $rates[$params['vat_code']],
            'amount' => array(
                'value' => $summ,
                'currency' => $currency,
            ),
        );
    }
}

try {
    $response = $client->createPayment(
        $pay_data,
        $idempotenceKey//Уникальный ключ сгенерированный выше
    );
} catch (Exception $e) {
    exit($e->getMessage());
}


$confirmation_url = false;
if (isset($response->status) and ($response->status != "canceled") and isset($response->confirmation->confirmation_url) and $response->confirmation->confirmation_url) {
  $confirmation_url = $response->confirmation->confirmation_url;
  header("Location: $confirmation_url");
}
?>