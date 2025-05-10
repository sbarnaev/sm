<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');
    

    // настройки Тинькофф
    $payment_name = 'tinkoff';
    $tinkoff = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($tinkoff['params']));
    $setting = System::getSetting();
    $terminal_key = $params['terminal_key'];
    $secret_key = $params['secret_key'];

    $taxations = array(
        'osn' => 'osn',                // Общая СН
        'usn_income' => 'usn_income',         // Упрощенная СН (доходы)
        'usn_income_outcome' => 'usn_income_outcome', // Упрощенная СН (доходы минус расходы)
        'envd' => 'envd',               // Единый налог на вмененный доход
        'esn' => 'esn',                // Единый сельскохозяйственный налог
        'patent' => 'patent'              // Патентная СН
    );
    $payment_method = array(
        'full_prepayment' => 'full_prepayment', //Предоплата 100%
        'prepayment' => 'prepayment',      //Предоплата
        'advance' => 'advance',         //Аванc
        'full_payment' => 'full_payment',    //Полный расчет
        'partial_payment' => 'partial_payment', //Частичный расчет и кредит
        'credit' => 'credit',          //Передача в кредит
        'credit_payment' => 'credit_payment',  //Оплата кредита
    );
    $payment_object = array(
        'commodity' => 'commodity',             //Товар
        'excise' => 'excise',                //Подакцизный товар
        'job' => 'job',                   //Работа
        'service' => 'service',               //Услуга
        'gambling_bet' => 'gambling_bet',          //Ставка азартной игры
        'gambling_prize' => 'gambling_prize',        //Выигрыш азартной игры
        'lottery' => 'lottery',               //Лотерейный билет
        'lottery_prize' => 'lottery_prize',         //Выигрыш лотереи
        'intellectual_activity' => 'intellectual_activity', //Предоставление результатов интеллектуальной деятельности
        'payment' => 'payment',               //Платеж
        'agent_commission' => 'agent_commission',      //Агентское вознаграждение
        'composite' => 'composite',             //Составной предмет расчета
        'another' => 'another',               //Иной предмет расчета
    );
    $vats = array(
        'none' => 'none', // Без НДС
        'vat0' => 'vat0', // НДС 0%
        'vat10' => 'vat10',// НДС 10%
        'vat20' => 'vat20' // НДС 20%
    );

    $inv_id = intval($_POST['order_id']);
    $order_items = Order::getOrderItems($inv_id);
    $amount = 0;
    $receipt_items = array();

    foreach ($order_items as $order_item) {
        $price = $order_item['price'] * 100;

        $receipt_items[] = array(
            'Name' => $order_item['product_name'],
            'Price' => $price,
            'Quantity' => '1.00',
            'Amount' => $price,
            'PaymentMethod' => isset($params['payment_method']) ? $params['payment_method'] : $payment_method['full_prepayment'],
            'PaymentObject' => isset($params['payment_object']) ? $params['payment_object'] : $payment_object['commodity'],
            'Tax' => isset($params['tax_type']) ? $vats[$params['tax_type']] : $vats['none'],
        );

        $amount += $price;
    }

    $shim_method_id = intval($_POST['ship_method_id']);
    $ship_method = System::getShipMethod($shim_method_id);

    if ($ship_method['tax'] != 0) {
        $price = $ship_method['tax'] * 100;

        $receipt_items[] = array(
            'Name' => $ship_method['ship_desc'],
            'Price' => $price,
            'Quantity' => '1.00',
            'Amount' => $price,
            'PaymentMethod' => isset($params['payment_method']) ? $params['payment_method'] : $payment_method['full_prepayment'],
            'PaymentObject' => isset($params['payment_object']) ? $params['payment_object'] : $payment_object['commodity'],
            'Tax' => $vats['none'],
        );

        $amount += $price;
    }

    $payment_data = array(
        'OrderId' => $inv_id,
        'Amount' => $amount,
        'Description' => $_POST['order_desc'],
        'Language' => 'ru',
        'BackUrl' => $setting['script_url']."/payments/$payment_name/success.php",
        'DATA' => array(
            'Name' => $_POST['client_name'],
            'Email' => $_POST['client_email'],
            'Connection_type' => 'Billing Master',
        ),
    );
    if ($_POST['client_phone']) {
        $payment_data['DATA']['Phone'] = $_POST['client_phone'];
    }

    $payment_data['Receipt'] = array(
        'EmailCompany' => $setting['admin_email'],
        'Email' => $_POST['client_email'],
        'Phone' => $_POST['client_phone'],
        'Taxation' => isset($params['SNO']) ? $taxations[$params['SNO']] : $taxations['osn'],
        'Items' => $receipt_items,
    );

    // Совершаем платеж
    $api = new TinkoffMerchantAPI($terminal_key, $secret_key);
    $api->init($payment_data);

    if ($api->error) {
        exit($api->error);
    } else {
        header('Location: '.$api->paymentUrl);
    }
}