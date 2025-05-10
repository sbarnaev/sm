<?php defined('BILLINGMASTER') or die;

if (class_exists('SoapClient')) {
    $payment_name = 'poscredit';
    $payment = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($payment['params']));
    $access_id = trim($params['secret_key']);
    $user_id = trim($params['user_id']);
    $user_token = trim($params['user_token']);

    if ($user_id && $user_token) {
        $posCredit = new PosCredit();
        $pc_orders = $posCredit->getOrders2SaveStatus();
        if ($pc_orders) {
            $url = 'https://api.b2pos.ru/loan/wsdl.php';
            $client = new SoapClient($url, ['soap_version' => SOAP_1_2]);

            foreach ($pc_orders as $pc_order) {
                $sh_param = [
                    'userID' => $user_id, // Код партнера
                    'userToken' => $user_token, // Хэш-пароль партнера
                    'orderID' => $pc_order['order_id'],
                ];

                if ($pc_order['profile_id']) {
                    $sh_param['profileID'] = $pc_order['profile_id'];
                }
                $result = $client->StatusSelectedOpty($sh_param);
                $status = (int)$result->status;

                if ($status && $pc_order['status'] != $status) {
                    $profile_id = (int)$result->profileID;
                    $bank = htmlentities($result->bankName);
                    $posCredit->updData($pc_order['id'], $profile_id, $status, $bank);
                    $order = Order::getOrder($pc_order['order_id']);
                    $sum = Order::getOrderTotalSum($order['order_id']);

                    $subject = 'Изменение статуса заявки на рассрочку';
                    $replace = [
                        '[CLIENT_NAME]' => $order['client_name'],
                        '[ORDER_ID]' => $order['order_date'],
                        '[PROFILE_ID]' => $profile_id,
                        '[CLIENT_EMAIL]' => $order['client_email'],
                        '[ORDER_SUM]' => $sum,
                        '[CURRENCY]' => $setting['currency'],
                        '[OLD_STATUS]' => $posCredit->getStatusText($order['pc_status']),
                        '[NEW_STATUS]' => $posCredit->getStatusText($status),
                        '[LINK_INFO]' => "{$setting['script_url']}/order-info/{$order['order_date']}?profile_id={$profile_id}&client_email={$order['client_email']}",
                    ];
                    $letter = strtr($params['letter2status_change'], $replace);
                    $send = Email::SendMessageToBlank($order['client_email'], $order['client_name'], $subject, $letter);

                    if ($result->authStatus == 1) {
                        $products = (array)$result->goods;
                        $total = 0;
                        if ($products) {
                            foreach ($products as $product) {
                                $total += $product->price;
                            }
                        }

                        if ($total >= $sum && Order::getOrderDataByID($order['order_id'], 100)) {
                            $render = Order::renderOrder($order, $payment['payment_id']);
                        }
                    }
                } else {
                    $posCredit->writeError($result->error);
                }
            }
        }
    }
}
