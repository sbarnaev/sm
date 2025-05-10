<?php defined('BILLINGMASTER') or die;

class AcyMailing {

    /**
     * @param $params
     * @param $acy_key
     * @param $client_name
     * @param $product_name
     * @param $client_email
     * @param $order_date
     * @param null $pin_code
     */
    public static function sendData($params, $acy_key, $client_name, $client_email, $product_name, $order_date, $pin_code = null) {
        $acymailing = unserialize(base64_decode($params));

        if (!empty($acymailing[$acy_key])) {
            $site = rtrim($acymailing['site'], '/');
            $product_name = urlencode($product_name);
            $client_name = urlencode($client_name);
            $client_email = urlencode($client_email);
            $pin_code = $pin_code ? urlencode($pin_code) : '';
            $field_id_order_num = isset($acymailing['field_id_order_num']) ? $acymailing['field_id_order_num'] : 3;
            $field_id_product_name = isset($acymailing['field_id_product_name']) ? $acymailing['field_id_product_name'] : 4;
            $field_id_pin_code = isset($acymailing['field_id_pin_code']) ? $acymailing['field_id_pin_code'] : 5;

            if (!isset($acymailing['cms_type']) || $acymailing['cms_type'] == 1) { // Joomla
                if (isset($acymailing['version']) && $acymailing['version'] == 6) {
                    $url_acy = "/index.php?option=com_acym&ctrl=frontusers&task=subscribe&hiddenlists={$acymailing[$acy_key]}&user[name]=$client_name&user[email]=$client_email";
                } else {
                    $url_acy = "/index.php?option=com_acymailing&ctrl=sub&task=optin&hiddenlists={$acymailing[$acy_key]}&user[email]=$client_email&user[name]=$client_name&seckey={$acymailing['acy_key']}";
                }
            } else { // WordPress
                $url_acy = "/index.php?page=acymailing_front&ctrl=frontusers&task=subscribe&hiddenlists={$acymailing[$acy_key]}&user[email]={$client_email}&action=acymailing_frontrouter&noheader=1";
            }

            if (isset($acymailing['version']) && $acymailing['version'] == 6) {
                if (isset($acymailing['send_order_data'])) {
                    $url_acy .= $field_id_order_num ? "&customField[{$field_id_order_num}]=$order_date" : '';
                    $url_acy .= $field_id_product_name ? "&customField[{$field_id_product_name}]=$product_name" : '';
                }
                $url_acy .= $acy_key == 'acy_id2' && $field_id_pin_code ? "&customField[{$field_id_pin_code}]=$pin_code" : '';
            } else {
                if (isset($acymailing['send_order_data'])) {
                    $url_acy .= "&order_number=$order_date&product_name=$product_name";
                }
                $url_acy .= $acy_key == 'acy_id2' ? "&pin=$pin_code" : '';
            }

            $url = "{$site}{$url_acy}";
            System::curlAsync($url);
        }
    }
}
