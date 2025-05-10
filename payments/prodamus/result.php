<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once(dirname(__FILE__) . '/../../components/db.php');
require_once(dirname(__FILE__) . '/../../config/config.php');

$root = dirname(__FILE__) . '/../../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');

require_once 'Hmac.php';

// настройки Prodamusa
$payment_name = 'prodamus';
$robox = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($robox['params']));

$secret_key = $params['prodamus_secret_key'];
$headers = apache_request_headers();

try {
	if (empty($_POST)) {
		throw new Exception('$_POST is empty');
	}
	if (empty($headers['Sign'])) {
		throw new Exception('signature not found');
	}
	if (!Hmac::verify($_POST, $secret_key, $headers['Sign'])) {
		throw new Exception('signature incorrect');
	}
    if (!isset($_POST['payment_status'])) {
        throw new Exception( 'Ошибка передаваемых данных. Не указан результат оплаты.' );
    }
    
    if ($_POST['payment_status'] == 'success') {
        http_response_code(200);
        $inv_id = intval($_POST["order_num"]);
        $out_summ = $_POST["sum"];
        // Данные заказа
        $order = Order::getOrderDataByID($inv_id, 100);
        if ($order) {
            $order_items = Order::getOrderItems($order['order_id']);
        } else {
            exit('Не удалось получить данные заказа');
        }
        $total = 0; 
        foreach($order_items as $item){
            $total = $total + $item['price'];
        }
        $summ = explode(".", $out_summ);
        if ($summ[0] != $total) {
            exit('Wrong summ');
        }
        $render = Order::renderOrder($order, $robox['payment_id']);
    } else {
        throw new Exception('status');
    }
}
catch (Exception $e) {
	http_response_code($e->getCode() ? $e->getCode() : 400);
	printf('error: %s', $e->getMessage());
}

?>