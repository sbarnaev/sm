<?php define('BILLINGMASTER', 1);

use esas\hutkigrosh\controllers\ControllerNotifyBM;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;

if (!empty($_REQUEST)) {

    // Настройки
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');
    require_once(__DIR__ . '/lib/hutkigrosh/SimpleAutoloader.php');



    // Настройки Hutkigrosh
    $payment_name = 'hutkigrosh';
    $hutkigrosh = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($hutkigrosh['params']));
    $setting = System::getSetting();
    $logger = Logger::getLogger("result");

    try {
        $billId = isset($_REQUEST['purchaseid']) ? $_REQUEST['purchaseid'] : null;
        if (!$billId) {
            exit;
        }

        $configurationWrapper = new ConfigurationWrapperBM($params, $setting);
        $controller = new ControllerNotifyBM($configurationWrapper);

        $billInfoRs = $controller->process($billId);
        if (!$billInfoRs) {
            $logger->error("Hutkigrosh bill info rs is null");
            exit;
        }
        
        $order_id = (int)$billInfoRs->getInvId();
        $order = $order_id ? Order::getOrderDataByID($order_id, 0) : null;
        if (!$order) {
            $logger->error("Can not find payments for order[$order_id]");
            exit;
        }

        $amount = Order::getOrderTotalSum($order_id);
        if ($controller->isStatusPayed() && $billInfoRs->getAmount() == $amount) {
            // Рендерим заказ
            $render = Order::renderOrder($order, $hutkigrosh['payment_id']);

            //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'Ответ Hutkigrosh на result', $hutkigrosh['payment_id']);
        }
    } catch (Throwable $e) {
        \esas\hutkigrosh\utils\Logger::getLogger("result")->error("Exception:", $e);
    }
}