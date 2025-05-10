<?php defined('BILLINGMASTER') or die;

class adminFacebookController extends AdminBase {

    /**
     * НАСТРОЙКИ FACEBOOK
     */
    public function actionSettings() {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        if (!isset($acl['change_users'])) {
            header("Location: /admin");
            exit;
        }

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            
            $params = serialize($_POST['facebook']);
            $status = intval($_POST['status']);
            $save = Facebook::saveSettings($params, $status);

            if ($save) {
                header('Location: /admin/facebooksetting' . ($save ? '?success' : ''));
            }
        }
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            
            $params = serialize($_POST['facebook']);
            $status = intval($_POST['status']);
            $save = Facebook::saveSettings($params, $status);

            if ($save) {
                header('Location: /admin/facebooksetting' . ($save ? '?success' : ''));
            }
        }

        $enable = Facebook::getStatus();
        $settings = Facebook::getSettings();
        $params = unserialize($settings);

        require_once (__DIR__ . '/../views/setting.php');
    }
    
    public function actionTestbutton() {

        $tec = isset($_POST['tec']) ? $_POST['tec'] : '' ;
        $settings = Facebook::getSettings();
        $setting = System::getSetting();
        $params = unserialize($settings);
        $email_client = hash('SHA256', $setting["admin_email"]);
        $url_from = addslashes($setting['script_url']);
        $ip_cur = $_SERVER['REMOTE_ADDR'];

        $t = '{"data": [
                {
                    "event_name": "Purchase",
                    "event_time": '.time().',
                    "action_source": "website",  
                    "user_data": {
                        "em": "'.$email_client.'",
                        "client_ip_address": "'.$ip_cur.'",
                        "client_user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36"
                    },
                    "custom_data": {
                        "currency": "RUB",
                        "value": "100.00",
                        "content_ids": "1"
                    },
                    "event_source_url":"'.$url_from.'"
                }
            ],
            "test_event_code": "'.$tec.'"    
          }';

        $data = json_decode($t, 1);
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
 
}