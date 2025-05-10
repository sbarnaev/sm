<?php define('BILLINGMASTER', 1);

$paid_string = file_get_contents('php://input');

if (!empty($paid_string)) {
    parse_str($paid_string, $paid_data);

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // Настройки Paypal
    $payment_name = 'paypal';
    $paypal = Order::getPaymentSetting($payment_name);
    $setting = System::getSetting();
    $params = unserialize(base64_decode($paypal['params']));

    $order_id = intval($paid_data['item_number']);
    $order = $order_id ? Order::getOrderDataByID($order_id, 100) : null;
    if (!$order) exit('order not found');

    $tax = 0;
    if (!empty($order['ship_method_id'])) {
        $ship_method = System::getShipMethod($order['ship_method_id']);
        $tax = $ship_method['tax'];
    }

    $summ = (Order::getOrderTotalSum($order_id) + $tax) . '.00';
    $currency = trim($params['currency']);
    $receiver_email = trim($params['business']);

    if ($paid_data['mc_gross'] != $summ) {
        exit('wrong payment summ');
    } elseif ($paid_data['mc_currency'] != $currency) {
        exit('wrong payment currency');
    } elseif ($paid_data['receiver_email'] != $receiver_email) {
        exit('wrong receiver email');
    }

    if ($paid_data['payment_status'] == "Completed" && checkPayment($paid_string)) {
        // Обработка заказа
        $render = Order::renderOrder($order, $paypal['payment_id']);
    } elseif($paid_data['payment_status'] == "Pending") {
        $count = 0;
        $timer = 0;

        while ($count < 5) {
            if (checkPayment($paid_string)) {
                // Обработка заказа
                $render = Order::renderOrder($order, $paypal['payment_id']);
                break;
            }

            $count++;
            sleep($timer+=30);
        }
    }
}

function checkPayment($paid_string) {
    $result = sendRequest('https://www.paypal.com/cgi-bin/webscr', "cmd=_notify-validate&{$paid_string}");

    return strcmp($result, "VERIFIED") == 0 ? true : false;
}

function sendRequest($url, $req) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv 11.0) like Gecko'));

    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}
?>