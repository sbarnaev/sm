<?php defined('BILLINGMASTER') or die;

class CallPasswordApi {

    const API_URl = 'https://api.new-tel.net/';

    private $api_key;
    private $signature_key;
    private $get_call_status;
    private $get_call_timeout;
    private $curl_timeout;


    /**
     * УСТАНОВИТЬ ПАРАМЕТРЫ НАСТРОЕК
     * CallPasswordApi constructor.
     * @param $api_key
     * @param $signature_key
     * @param $get_call_status
     * @param $get_call_timeout
     */
    public function __construct($api_key, $signature_key, $get_call_status, $get_call_timeout) {
        $this->api_key = $api_key;
        $this->signature_key = $signature_key;
        $this->get_call_status = $get_call_status;
        $this->get_call_timeout = $get_call_timeout;
        $this->curl_timeout = $get_call_status ? $get_call_timeout + 5 : 30;
    }


    /**
     * ПОЛУЧИТЬ КЛЮЧ ЗАПРОСА
     * @param $methodName
     * @param $time
     * @param $accessKey
     * @param $params
     * @param $signatureKey
     * @return string
     */
    private function getRequestKey($methodName, $time, $accessKey, $params, $signatureKey) {
        $str = "$methodName\n$time\n$accessKey\n$params\n$signatureKey";
        $hash = "{$accessKey}{$time}" . hash('sha256',$str);

        return $hash;
    }


    /**
     *ПОЛУЧИТЬ РЕЗУЛЬТАТ ПОДТВЕРЖДЕНИЯ ТЕЛЕФОНА
     * @param $phone
     * @return array|bool
     */
    public function confirmPhone($phone) {
        $phone = str_replace('+', '', trim($phone));
        $pin = System::generateNums(5);
        $params = json_encode([
            'async' => '1',
            'dstNumber' => $phone,
            'pin' => $pin,
            'timeout' => $this->get_call_status ? $this->get_call_timeout : 20
        ]);

        $data = $this->getResponseData($params, 'call-password/start-password-call');
        if (!$data) {
            return false;
        }

        if ($data['status'] == 'success' && $data['data']['result'] == 'success' && $data['data']['callDetails']['pin'] == $pin) {
            $confirm = ['confirm' => false];

            if (!$data['data']['callDetails']['isValidNumber']) {
                return $confirm;
            }

            if ($this->get_call_status) {
                $params = json_encode([
                    'callId' => $data['data']['callDetails']['callId'],
                ]);

                $time = 0;
                while ($time < $this->get_call_timeout) {
                    sleep($time += 7);
                    $data = $this->getResponseData($params, 'call-password/get-password-call-status');
                    if (!$data) {
                        return false;
                    }

                    if ($data['status'] == 'success' && $data['data']['result'] == 'success') {
                        $confirm['confirm'] = $data['data']['callDetails']['status'] == 'answered' ? true : false;
                        break;
                    }
                }
            }

            return $confirm;
       } elseif($data['status'] == 'error' || $data['data']['result'] == 'error') {
            $error_msg = $data['status'] == 'error' ? $data['message'] : $data['data']['message'];
            $this->writeError($error_msg);
       }

       return false;
    }


    private function getResponseData($params, $method_name) {
        $time = time();
        $requestKey = $this->getRequestKey($method_name, $time, $this->api_key, $params, $this->signature_key);

        $url = self::API_URl . $method_name;
        $response = $this->curl($url, $requestKey, $params);

        if (!$response) {
            return false;
        }

        $data = json_decode($response, 1);
        if (!$data) {
            return false;
        }

        return $data;
    }


    /**
     * ОТПРАВИТЬ ДАННЫЕ
     * @param $url
     * @param $requestKey
     * @param $data
     * @return bool|string
     */
    private function curl($url, $requestKey, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $requestKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    /**
     * ЗАПИСАТЬ ЛОГ
     * @param $data
     */
    public static function writeError($error_msg) {
        $error = date('d.m.Y H:i:s', time()) . " Error: $error_msg";
        file_put_contents(__DIR__ . '/../log.txt', PHP_EOL . $error, FILE_APPEND);
    }
}