<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    // Настройки
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');
	require_once(dirname(__FILE__) . '/../../lib/payments/modulbank/lib/fpayments.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Modulbank
    $payment_name = 'modulbank';
    $modulbank = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($modulbank['params']));
    $secret_key = trim($params['secret_key']);
    $merchant_id = trim($params['merchant_id']);

    $order_id = intval($_POST['order_id']);
    $order = !empty($order_id) ? Order::getOrderDataByID($order_id, 0) : null;
    if (!$order) {
        exit('order not found');
    }

    $amount = Order::getOrderTotalSum($order_id);
    if (!empty($order['ship_method_id'])) {
        $ship_method = System::getShipMethod($order['ship_method_id']);
        $amount += $ship_method['tax'];
    }
    $amount = number_format($amount, 2, '.', '');

    if ($_POST['merchant'] != $merchant_id) {
        exit('Wrong merchant ID');
    } elseif ($_POST['amount'] != $amount) {
        exit('Wrong payment amount');
    } elseif ($_POST['currency'] != $params['currency']) {
        exit('Wrong payment currency');
    }

    $PaymentForm = new FPayments\PaymentForm($merchant_id, $secret_key, $params['test_mode']);
    $is_signature = $PaymentForm->is_signature_correct($_POST);

    if ($_POST['state'] == 'COMPLETE' && $PaymentForm->is_signature_correct($_POST)) {

        // Рендерим заказ
        $render = Order::renderOrder($order, $modulbank['payment_id']);

        //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'Ответ Modulbank на result', $modulbank['payment_id']);
    }
}