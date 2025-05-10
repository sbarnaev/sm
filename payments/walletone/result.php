<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {
    if (!isset($_POST["WMI_SIGNATURE"])) {
        exit(show_answer('Retry', 'Отсутствует параметр WMI_SIGNATURE'));
    } elseif (!isset($_POST["WMI_PAYMENT_NO"])) {
        exit(show_answer('Retry', 'Отсутствует параметр WMI_PAYMENT_NO'));
    } elseif (!isset($_POST["WMI_ORDER_STATE"])) {
        exit(show_answer('Retry', 'Отсутствует параметр WMI_ORDER_STATE'));
    }

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Walletone
    $payment_name = 'walletone';
    $walletone = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($walletone['params']));

    list($order_id, $order_date) = explode('-', $_POST['WMI_PAYMENT_NO']);
    $order_id = intval($order_id);
    $order = !empty($order_id) ? Order::getOrderDataByID($order_id, 100) : null;
    if (!$order) {
        exit(show_answer('Retry', 'Заказ с таким ID отсутствует или уже оплачен'));
    }

    $amount = Order::getOrderTotalSum($order_id);
    if (!empty($order['ship_method_id'])) {
        $ship_method = System::getShipMethod($order['ship_method_id']);
        $amount += $ship_method['tax'];
    }
    $amount = number_format($amount, 2, '.', '');

    if ($_POST['WMI_PAYMENT_AMOUNT'] != $amount) {
        exit(show_answer('Retry', 'Оплаченная сумма не совпадает с суммой заказа'));
    }

    if ($_POST['WMI_CURRENCY_ID'] != $params['currency_id']) {
        exit(show_answer('Retry', 'Валюта оплаченной суммы не совпадает с валютой заказа'));
    }

    $data = array();
    foreach ($_POST as $name => $value) {
        if ($name !== "WMI_SIGNATURE") {
            $data[$name] = $value;
        }
    }
    uksort($data, "strcasecmp");

    $data_str = implode('', $data);
    $signature = base64_encode(pack("H*", md5($data_str . $params['secret_key'])));
    if ($_POST["WMI_SIGNATURE"] == $signature) {
        if ($_POST["WMI_ORDER_STATE"] == "Accepted") {
            // Рендерим заказ
            $render = Order::renderOrder($order, $walletone['payment_id']);

            //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'Ответ Walletone на result', $walletone['payment_id']);
            exit(show_answer('Ok', 'Order successfully processed'));
        } else {
            exit(show_answer('Retry', 'Неверное состояние '. $_POST["WMI_ORDER_STATE"]));
        }
    } else {
        exit(show_answer('Retry', 'Неверная подпись '. $_POST["WMI_SIGNATURE"]));
    }
}

function show_answer($result, $description) {
    return 'WMI_RESULT=' . strtoupper($result) . '&' . 'WMI_DESCRIPTION=' .$description;
}