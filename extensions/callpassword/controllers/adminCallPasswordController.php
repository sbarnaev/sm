<?php defined('BILLINGMASTER') or die;

class adminCallPasswordController extends AdminBase {

    /**
     * НАСТРОЙКИ CallPassword
     */
    public function actionSettings() {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $data = $_POST['callpassword'];
            $data['params']['api_key'] = trim($data['params']['api_key']);
            $data['params']['sign_key'] = trim($data['params']['sign_key']);

            $params = json_encode($data);
            $status = intval($_POST['status']);
            $save = CallPassword::saveSettings($params, $status);
            
            if ($save) {
                Telegram::addSuccess('Успешно');
            }
            
            header('Location: /admin/callpasswordsetting');
            exit;
        }

        $enable = CallPassword::getStatus();
        $settings = CallPassword::getSettings();
        $params = json_decode($settings, 1);
        $group_list = User::getUserGroups();

        require_once (__DIR__ . '/../views/setting.php');
    }
}