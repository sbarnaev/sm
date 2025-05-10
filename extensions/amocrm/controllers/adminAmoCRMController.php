<?php defined('BILLINGMASTER') or die;

class adminAmoCRMController extends AdminBase {
    
    // НАСТРОЙКИ AmoCRM
    public function actionSettings() {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        if (!isset($acl['show_orders'])) {
            System::redirectUrl('/admin');
        }
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {//если POST
            if (!isset($acl['show_orders'])) {
                System::redirectUrl('/admin');
            }
        
            $params = serialize($_POST['amocrm']);
            $status = trim($_POST['status']);
            $save = AmoCRM::saveSettings($params, $status);

            System::redirectUrl('/admin/amocrmsetting', $save);
        }
        
        $main_settings = System::getSetting();
        $settings = AmoCRM::getSettings();
        $params = unserialize($settings);
        $enable = AmoCRM::getStatus();

        require_once (__DIR__ . '/../../../vendor/autoload.php');
        $api = new AmoCRMApi($main_settings, $params);
        
        if ($api->auth()) {
            $pipelines = $api->getPipelines();

            if ($params['params']['pip_acc_stat']) {
                $stages_acc_stat = $api->getStatuses($params['params']['pip_acc_stat']);
            }
            if ($params['params']['pip_acc_pay']) {
                $stages_acc_pay = $api->getStatuses($params['params']['pip_acc_pay']);
            }
            if (isset($params['params']['pip_give_fp']) && $params['params']['pip_give_fp']) {
                $stages_give_fp =  $api->getStatuses($params['params']['pip_give_fp']);
            }
            if (isset($params['params']['pip_instlmnt_pay']) && $params['params']['pip_instlmnt_pay']) {
                $stages_instlmnt_pay = $api->getStatuses($params['params']['pip_instlmnt_pay']);
            }
            if (isset($params['params']['pip_gen_trial']) && $params['params']['pip_gen_trial']) {
                $stages_gen_trial = $api->getStatuses($params['params']['pip_gen_trial']);
            }
            if (isset($params['params']['pip_debtors_instlmnt']) && $params['params']['pip_debtors_instlmnt']) {
                $stages_debtors_instlmntl = $api->getStatuses($params['params']['pip_debtors_instlmnt']);
            }
        }
        
        require_once (__DIR__ . '/../views/setting.php');
    }
    
    public function actionOauth() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }
    
        require_once (__DIR__ . '/../../../vendor/autoload.php');
    
        $main_settings = System::getSetting();
        $settings = AmoCRM::getSettings();
        $params = unserialize($settings);
        
        $api = new AmoCRMApi($main_settings, $params);
        $api_client = $api->getApiClient();
        
        if (isset($_GET['referer'])) {
            $api_client->setAccountBaseDomain($_GET['referer']);
        }
        
        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
            
            if (isset($_GET['button'])) {
                $content = $api_client->getOAuthClient()->getOAuthButton([
                    'title' => 'Установить интеграцию',
                    'compact' => true,
                    'class_name' => 'className',
                    'color' => 'default',
                    'error_callback' => 'handleOauthError',
                    'state' => $state,
                ]);
                
                exit($content);
            } else {
                $authorizationUrl = $api_client->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
            }
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
    
        try {
            $access_token = $api_client->getOAuthClient()->getAccessTokenByCode($_GET['code']);
        
            if (!$access_token->hasExpired()) {
                $data = array(
                    'accessToken' => $access_token->getToken(),
                    'refreshToken' => $access_token->getRefreshToken(),
                    'expires' => $access_token->getExpires(),
                    'baseDomain' => $api_client->getAccountBaseDomain(),
                );
                $api->saveToken($data);
            }
        
            header('Location: /admin/amocrmsetting');
        } catch (Exception $e) {
            die((string)$e);
        }
    }
    
    public function actionAjax() {
        if (isset($_POST['pip_type']) && isset($_POST['pip_id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            require_once (__DIR__ . '/../../../vendor/autoload.php');
            
            $main_settings = System::getSetting();
            $settings = AmoCRM::getSettings();
            $params = unserialize($settings);
    
            $api = new AmoCRMApi($main_settings, $params);
            if ($api->auth()) {
                $statuses = $api->getStatuses(intval($_POST['pip_id']));
            }
            
            $resp = array(
                'status' => true,
                'error' => false,
                'data' => isset($statuses) ? $statuses : null,
            );
            
            echo json_encode($resp);
        }
    }
}