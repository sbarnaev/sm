<?php defined('BILLINGMASTER') or die;


class adminExpertSenderController extends AdminBase {
    
    
    // НАСТРОЙКИ ExpertSender
    public function actionSettings()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_responder'])) {
            header("Location: /admin");
            exit;
        }

        $name = $_SESSION['admin_name'];
        
        require_once(__DIR__ . '/../models/expertsender.php');
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['show_responder'])){
                header("Location: /admin");
                exit();
            }

            $data = $_POST['expertsender'];
            $data['params']['api_url'] = rtrim($data['params']['api_url'], '/');
            $params = serialize($data);
            $status = trim($_POST['status']);

            $save = ExpertSender::saveSettings($params, $status);
            if ($save) {
                header("Location: /admin/expertsendersetting?success");
            }
        }
    
        $settings = ExpertSender::getSettings();
        $params = unserialize($settings);
        $enable = ExpertSender::getStatus();
        
        require_once (__DIR__ . '/../views/setting.php');
    }
}