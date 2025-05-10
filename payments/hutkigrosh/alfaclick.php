<?php define('BILLINGMASTER', 1);
use esas\hutkigrosh\controllers\ControllerAlfaclick;
use esas\hutkigrosh\lang\TranslatorBM;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;

if (!empty($_REQUEST)) {

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
    $setting = System::getSetting();

    try {
        $controller = new ControllerAlfaclick(new ConfigurationWrapperBM($params, $setting), new TranslatorBM());
        $controller->process($_REQUEST['billid'], $_REQUEST['phone']);
    } catch (Throwable $e) {
        \esas\hutkigrosh\utils\Logger::getLogger("alfaclick")->error("Exception: ", $e);
    }
}