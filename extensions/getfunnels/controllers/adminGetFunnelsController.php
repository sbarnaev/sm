<?php

defined('BILLINGMASTER') or die;

class adminGetFunnelsController extends AdminBase {
    
    // НАСТРОЙКИ GetFunnels
    public function actionSettings() {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }
    
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {//если POST
            $params = serialize($_POST['getfunnels']);
            $status = trim($_POST['status']);
            $save = GetFunnels::saveSettings($params, $status);
    
            header('Location: /admin/getfunnelssetting' . ($save ? '?success' : ''));
        }

        $enable = GetFunnels::getStatus();
        $settings = GetFunnels::getSettings();
        $params = unserialize($settings);


        require_once (__DIR__ . '/../views/setting.php');
    }
}