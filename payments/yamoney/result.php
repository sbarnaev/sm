<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Яндекс денег
    $payment_name = 'yamoney';
    $yamoney = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($yamoney['params']));
    $secret_code = trim($params['secret_code']);

    if (!empty($_POST['label'])) {
        $data = [];

        foreach (explode('|', $_POST['label']) as $item) {
            list($key, $value) = explode(':', $item);
            $data[$key] = $value;
        }
    }

    $order_id = intval($data['order_id']);
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

    if ($summ != $_POST['withdraw_amount']) {
        exit('Wrong payment summ');
    } elseif($_POST['currency'] != 643) {
        exit('Wrong payment currency');
    }

    // Генерация ключа, для проверки подлинности пришедших к нам данных
    $hash = sha1(
        $_POST['notification_type'] . '&' .
        $_POST['operation_id'] . '&' .
        $_POST['amount'] . '&' .
        $_POST['currency'] . '&' .
        $_POST['datetime'] . '&' .
        $_POST['sender'] . '&' .
        $_POST['codepro'] . '&' .
        $secret_code . '&' .
        $_POST['label']
    );

    if ($_POST['sha1_hash'] == $hash && $_POST['codepro'] !== true && $_POST['unaccepted'] !== true) {
        // Рендерим заказ
        $render = Order::renderOrder($order, $yamoney['payment_id']);

        //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'Ответ Яденег на result', $yamoney['payment_id']);
    }
}