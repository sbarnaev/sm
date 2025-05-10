<?php define('BILLINGMASTER', 1);

use esas\hutkigrosh\controllers\ControllerAddBill;
use esas\hutkigrosh\controllers\ControllerWebpayFormBM;
use esas\hutkigrosh\lang\TranslatorBM;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;
use esas\hutkigrosh\wrappers\OrderWrapperBM;
use esas\hutkigrosh\utils\Logger;

if (!empty($_POST)) {

    // Настройки Hutkigrosh
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');
    require_once(__DIR__ . '/lib/hutkigrosh/SimpleAutoloader.php');


    $payment_name = 'hutkigrosh';
    $hutkigrosh = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($hutkigrosh['params']));
    $sys_settings = System::getSetting();
    $settings = [
        'site_name' => $sys_settings['site_name'],
    ];
    $logger = Logger::getLogger("payment");


    $order = Order::getOrderDataByID((int)$_POST['order_id'], 0);
    if (!$order) {
        $logger->error("Order not found");
        header("Content-type: application/json; charset=utf-8");
        exit(json_encode(['error' => 'Ошибка при оплате: заказ не найден', JSON_UNESCAPED_UNICODE]));
    }

    try {
        $translator = new TranslatorBM();
        $configurationWrapper = new ConfigurationWrapperBM($params, $settings);
        $orderWrapper = new OrderWrapperBM($order, $configurationWrapper);// проверяем, привязан ли к заказу billid, если да, то счет не выставляем, а просто прорисовываем страницу

        if (empty($orderWrapper->getBillId())) {
            $controller = new ControllerAddBill($configurationWrapper, $translator);
            $addBillRs = $controller->process($orderWrapper);
        }


        $orderWrapper->getStatus();
        $completion_text = $configurationWrapper->cookCompletionText($orderWrapper);

        if ($configurationWrapper->isAlfaclickButtonEnabled()) {
            $alfaclick_billID = $orderWrapper->getBillId();
            $alfaclick_phone = $orderWrapper->getMobilePhone();
            $alfaclick_url = "/payments/hutkigrosh/alfaclick.php";
        }

        if ($configurationWrapper->isWebpayButtonEnabled()) {
            $controller = new ControllerWebpayFormBM($configurationWrapper);
            $webpayResp = $controller->process($orderWrapper);
            $webpay_form = $webpayResp->getHtmlForm();
            $webpay_status = $_REQUEST['webpay_status'];
        }

        require_once(ROOT . '/payments/hutkigrosh/completion.php');
    } catch (Throwable $e) {
        $logger->error("Exception:", $e);
        header("Content-type: application/json; charset=utf-8");
        exit(json_encode(['error' => "Ошибка при оплате: {$e->getMessage()}", JSON_UNESCAPED_UNICODE]));
    }
}

