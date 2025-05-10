<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Fondy
    $payment_name = 'fondy';
    $fondy = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($fondy['params']));
    $secret_key = trim($params['secret_key']);
    $merchant_id = trim($params['merchant_id']);

    $setting = System::getSetting();

    $send_data = array(
        'order_id' => $_POST['order_id'],
        'merchant_id' => $merchant_id,
        'order_desc' => $_POST['order_desc'],
        'amount' => $_POST['order_amount'],
        'currency' => $params['currency'],
        'server_callback_url' => $setting['script_url'] . '/payments/fondy/result.php',
        'response_url' => $setting['script_url'] . '/payments/fondy/success.php',
        'lang' => $setting['lang'],
        'sender_email' => $_POST['client_email'],
    );

    $signature = getSignature($secret_key, $send_data);
    $send_data['signature'] = $signature;

    try {
        $url = fondyCheckout($send_data);
        header("Location: $url");
    } catch (Exception $e) {
        exit($e->getMessage());
    }
}

function getSignature($secret_key, $params) {
    $params = array_filter($params,'strlen');
    ksort($params);
    $params = array_values($params);
    array_unshift($params , $secret_key);
    $params = implode('|',$params);

    return(sha1($params));
}

function fondyCheckout($args) {
    if (is_callable('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.fondy.eu/api/checkout/url/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request' => $args)));

        $result = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200 && $result->response->response_status != 'failure') {
            return $result->response->checkout_url;
        } else {
            throw new Exception($result->response->error_message);
        }
    }
}