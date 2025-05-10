<?php defined('BILLINGMASTER') or die;

class PostBacks {

    const ACT_TYPE_USER_REGISTRATION = 1;
    const ACT_TYPE_CREATE_ORDER = 2;
    const ACT_TYPE_PAY_ORDER = 3;

    public static function sendData($act, $partner_id, $client_name, $client_email, $client_phone, $user_id, $order_id = null, $order_date = null, $order_sum = null) {
        $req = Aff::getPartnerReq($partner_id);
        $params = $req && isset($req['postbacks']) ? json_decode($req['postbacks'], true) : null;

        if ($params) {
            $url = null;

            switch ($act) {
                case self::ACT_TYPE_USER_REGISTRATION:
                    $url = $params['register'];
                    break;
                case self::ACT_TYPE_CREATE_ORDER:
                    $url = $order_sum > 0 || !isset($params['only_paid']) ? $params['add_order'] : null;
                    break;
                case self::ACT_TYPE_PAY_ORDER:
                    $url = $order_sum > 0 || !isset($params['only_paid']) ? $params['pay_order'] : null;
                    break;
            }

            if ($url) {
                $replace = [
                    '{NAME}' => urlencode(trim($client_name)),
                    '{EMAIL}' => trim($client_email),
                    '{PHONE}' => trim($client_phone),
                    '{USER_ID}' => $user_id,
                    '{ORDER_ID}' => $order_id ? $order_id : '',
                    '{ORDER_NUM}' => $order_date ? $order_date : '',
                    '{SUMM}' => $order_sum !== null ? $order_sum : '',
                ];

                $url = strtr($url, $replace);
            }

            $result = System::curl($url);
        }
    }
}