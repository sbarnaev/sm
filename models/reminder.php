<?php defined('BILLINGMASTER') or die;


class Reminder {

    /**
     * ДОЖИМАЮЩИЕ (НАПОМИНАЮЩИЕ) ПИСЬМА ДЛЯ КЛИЕНТОВ
     * @param $setting
     */
    public static function remindClientLetter($setting) {
        /**
         *  Крон дёргает файл каждые 10 минут, файл получает настройки писем из настроек. [1] = 30m, [2] = 120m, [3] = 360m
         *  Шаг 1. Ищет неоплаченные заказы с remindLetter = 0 и датой заказа больше чем $now - 30m,
         *     отправляет письмо, обновляет remindLetter с 0 на 1 - 1 письмо отправлено.
         *
         *  Шаг 2. Если в настройках есть 2 письмо.
         *  Ищет неоплаченные заказы с remindLetter = 1 и датой заказа больше чем $now - 120m,
         *     отправляет письмо и обновляет с 1 до 2, т.е. отправлено 2-е письмо
         *
         *  Шаг 3. Если в настрйоках есть 3 письмо.
         *  Ищет неоплаченные заказы с remindLetter = 2 и временем больше чем $now - 360m,
         *
         */

        $now = time();
        $setting = System::getSetting();
        $prod_ids = '';

        self::sendRemindersWithIndividSettings($now, $setting, $prod_ids);
        self::sendReminders($now, $setting, $prod_ids);
    }


    /**
     * ОТПРАВКА НАПОМИНАНИЙ С ИНДИВИДУАЛЬНЫМИ НАСТРОЙКАМИ ДЛЯ ПРОДУКТОВ
     * @param $now
     * @param $setting
     * @param $prod_ids
     */
    private static function sendRemindersWithIndividSettings($now, $setting, &$prod_ids) {
        $prod_reminders = ProductReminder::getReminders(1);
        
        if ($prod_reminders) {
            foreach ($prod_reminders as $reminder) {
                $product_id = $reminder['product_id'];
                $prod_ids .= ($prod_ids ? ',' : '') . $product_id;

                for ($num = 1; $num <= 3; $num++) {
                    $remind_letter = unserialize(base64_decode($reminder["remind_letter$num"]));
                    self::sendLetters($remind_letter, $now, $num, $product_id);
                }
    
                for ($num = 1; $num <= 2; $num++) {
                    $remind_sms = unserialize(base64_decode($reminder["remind_sms$num"]));
                    self::sendSMS($setting, $now, $num, $remind_sms, $prod_ids);
                }
            }
        }
    }


    /**
     * ОТПРАВКА НАПОМИНАНИЙ С ОБЩИМИ НАСТРОЙКАМИ
     * @param $now
     * @param $setting
     * @param $prod_ids
     */
    private static function sendReminders($now, $setting, $prod_ids) {
        for ($num = 1; $num <= 3; $num++) {
            $remind_letter = unserialize(base64_decode($setting["remind_letter$num"]));
            self::sendLetters($remind_letter, $now, $num, $prod_ids, 2);
        }
    
        for ($num = 1; $num <= 2; $num++) {
            $remind_sms = unserialize(base64_decode($setting["remind_sms$num"]));
            self::sendSMS($setting, $now, $num, $remind_sms, $prod_ids, 2);
        }
    }


    /**
     * ОТПРАВКА НАПОМИНАНИЙ НА ПОЧТУ
     * @param $remind_letter
     * @param $now
     * @param $num
     * @param string $prod_ids
     * @param int $type
     * @return bool
     */
    public static function sendLetters($remind_letter, $now, $num, $prod_ids = '', $type = 1) {
        $rl_status = $remind_letter['status'];
        $rl_time = (int)$remind_letter['time'] * 60;

        if (!$rl_status || !$rl_time) {
            return false;
        }

        $time = $now - $rl_time;
        $searchOrders = Order::searchNoPaidOrders($time, $num-1, null, $prod_ids, $type);

        if ($searchOrders) {
            $subj = $remind_letter['subject'];
            $text = $remind_letter['text'];

            foreach ($searchOrders as $order) {
                $send = Email::SendClientNotifAboutOrder($order['client_email'], $order['client_name'], $order['order_date'], $subj, $text); // Отправляем письмо

                if ($send) { // Обнволяем заказ
                    Order::updateRemindLetterInOrder($order['order_id'], $num);
                }
            }

            return true;
        }

        return false;
    }


    /**
     * ОТПРАВКА НАПОМИНАНИЙ ЧЕРЕЗ SMS
     * @param $setting
     * @param $now
     * @param $remind_sms
     * @param string $prod_ids
     * @param int $type
     * @return bool
     */
    private static function sendSMS($setting, $now, $num, $remind_sms, $prod_ids = '', $type = 1) {
        $rs_status = isset($remind_sms['status']) ? $remind_sms['status'] : $remind_sms['send'];
        $rs_time = (int)(isset($remind_sms['time']) ? $remind_sms['time'] : $remind_sms['delay']) * 60;
        $rs_text = $remind_sms['text'];

        if (!$rs_status || !$rs_time) {
            return false;
        }

        $time = $now - $rs_time;
        $searchOrders = Order::searchNoPaidOrders($time, $num-1, 1, $prod_ids, $type);

        if ($searchOrders) {
            foreach($searchOrders as $order) {
                if (empty($order['client_phone'])) {
                    continue;
                }

                $name = $order['client_name'];
                $order_date = $order['order_date'];
                $link = $setting['script_url'].'/pay/'.$order_date;

                $replace = array(
                    '[NAME]' => $name,
                    '[LINK]' => $link,
                );

                $text = strtr($rs_text, $replace);
                SMSC::sendSMS($order['client_phone'], $text, $message_type = null, $translit = 0, $time = 0, $format = 0, $sender = false);

                //$send = Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'SMS', 'SMS');

                // Обновляем заказ
                Order::updateRemindLetterInOrder($order['order_id'], $num, 1);
            }
        }
    }
}