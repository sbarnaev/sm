<?php defined('BILLINGMASTER') or die;

class Facebook {

    const PIXEL_CODE_PART_1 = "
        <!-- Facebook Integration Begin -->
        <script type='text/javascript'>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        %s
        fbq('track', 'PageView');
        </script>
        ";
    const PIXEL_CODE_PART_2 = "<noscript>
        <img height=\"1\" width=\"1\" style=\"display:none\" alt=\"fbpx\"
        src=\"https://www.facebook.com/tr?id=%s&ev=PageView&noscript=1\"/>
        </noscript>";
    

    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ
     * @return mixed
     */
    public static function getSettings()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT params FROM ".PREFICS."extensions WHERE name = 'facebookapi'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['params'] : false;
    }

    /**
     * ПОЛУЧИТЬ СТАТУС
     * @return mixed
     */
    public static function getStatus()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT enable FROM ".PREFICS."extensions WHERE name = 'facebookapi'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['enable'] : false;
    }

    /**
     * СОХРАНИТЬ НАСТРОЙКИ
     * @param $params
     * @param $status
     * @return bool
     */
    public static function saveSettings($params, $status = null) {
        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."extensions SET params = :params";
        $sql .= ($status !== null ? ', enable = '.intval($status) : '')." WHERE name = 'facebookapi'";
        
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }

     /**
     * ПЕРЕДАЕМ СОБЫТИЕ В ПИКСЕЛЬ FACEBOOK`а
     * если в fb_data ничего не пришло, 
     * то передаем базовое событие покупки
     * @param $partner_id
     * @param $order
     * @param $amount
     * @param $fb_data
     * @return bool
     */

    public static function eventsend2pixel($order, $amount, $partner_id = null, $fb_data = null) {
        
        $setting = System::getSetting();
        $settings = Facebook::getSettings();
        $params = unserialize($settings);
        $email_client = hash('SHA256', $order['client_email']);
        $url_from = addslashes($setting['script_url']);
        $product_id = $order['product_id'];

        $fb_data_base = '{"data": [
                {
                    "event_name": "Purchase",
                    "event_time": '.$order['order_date'].',
                    "action_source": "website",  
                    "user_data": {
                        "em": "'.$email_client.'",
                        "client_ip_address": "'.$order['ip'].'"
                    },
                    "custom_data": {
                        "currency": "'.$setting['currency'].'",
                        "value": "'.$amount.'",
                        "content_ids": "'.$product_id.'"
                    },
                    "event_source_url":"'.$url_from.'"
                }
             ]    
          }';

        $fb_data_send = empty($fb_data) ? $fb_data_base : $fb_data;
        
        //"test_event_code": "'.$params['params']['test_event_code'].'"
    
        $data = json_decode($fb_data_send, 1);
        
        if ($params['params']['pixel_id'] && $params['params']['access_token_fb']) {
            $url_fb = 'https://graph.facebook.com/v11.0/'.$params['params']['pixel_id'].'/events?access_token='.$params['params']['access_token_fb'];
            $ch = curl_init();
            $data_cur = json_encode($data);
            curl_setopt($ch, CURLOPT_URL, $url_fb);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_cur);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
        }

        // Если есть партнер, то отправляем событие и ему
        if ($partner_id) {
            $partner = Aff::getPartnerReq($partner_id);
            $fb_pixel_part = json_decode($partner['fb_pixel'], true);
            if ($fb_pixel_part['pixel_fb_id'] && $fb_pixel_part['access_token_fb']) {
                $url_fb_part = 'https://graph.facebook.com/v11.0/'.$fb_pixel_part['pixel_fb_id'].'/events?access_token='.$fb_pixel_part['access_token_fb'];
                $ch = curl_init();
                $data_cur = json_encode($data);
                curl_setopt($ch, CURLOPT_URL, $url_fb_part);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_cur);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                $result = curl_exec($ch);
                curl_close($ch);
            }
        }

      }

    public static function getPixelCode() {

        $pixel_code_1 = '';
        $pixel_code_2 = '';
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        $settings = Facebook::getSettings();
        $params = unserialize($settings);
        if ($params['params']['pixel_id']){
            $pixel_code_1 = "fbq('init', '".$params['params']['pixel_id']."');";
            $pixel_code_2 = sprintf(self::PIXEL_CODE_PART_2, $params['params']['pixel_id']);
        }
        if ((isset($_COOKIE["aff_$cookie"])) || (isset($_GET['pr']))) {
            $partner_id = isset($_GET['pr']) ? intval($_GET['pr']) : intval($_COOKIE["aff_$cookie"]);
            $partner = Aff::getPartnerReq($partner_id);
            $fb_pixel_part = json_decode($partner['fb_pixel'], true);
            if ($fb_pixel_part['pixel_fb_id']) {
                $pixel_code_1 =  $pixel_code_1 . "fbq('init', '".$fb_pixel_part['pixel_fb_id']."');";
                $pixel_code_2 = $pixel_code_2 . sprintf(self::PIXEL_CODE_PART_2, $fb_pixel_part['pixel_fb_id']);
            }
            
        }
        return sprintf(self::PIXEL_CODE_PART_1, $pixel_code_1) . $pixel_code_2 . '<!-- Facebook Integration End -->';
    }

}