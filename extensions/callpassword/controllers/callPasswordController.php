<?php defined('BILLINGMASTER') or die;

class callPasswordController {

    public function actionConfirmPhone() {
        $user_id = intval(User::checkLogged());
        $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
        $resp = [
            'status' => false,
            'confirm' => false
        ];

        if ($phone) {
            $settings = CallPassword::getSettings();
            $params = json_decode($settings, 1);
            $params = $params['params'];

            $api = new CallPasswordApi($params['api_key'], $params['sign_key'], $params['get_call_status'], $params['get_call_timeout']);
            $result = $api->confirmPhone($phone);

            if (is_array($result)) {
                $resp['status'] = 1;

                if ($result['confirm']) {
                    if ($user_id) {
                        $resp['status'] = User::confirmPhone($user_id, $phone);
                    } else {
                        $_SESSION['confirm_phone'] = $phone;
                    }
                }

                $resp['confirm'] = $result['confirm'];
            }
        }

        echo json_encode($resp);
    }
}
