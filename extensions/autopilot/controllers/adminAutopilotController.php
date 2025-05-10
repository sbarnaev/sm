<?php defined('BILLINGMASTER') or die;

class adminAutopilotController extends AdminBase {


    /**
     * НАСТРОЙКИ РАСШИРЕНИЯ
     */
    public static function actionSettings()
    {
        $autopilot = Autopilot::getSettings();

        $acl = self::checkAdmin();
        if (!isset($acl['show_users'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            unset($_POST['save'],$_POST['token']);

            if (!isset($acl['change_users'])) {
                header("Location: /admin");
                exit;
            }

            $edit = Autopilot::setSettings($_POST);
            if ($edit) {
                header("Location: /admin/autopilotsetting?success");
            }
        }

        require_once (__DIR__ . '/../views/admin/settings.php');
    }
}