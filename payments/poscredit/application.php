<?php define('BILLINGMASTER', 1);

if (!empty($_POST) && isset($_POST['order_id'])) {

    // Настройки PosCredit
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);
    require_once (ROOT . '/components/autoload.php');

    $pos_credit = new PosCredit();
    $order_id = (int)$_POST['order_id'];
    $client_status = isset($_POST['client_status']) ? (int)$_POST['client_status'] : null;
    $profile_id = isset($_POST['profile_id']) ? (int)$_POST['profile_id'] : null;
    $res = false;

    if ($profile_id && $pos_credit->countOrders($order_id, $profile_id) == 0) { // сохранение айди заявки
        $res = $pos_credit->addOrder($order_id, $profile_id);
    } elseif($client_status) { // сохранение стутуса по заявке
        $res = $pos_credit->saveStatusClient($order_id, $client_status);
    }

    echo json_encode(['status' => $res]);
}
