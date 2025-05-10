<?php


class ExpertSenderAPi {
    CONST LANGUAGE = 'ru-RU';
    private $secret_key;
    private $api_url;

    public function __construct($api_url, $secret_key) {
        $this->api_url = $api_url . (strpos($api_url, '/Api') === false ? '/Api/' : '/');
        $this->secret_key = $secret_key;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ПОДПИСОК
    public function getLists() {
        $data = $this->getData('Lists');
        return $data ? ((array)$data->Lists)['List'] : false;
    }
    
    
    public function getTemplates() {
        $data = $this->getData('Templates');
        return $data ? (array)$data->Templates : false;
    }
    
    
    public function getMessages() {
        $data = $this->getData('Messages');
        return $data ? (array)$data->Messages : false;
    }
    
    
    //ДОБАВИТЬ ПОДПИСЧИКА В ЛИСТ
    public function addSubscriber2list($list_id, $email, $name) {
        $xml = $this->getDefaultRequestXml();
        
        $xml->addChild('ReturnData', 'true');
        $xml->addChild('VerboseErrors', 'true');
        $dataXml = $xml->addChild('Data');
        $dataXml->addAttribute('xmlns:xsi:type', 'Subscriber');
        $dataXml->addChild('ListId', $list_id);
        $dataXml->addChild('Email', $email);
        if ($name) {
            $dataXml->addChild('Firstname', $name);
        }
    
        $url = $this->api_url . "Subscribers";
        $res = $this->curl($url, $xml);
        if ($res['code'] != 201) {
            self::writeError($res);
        }
        
        return $res;
    }
    
    
    private function getData($type) {
        $url = $this->api_url . "$type?apiKey=$this->secret_key";
        $response = $this->curl($url);

        if ($response['code'] == 200 && $response['response']) {
            $xmlstr = $response['response'];
            $xml = new SimpleXMLElement($xmlstr);
            
            return $xml->Data;
        } else {
            self::writeError($response);
        }
        
        return false;
    }
    
    
    private function curl($url, $xml = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_NOBODY,false);
        curl_setopt($ch,CURLOPT_HEADER,false);
       
        
        if (!empty($xml)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type' => 'text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        }
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        return array('response' => $response, 'code' => $info['http_code']);
    }
    
    
    private function getDefaultRequestXml()
    {
        $xmlObject = new \SimpleXMLElement('<ApiRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" />');
        $xmlObject->addChild('ApiKey', $this->secret_key);
        return $xmlObject;
    }
    
    private function writeError($res) {
        $xml = new SimpleXMLElement($res['response']);
        $msgs = (array)$xml->ErrorMessage;
        $msg = isset($msgs['Message']) ? $msgs['Message'] : trim(strip_tags($res['response']));
        $msg = str_replace("\n", ' ', $msg);

        $error = date('d.m.Y H:i:s', time()) . " error code {$res['code']} {$msg}\n";
        file_put_contents(__DIR__ . '/../log.txt', PHP_EOL . $error, FILE_APPEND);
    }
}