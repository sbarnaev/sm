<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    if (empty($_POST['LMI_PAYMENT_NO'])) {
        exit('Wrong payment id');
    }

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Webmoney
    $payment_name = 'webmoney';
    $webmoney = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($webmoney['params']));
    $secret_key = trim($params['secret_key']);

    $order_id = intval($_POST['LMI_PAYMENT_NO']);
    $order = !empty($order_id) ? Order::getOrderDataByID($order_id, 100) : null;
    if (!$order) {
        exit('order not found');
    }

    $summ = Order::getOrderTotalSum($order_id);
    $ship_method = !empty($order['ship_method_id']) ? System::getShipMethod($order['ship_method_id']) : null;
    if ($ship_method) {
        $summ += $ship_method['tax'];
    }

    $summ = number_format($summ, 2, '.', '');
    if ($_POST['LMI_PAYMENT_AMOUNT'] != $summ) {
        exit('wrong payment amount');
    }

    $hash = hash("sha256",
        $_POST['LMI_PAYEE_PURSE'].
        $_POST['LMI_PAYMENT_AMOUNT'].
        $_POST['LMI_PAYMENT_NO'].
        $_POST['LMI_MODE'].
        $_POST['LMI_SYS_INVS_NO'].
        $_POST['LMI_SYS_TRANS_NO'].
        $_POST['LMI_SYS_TRANS_DATE'].
        $params['secret_key'].
        $_POST['LMI_PAYER_PURSE'].
        $_POST['LMI_PAYER_WM']
	);

    if ($_POST["LMI_PAYEE_PURSE"] == $params['purse_number'] && $_POST["LMI_HASH"] == $hash) {
        // Рендерим заказ
        $render = Order::renderOrder($order, $webmoney['payment_id']);

        //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'Ответ Webmoney на result', $webmoney['payment_id']);
    }
}