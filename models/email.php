<?php defined('BILLINGMASTER') or die;

class Email {

    // ШАБЛОН ЧИСТЫЙ
    public static function SendMessageToBlank($email, $name, $subject, $text)
    {
        $setting = System::getSetting();
        $send = self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);

        return $send ? true : false;
    }


    // ОТПРАВКА ПИЬСМА О ЗАВЕРШЕНИИ ПОДПИСКИ
    public static function sendLetterAboutExpireSubscription($email, $subj_manager, $letter, $user_id)
    {
        $setting = System::getSetting();
        $user = User::getUserById($user_id);

        $replace = array(
            '[NAME]' => $user['user_name'],
            '[SURNAME]' => $user['surname'],
            '[EMAIL]' => $user['email'],
            '[NICK_TG]' => $user['nick_telegram'],
            '[NICK_IG]' => $user['nick_instagram'],
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subj_manager, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    // ОТПРАВКА КАСТОМНОГО ПИСЬМА МЕНЕДЖЕРУ
    public static function sendCustomLetterForManager($email, $subj_manager, $letter, $order)
    {
        $setting = System::getSetting();
        $surname = null;
        $nick_telegram = null;
        $nick_instagram = null;

        if ($order['order_info'] != null) {
            $order_info = unserialize(base64_decode($order['order_info']));
            if (isset($order_info['surname'])) {
                $surname = $order_info['surname'];
            }

            if (isset($order_info['nick_telegram'])) {
                $nick_telegram = $order_info['nick_telegram'];
            }

            if (isset($order_info['nick_instagram'])) {
                $nick_instagram = $order_info['nick_instagram'];
            }
        }

        $replace = array(
            '[ORDER]' => $order['order_date'],
            '[DATE]' => date("d-m-Y H:i:s", $order['order_date']),
            '[NAME]' => $order['client_name'],
            '[SURNAME]' => $surname,
            '[EMAIL]' => $order['client_email'],
            '[SUMM]' => $order['summ'],
            '[NICK_TG]' => $nick_telegram,
            '[NICK_IG]' => $nick_instagram,
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subj_manager, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    // ОТПРАВКА ДОКУМЕНТА СТРОГОЙ ОТЧЁТНОСТИ
    public static function SendStrictReport($client_name, $client_email, $order_date, $payment_date, $summ, $setting, $order_items)
    {
        $ticket = unserialize(base64_decode($setting['org_data']));
        $admin_email = $setting['admin_email'];
        $subject = System::Lang('STRICT_REPORT_SUBJ');
        $order_items = '<table style="width:100%;max-width:100%;border-collapse:collapse;border-spacing:0;font-size:12px;text-align:center">
        <tr style="color: #737581;">
          <td style="padding: 8px 8px 8px 0; text-align: left; line-height: 1.42857143; vertical-align: top;">Наименование услуг</td>
          <td style="padding: 8px; line-height: 1.42857143; vertical-align: top;">Количество</td>
          <td style="padding: 8px 0 8px 8px; line-height: 1.42857143; vertical-align: top; text-align: right;">Стоимость (руб.)</td>
        </tr>'.$order_items.'</table>';

        $letter = $ticket['text'];
        $date = date("d-m-Y H:i:s", $payment_date);

        $replace = array(
            '[DATE]' => $date,
            '[ORDER]' => $order_date,
            '[CLIENT_EMAIL]' => $client_email,
            '[EMAIL]' => $admin_email,
            '[SITE]' => $setting['script_url'],
            '[SUMM]' => $summ . $setting['currency'],
            '[NAME]' => $client_name,
            '[ORG_NAME]' => $ticket['org_name'],
            '[INN]' => $ticket['inn'],
            '[YR_ADDRESS]' => $ticket['address'],
            '[OGRN]' => $ticket['ogrn'],
            '[PHONE]' => $ticket['phone'],
            '[ORDER_ITEMS]' => $order_items,
        );

        $text = strtr($letter, $replace);

        return self::sender($client_email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ОТПРАВКА ПИСЕМ НАПОМИНАНИЯ (ДОжимающие )
    public static function SendClientNotifAboutOrder($email, $name, $order_date, $subject, $letter, $link = null)
    {
        $setting = System::getSetting();

        if ($link == null) {
            $link = $setting['script_url'].'/pay/'.$order_date;
        }

        $replace = array(
            '[NAME]' => $name,
            '[ORDER]' => $order_date,
            '[LINK]' => $link,
        );

        $text = strtr($letter, $replace);
        $subject = strtr($subject, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    // ОТПРАВКА ПИСЕМ НАПОМИНАНИЯ О ПЛАТЕЖЕ (РАССРОЧКА )
    public static function SendClientNotifAboutInstallment($email, $name, $order_date, $subject, $letter, $link = null)
    {
        $setting = System::getSetting();

        if ($link == null) {
            $link = '<a href="'.$setting['script_url'].'/pay/'.$order_date.'">Завершить заказ</a>';
        }

        $replace = array(
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[ORDER]' => $order_date,
            '[LINK]' => $link,
        );

        $text = strtr($letter, $replace);
        $subject = strtr($subject, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ОТПРАВКА ПОДТВЕРЖДЕНИЯ ПОДПИСКИ
    public static function sendConfirmSubs($delivery_id, $email, $name, $subs_key)
    {
        $setting = System::getSetting();
        $delivery = Responder::getDeliveryData($delivery_id);
        $delivery_name = $delivery['name'];
        $subject = $delivery['confirm_subject'];
        $letter = $delivery['confirm_body'];
        $confirm_link = $setting['script_url'] . "/responder/confirm/$delivery_id?email=$email&key=$subs_key";

        // Реплейсим письмо
        $replace = array(
            '[NAME]' => $name,
            '[DELIVERY]' => $delivery_name,
            '[EMAIL]' => $email,
            '[CONFIRM_LINK]' => $confirm_link,
        );

        $text = strtr($letter, $replace);
        $subject = strtr($subject, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    /**
     *     ФОРУМСКИЕ
     */


    /**
     * ОПОВЕЩЕНИЕ ТОПИКСТАРТЕРА О НОВОМ СООБЩЕНИИ В ТЕМЕ + ОПОВЕЩЕНИЕ ПОДПИСАННЫХ ЮЗЕРОВ
     * Принимает данные + параметр to. Если 1 - значит оповещение топикстартера, если 0 - то подписанных юзеров.
     * @param $email
     * @param $user_name
     * @param $user
     * @param $message
     * @param $alias
     * @param $topic_id
     * @param $to
     * @return bool
     */
    public static function SendEmailTopicstarterAboutNewMessage($email, $user_name, $user, $message, $alias, $topic_id, $to)
    {
        $setting = System::getSetting();
        $key = md5($setting['secret_key']);
        $user = User::getUserNameByID($user);
        $user = $user['user_name'];

        $topic = Forum::getTopicDataByID($topic_id);
        $link = $setting['script_url']."/forum/$alias/topic-$topic_id#answer";
        $unsub_link = $setting['script_url']."/forum/$alias/topic-$topic_id/unsubscribe?email=$email&key=$key";

        if ($to == 1) {
            $subject = System::Lang('TOPICSTARTER_NEW_MESS_SUBJ');
            $letter = System::Lang('TOPICSTARTER_NEW_MESS_MESS');
        } else {
            $subject = System::Lang('SUBS_USER_NEW_MESS_SUBJ');
            $letter = System::Lang('SUBS_USER_NEW_MESS_MESS');
        }

        // Реплейсим письмо
        $replace = array(
            '[LINK]' => $link,
            '[TOPICSTARTER]' => $user_name,
            '[TOPIC]' => $topic['topic_title'],
            '[USER]' => $user,
            '[UNSUBSCRIBE]' => $unsub_link,
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ОПОВЕЩЕНИЕ АДМИНА / ТЕХПОДДЕРЖКИ О НОВОМ СООБЩЕНИИ В ТЕМЕ
    public static function SendEmailAdminAboutNewMess($email, $user, $message, $alias, $topic_id, $mess_id, $status)
    {
        $setting = System::getSetting();
        $user = User::getUserNameByID($user);
        $user = $user['user_name'];

        $topic = Forum::getTopicDataByID($topic_id);

        if ($status == 0) {
            $link = $setting['script_url']."/forum/$alias/topic-$topic_id/mess-$mess_id/confirm?public=1&key=".md5($setting['secret_key']);
        } else {
            $link = $setting['script_url']."/forum/$alias/topic-$topic_id/#mess$mess_id";
        }

        $del_link = $setting['script_url']."/forum/$alias/topic-$topic_id/mess-$mess_id/confirm?public=0&key=".md5($setting['secret_key']);

        $subject = System::Lang('ADMIN_NEW_MESS_SUBJ');
        $letter = $status == 1 ? System::Lang('ADMIN_NEW_MESS_MESS') : System::Lang('ADMIN_NEW_MESS_MESS_LINK');

        // Реплейсим письмо
        $replace = array(
            '[LINK]' => $link,
            '[DEL_LINK]' => $del_link,
            '[TOPIC]' => $topic['topic_title'],
            '[USER]' => $user,
            '[TEXT]' => $message,
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ОПОВЕЩЕНИЕ АДМИНА / ТЕХПОДДЕРЖКИ О НОВОЙ ТЕМЕ НА ФОРУМЕ
    // Ghkexftn email куда отправить, название темы, алиас категории, статус и id юзера
    public static function SendEmailAboutNewTopic($email, $name, $cat, $status, $user, $topic_message, $topic_id)
    {
        $setting = System::getSetting();
        $user = User::getUserNameByID($user);
        $user = $user['user_name'];
        $link = $setting['script_url']."/forum/$cat/topic-$topic_id/confirm?public=1&key=".md5($setting['secret_key']);
        $del_link = $setting['script_url']."/forum/$cat/topic-$topic_id/confirm?public=0&key=".md5($setting['secret_key']);

        $subject = System::Lang('ADMIN_NEW_TOPIC_SUBJ');
        $letter = $status == 1 ? System::Lang('ADMIN_NEW_TOPIC_MESS') : System::Lang('ADMIN_NEW_TOPIC_MESS_LINK');

        // Реплейсим письмо
        $replace = array(
            '[LINK]' => $link,
            '[DEL_LINK]' => $del_link,
            '[USER]' => $user,
            '[TEXT]' => $topic_message,
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    /**
     *     МЕМБЕРШИП
     */


    /**
     * ПИСЬМО КЛИЕНТУ ОБ ОКОНЧАНИИ ПЛАНА ПОДПИСКИ
     * @param $email
     * @param $name
     * @param $subject
     * @param $letter
     * @param $link
     * @return bool
     */
    public static function SendExpirationMessageByClient($email, $name, $subject, $letter, $link)
    {
        $setting = System::getSetting();
        // Реплейсим письмо
        $replace = array(
            '[NAME]' => $name,
            '[LINK]' => $link
        );

        $text = strtr($letter, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    /**
     *   ОНЛАЙН УРОКИ
     */



    // ПИСЬМО УЧЕНИКУ О НОВОМ СООБЩЕНИИ К ЗАДАНИЮ ОТ КУРАТОРА/АДМИНА
    public static function SendUserNotifAboutTaskAnswer($email, $name, $course, $lesson)
    {
        // Получаем настройки
        $setting = System::getSetting();
        $link = $setting['script_url'].'/courses/'.$course .'/'.$lesson;

        // Реплейсим письмо
        $replace = array(
            '[NAME]' => $name,
            '[LINK]' => $link
        );

        $letter = System::Lang('MESS_NOTIF_ABOUT_TASK_ANSWER');
        $text = strtr($letter, $replace);
        $subject = System::Lang('SUBJ_NOTIF_ABOUT_TASK_ANSWER');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    /**
     * ПИСЬМО АДМИНУ / АВТОРУ О ПРОВЕРКЕ ЗАДАНИЯ УРОКА
     * @param $user
     * @param $lesson_id
     * @param $answer
     * @param $curators
     * @return bool
     */
    public static function SendCheckTaskToAdmin($user, $lesson_id, $answer, $curators)
    {
        // Получаем настройки
        $setting = System::getSetting();
        $lesson = Course::getLessonDataByID($lesson_id);
        $course = Course::getCourseByID($lesson['course_id']);
        $course_name = $course['name'];
        $link = $setting['script_url'].'/courses/'.$course['alias'].'/'.$lesson['alias'];
        $link_admin = '<a href="'.$setting['script_url'].'/admin/answers?get=check">Проверить</a>';
        $user = User::getUserById($user);
        $user = $user['user_name'] .'('.$user['email'].')';

        $from = $setting['admin_email'];
        $from_name = 'Billing Master';

        $letter = System::Lang('ADMIN_LETTER_ABOUT_CHECK_TASK');
        $subject = System::Lang('ADMIN_SUBJ_ABOUT_CHECK_TASK');
        $send = false;

        // ЕСЛИ кураторы есть.
        if (!empty($curators)) {
            // Получить емейлы кураторов
            $arr = unserialize($curators);
            $cur_true = false;

            // Реплейсим письмо
            $replace = array(
                '[COURSE]' => $course_name,
                '[LESSON]' => $lesson['name'],
                '[USER]' => $user,
                '[LINK]' => $link
            );

            $text = strtr($letter, $replace);

            foreach($arr as $curator) {
                $user_curator = User::getUserById($curator);
                if ($user_curator) {
                    $cur_true = true;
                    $send = self::sender($user_curator['email'], $subject, $text, $setting, $from_name, $from);
                }
            }

            if (!$cur_true) { // Если ни одного куратора не существует, отправляем админу.
                // Реплейсим письмо
                $replace = array(
                    '[COURSE]' => $course_name,
                    '[LESSON]' => $lesson['name'],
                    '[USER]' => $user,
                    '[LINK]' => $link_admin
                );

                $text = strtr($letter, $replace);

                return self::sender($setting['admin_email'], $subject, $text, $setting, $from_name, $from);
            }
        } else { // ЕСЛИ кураторов нет, отправляем админу
            // Реплейсим письмо
            $replace = array(
                '[COURSE]' => $course_name,
                '[LESSON]' => $lesson['name'],
                '[USER]' => $user,
                '[LINK]' => $link_admin
            );

            $text = strtr($letter, $replace);
            $send = self::sender($setting['admin_email'], $subject, $text, $setting, $from_name, $from);
        }

        return $send;
    }


    /**
     * ПИСЬМО КУРАТОРУ или кураторам или АДМИНУ от ученика об ответе на ДЗ или комментарий
     * @param $user_id
     * @param $answer_homework_id
     * @param $answer
     * @param $lesson
     * @param $training
     * @return bool
     */
    public static function SendAnswerFromUserToCurator($user_id, $answer_homework_id, $answer, $lesson, $training)
    {
        
        $setting = System::getSetting();
        $user = User::getUserById($user_id);
        if (empty($answer_homework_id)) {
            $answer_homework = TrainingLesson::getHomeWork($user_id, $lesson['lesson_id']);
            $answer_homework_id = $answer_homework['homework_id'];
        }
        $training_name = $training['name'];
        $from = $setting['admin_email'];
        $from_name = 'School Master';
        $link_admin = $setting['script_url'].'/lk/curator/answers/'.$answer_homework_id.'/'.$user_id.'/'.$lesson['lesson_id'];
        $subject = $training['subject_letter_to_curator'];
        $letter = $training['letter_to_curator'];
        $curators = '';
       

        $assign_curator = Training::getCuratorToUserByLessonId($lesson['lesson_id'], $user_id);
        if (isset($assign_curator['curator_id'])) { 
            $send_email = $assign_curator['email'];
        } else {
            if ($training['send_email_to_all_curators'] == 1) {              
                $curators = Training::getCuratorsTraining($training['training_id']);          
            } else {
                $send_email = $setting['admin_email'];
            }
        }
             
         // Реплейсим письмо
         $replace = array(
            '[LINK]' => $link_admin,
            '[LESSON]' => $lesson['name'],
            '[NAME]' => $user['user_name'],
            '[SURNAME]' => $user['surname'],
            '[EMAIL]' => $user['email'],
            '[TRAINING]' => $training_name
            
        );

        $text = strtr($letter, $replace);
        
        if ($curators){
            // TODO тут задвоение кураторов, если он мастер и обычный и нет персоноально назначеного.
            $curators_for_send = array_unique(array_merge($curators['datamaster'],$curators['datacurators']));
            foreach ($curators_for_send as $curator) {
                $user_curator = User::getUserById($curator);
                if ($user_curator) {
                    $replace = array('[CURATOR]' => $user_curator['user_name']);
                    $text = strtr($text, $replace);
                    $send = self::sender($user_curator['email'], $subject, $text, $setting, $from_name, $from);
                }
            }
        } else {
            return $send = self::sender($send_email, $subject, $text, $setting, $from_name, $from);
        }   

    }


    /**
     * ПИСЬМО КЛИЕНТУ ОБ ОТВЕТЕ на его задание/тест и т.д.
     * @param $user
     * @param $lesson_id
     * @param $text_message
     * @param $type_message
     * @param $status
     * @param $curator
     * @return bool
     */
    public static function SendEmailFromCuratorToUser($user, $lesson_id, $text_message, $type_message, $status, $curator)
    {

        // Получаем настройки
        $setting = System::getSetting();
        $lesson = TrainingLesson::getLesson($lesson_id);
        $training_id = Training::getTrainingIdByLessonId($lesson_id);
        $training = Training::getTraining($training_id);
        $subject = $training['subject_letter_to_user'];
        $letter = $training['letter_to_user'];
        $link = $setting['script_url']."/training/view/{$training['alias']}/lesson/{$lesson['alias']}";
        $message = isset($type_message) ? base64_decode($type_message) : '';
        
        // Реплейсим письмо
        $replace = array(
            '[LINK]' => $link,
            '[TRAINING]' => $training['name'],
            '[LESSON]' => $lesson['name'],
            '[NAME]' => $user['user_name'],
            '[SURNAME]' => $user['surname'],
            '[CURATOR]' => $curator['user_name'],
            '[MESSAGE]' => $message,
            '[STATUS]' => $status
        );

        $text = strtr($letter, $replace);

        return self::sender($user['email'], $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }




    /**
     *    ПРОДУКТЫ , ЗАКАЗЫ
     */


    /**
     * ПИСЬМО КЛИЕНТУ С ПОДТВЕРЖДЕНИЕМ ДОСТАВКИ
     * @param $order_date
     * @param $name
     * @param $email
     * @param $items
     * @param $total
     * @param $metod_name
     * @return bool
     */
    public static function SendConfirmDelivery($order_date, $name, $email, $items, $total, $metod_name)
    {
        // Получаем настройки
        $setting = System::getSetting();

        $link = $setting['script_url'].'/delivery/confirm/'.$order_date.'?key='.md5($email);
        $space = '<br />';
        $items = implode($space, $items);

        // Реплейсим письмо
        $replace = array(
            '[ORDER]' => $order_date,
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[ITEMS]' => $items,
            '[SUMM]' => $total,
            '[METHOD]' => $metod_name,
            '[CURRENCY]' => $setting['currency'],
            '[LINK]' => $link
        );

        $letter = System::Lang('CONFIRM_DELIVERY_LETTER');

        $text = strtr($letter, $replace);
        $subject = System::Lang('CONFIRM_DELIVERY_SUBJECT');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }

    // ПИСЬМО КЛИЕНТУ О ЗАКАЗЕ
    // ПРИНИМАЕТ ТЕКСТ ПИСЬМА, ИМЯ КЛИЕНТА, НОМЕР ЗАКАЗА
    public static function SendOrder($order_date, $letter, $product, $name, $email, $summ, $pincode, $addsubject = null)
    {
        $setting = System::getSetting();
        $link = $setting['script_url'].'/download/'. $order_date.'?key='.md5($email);
        $pin = !empty($pincode) ? System::Lang('YOUR_PINCODE').$pincode : '';

        // реплейсим письмо
        $replace = array(
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[ORDER]' => $order_date,
            '[PRODUCT_NAME]' => $product,
            '[LINK]' => $link,
            '[SUMM]' => $summ,
            '[DWL_TIME]' => $setting['dwl_time'],
            '[SUPPORT]' => $setting['support_email'],
            '[PINCODE]' => $pin,
            '[EMAIL]' => $email
        );

        $text = strtr($letter, $replace);
        if ($addsubject != null) {
            $subject = $addsubject;
        } else {
            $subject = $setting['client_letter_subj'] != null ? $setting['client_letter_subj'] : System::Lang('SUBJECT_EMAIL_ORDER');
        }

        $subject = strtr($subject, $replace);

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    // ПИСЬМО КЛИЕНТУ О РЕГИСТРАЦИИ
    public static function SendLogin($name, $email, $pass, $letter)
    {
        // Получаем настройки
        $setting = System::getSetting();

        $link = $setting['script_url'].'/lk';

        // реплейсим письмо
        $replace = array(
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[EMAIL]' => $email,
            '[LINK]' => $link,
            '[SUPPORT]' => $setting['support_email'],
            '[PASS]' => $pass
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('SUBJECT_EMAIL_REGISTER');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ПИСЬМО КЛИЕНТУ ДЛЯ ПОДТВЕРЖДЕНИЯ РЕГИСТРАЦИИ
    public static function SendRegConfirm($name, $email, $req_key, $pass, $letter)
    {
        // Получаем настройки
        $setting = System::getSetting();

        $link = "{$setting['script_url']}/lk/registration/$req_key";
        $link2 = "{$setting['script_url']}/lk";

        // реплейсим письмо
        $replace = array(
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[EMAIL]' => $email,
            '[LINK]' => $link,
            '[LINK2]' => $link2,
            '[SUPPORT]' => $setting['support_email'],
            '[PASS]' => $pass,
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('SUBJECT_EMAIL_REGISTER');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ПИСЬМО ПАРТНЁРУ О РЕГИСТРАЦИИ
    public static function SendPernerLetter($name, $email, $reg_key)
    {
        $setting = System::getSetting($name, $email);
        $link = $setting['script_url'].'/aff/confirm?key='.$reg_key;
        $letter = System::Lang('LETTER_PARTNER_REGISTER');

        // реплейсим письмо
        $replace = array(
            '[NAME]' => $name,
            '[LINK]' => $link,
            '[SUPPORT]' => $setting['support_email']
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('SUBJECT_PARTNER_REGISTER');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ПИСЬМО АДМИНУ о ЗАКАЗЕ
    public static function SendOrderToAdmin ($order_date, $name, $email, $sum, $partner, $order_id = null, $payment_id = null)
    {
        // Получаем настройки
        $setting = System::getSetting();
        $letter = System::Lang('ADMIN_LETTER_ORDER');
        $subject = System::Lang('SUBJECT_EMAIL_ADMIN_ORDER');
        $subject = $subject . ' '.$setting['site_name'];
        $contents = '';

        if ($payment_id != null) {
            $payment_data = Order::getPaymentDataForAdmin($payment_id);
            $payment = $payment_data['title'];
        } else {
            $payment = 'Free';
        }

        if (!empty($order_id)) {
            $items = Order::getOrderItems($order_id);
            foreach($items as $item) {
                $product_data = Product::getProductName($item['product_id']);
                $contents .= $product_data['product_name'].$product_data['mess'].'<br />';
            }
        }

        $replace = array(
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[ORDER]' => $order_date,
            '[PAYMENT]' => $payment,
            '[CLIENT_EMAIL]' => $email,
            '[SUMM]' => $sum,
            '[PARTNER]' => $partner,
            '[CONTENTS]' => $contents
        );

        $text = strtr($letter, $replace);

        return self::sender($setting['admin_email'], $subject, $text, $setting, 'Billing Master', $setting['admin_email']);
    }


    /**
     * ПИСЬМО АДМИНУ О РЕГИСТРАЦИИ НОВОГО ПАРТНЁРА
     * @param $name
     * @param $email
     * @return bool
     */
    public static function SendNotifAboutPartnerToAdmin($name, $email)
    {
        // Получаем настройки
        $setting = System::getSetting();
        $letter = System::Lang('ADMIN_LETTER_ABOUT_PARTNER');

        $replace = array (
            '[NAME]' => $name,
            '[EMAIL]' => $email
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('ADMIN_SUBJECT_ABOUT_PARTNER');

        return self::sender($setting['admin_email'], $subject, $text, $setting, 'Billing Master', $setting['admin_email']);
    }


    /**
     * ПИСЬМО ЮЗЕРУ ДЛЯ ПОДТВЕРЖДЕНИЯ СМЕНЫ ПАРОЛЯ
     * @param $email
     * @param $letter
     * @param $key
     * @return bool
     */
    public static function LostYourPass($email, $letter, $key)
    {
        // Получаем настройки
        $setting = System::getSetting();

        $link = $setting['script_url'].'/lostpass?email='.$email.'&key='.$key;

        // реплейсим письмо
        $replace = array(
            '[LINK]' => $link,
            '[SITE]' => $setting['script_url']
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('LETTER_LOSTPASS_SUBJECT');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    // ПИСЬМО ЮЗЕРУ С НОВЫМ ПАРОЛЕМ
    public static function ChangePassOk($email, $pass, $letter)
    {
        // Получаем настройки
        $setting = System::getSetting();
        $link = $setting['script_url'].'/lk';

        // реплейсим письмо
        $replace = array(
            '[PASS]' => $pass,
            '[LINK]' => $link
        );

        $text = strtr($letter, $replace);
        $subject = System::Lang('LETTER_LOSTPASS_SUBJECT');

        return self::sender($email, $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }


    /**
     * УВЕДОМЛЕНИЕ АДМИНУ О ПОДТВЕРЖДЕНИИ ДОСТАВКИ ЗАКАЗА
     * @param $order_date
     * @param $email
     * @param $name
     * @return bool
     */
    public static function AdminDeliveryConfirm($order_date, $email, $name)
    {
        $setting = System::getSetting();
        // Реплейсим письмо
        $replace = array(
            '[ORDER]' => $order_date,
            '[CLIENT_NAME]' => $name,
            '[NAME]' => $name,
            '[EMAIL]' => $email
        );

        $letter = System::Lang('CONFIRM_DELIVERY_ADMIN_LETTER');
        $text = strtr($letter, $replace);
        $subject = System::Lang('CONFIRM_DELIVERY_ADMIN_SUBJECT');

        return self::sender($setting['admin_email'], $subject, $text, $setting, $setting['sender_name'], $setting['sender_email']);
    }



    // УВЕДОМЛЕНИЕ О РУЧНОЙ ОПЛАТЕ
    public static function AdminCustomOrder($order, $secret, $email, $client_email, $payment, $purse, $summ, $client, $phone, $script_url, $order_id)
    {
        $setting = System::getSetting();
        $key = md5($order_id.$secret);

        $subject = 'Ручной перевод';
        $message = "<p>Оплата заказа № $order ручным способом.</p>
        <p>Система: $payment<br />
        Кошелёк: $purse<br />
        Сумма: $summ <br />
        Клиент: $client<br />
        E-mail: $client_email<br />
        Телефон: $phone</p>
        <p><a href=\"$script_url/confirmcustom?key=$key&date=$order\">Подтвердить заказ</p>";

        return self::sender($email, $subject, $message, $setting, 'BillingMaster', $email);
    }




    // УВЕДОМЛЕНИЕ О ВЫСТАВЛЕНИИ СЧЁТА КОМПАНИИ
    // УВЕДОМЛЕНИЕ О РУЧНОЙ ОПЛАТЕ
    public static function AdminCompanyOrder($order, $secret, $email, $client_email, $summ, $client, $phone, $script_url,
        $order_id, $organization, $inn, $bik, $rs, $country, $city, $address)
    {
        $setting = System::getSetting();
        $key = md5($order_id.$secret);

        $subject = "Выставление счёта юр.лицу";
        $message = "<p>Выставлен счёт на заказ № $order, проверьте поступление на ваш р/с</p>
        <p>Сумма: $summ <br />
        Клиент: $client<br />
        E-mail: $client_email<br />
        Телефон: $phone</p>
        <p><strong>Реквизиты:</strong></p>
        <p>Организация: $organization<br />
        ИНН/КПП: $inn<br />
        БИК:$bik<br />
        Р/с:$rs</p>
        <p>$address</p>
        <p>Когда счёт будет оплачен, вы можете <a href=\"$script_url/confirmcustom?key=$key&date=$order\">подвтердить оплату</p>";

        // Письмо админу
        return self::sender($email, $subject, $message, $setting, 'BillingMaster', $email);
    }



    /**
     *  СИСТЕМНЫЕ УВЕДОМЛЕНИЯ ДЛЯ АДМИНА
     */


    /**
     * СИСТЕМНОЕ УВЕДОМЛЕНИЕ ДЛЯ АДМИНА ОБ ОКОНЧАНИИ ПИНКОДОВ ДЛЯ ПРОДУКТА
     * ПРИНИМАЕТ ЕМЕЙЛ АДМИНА И ID продукта
     * @param $email
     * @param $id
     * @param $pin_count
     * @return bool
     */
    public static function AdminPincodeNotification($email, $id, $pin_count)
    {
        $setting = System::getSetting();
        $product_name = Product::getProductName($id);

        $subject = "Billing Master - заканчиваются пин коды";
        $message = "<p>Заканчиваются пинкоды для продукта: " . $product_name['product_name']."<br />Осталось всего $pin_count. <br />
        Проверьте и добавьте новых.</p>
        <p>Это системное сообщение скрипта Billing Master, отвечать на него не нужно.</p>";

        // Письмо админу
        return self::sender($email, $subject, $message, $setting, 'BillingMaster', $email);
    }



    // СИСТЕМНОЕ УВЕДОМЛЕНИЕ ДЛЯ АДМИНА О НЕРАБОЧЕМ АПСЕЛЛЕ
    // ПРИНИМАЕТ ЕМЕЙЛ АДМИНА И НОМЕР ЗАКАЗА
    public static function AdminNotification($email, $id)
    {
        $setting = System::getSetting();
        $subject = "Billing Master - Апселл не найден";
        $message = "<p>Товар для апселла не найден (может вы его удалили?).<br />Номер заказа $id<br />Проверьте настройки для продукта</p>
        <p>Это системное сообщение скрипта Billing Master, отвечать на него не нужно.</p>";

        // Письмо админу
        return self::sender($email, $subject, $message, $setting, 'BillingMaster', $email);
    }


    /**
     * SMTP ОТПРАВЩИК ОДИНОЧНЫХ писем
     * @param $email
     * @param $subject
     * @param $text
     * @param $setting
     * @return bool
     */
    public static function SMTPSingleSender($email, $subject, $text, $setting)
    {
        require_once (dirname(__FILE__) . '/../vendor/autoload.php');

        if (System::CheckExtensension('autopilot', 1)) { // расширение autopilot
            $autopilot = Autopilot::getSettings();
            if (isset($autopilot['vk_club']['notify']) && $autopilot['vk_club']['notify'] == 1) {
                Autopilot::sendMessToVKbyEmail($email, $text);
            }

            if (stripos($email, '@vk.com')) {
                return true; // do not send to not valid emails!
            }
        }

        $time = time();

        $sender_name = $setting['sender_name'];
        $sender_email = $setting['sender_email'];

        // Инициализировать объект Мейлера
        if ($setting['smtp_ssl'] > 0) {
            if ($setting['smtp_ssl'] == 1) {
                $auth = 'ssl';
            }

            if ($setting['smtp_ssl'] == 2) {
                $auth = 'tls';
            }

            $transport = (new Swift_SmtpTransport($setting['smtp_host'], $setting['smtp_port'], $auth))
                ->setUsername($setting['smtp_user'])
                ->setPassword($setting['smtp_pass']);
        } else {
            $transport = (new Swift_SmtpTransport($setting['smtp_host'], $setting['smtp_port']))
                ->setUsername($setting['smtp_user'])
                ->setPassword($setting['smtp_pass']);
        }

        $mailer = new Swift_Mailer($transport);

        if (!empty($setting['smtp_private_key'])) {
            $signer = new Swift_Signers_DKIMSigner($setting['smtp_private_key'], $setting['smtp_domain'], $setting['smtp_selector']);
        }

        $message = new Swift_Message();
        if (!empty($setting['smtp_private_key'])) {
            $message->attachSigner($signer);
        }

        $message->setFrom([$sender_email => $sender_name]);
        if ($setting['return_path'] != null) {
            $message->setSender($setting['return_path']);
        }

        $message->AddReplyTo($setting['return_path'], $sender_name);

        // Message-ID
        $header_name = md5($email.$time);
        $chars = ['http://','https://']; // символы для удаления
        $domain = str_replace($chars, '', $setting['script_url']); // PHP код
        $message->getHeaders()->addIdHeader($header_name, $header_name.'@'.$domain);

        // Письмо
        $message->setBody($text, 'text/html', 'utf-8');
        $message->addPart(strip_tags($text), 'text/plain');
        $message->setSubject(html_entity_decode($subject));
        $message->setTo($email);

        $res = $mailer->send($message);
        if ($res) {
            $descript = null;
            $subject = htmlentities($subject);
            $log = Email::WriteLog($email, $text, $time, $subject, $descript);
            if ($log) {
                return true;
            }
        }

        return false;
    }


    // Тестовое уведоление для отладки
    public static function TestEmail($str)
    {
        $setting = System::getSetting();
        $email = 'report@kasyanov.info';
        $message = 'Для отладки<br />'.$str;

        return self::sender($email, 'Billing Master - отладка', $message, $setting, 'BillingMaster', $email);
    }


    /**
     * ОТПРАВИТЬ ПИСЬМО
     * @param $email
     * @param $subject
     * @param $text
     * @param $setting
     * @param $from_name
     * @param $from
     * @return bool
     */
    public static function sender($email, $subject, $text, $setting, $from_name, $from) {
        if ($setting['use_smtp'] == 1) { // Отправляем через SMTP
            $send = self::SMTPSingleSender($email, $subject, $text, $setting);
        } else { // Отправляем через Mail()
            $from_name = $setting['sender_name'];
            $from = $setting['sender_email'];

            $subject = html_entity_decode($subject);
            $send = self::mailSender($email, $subject, $text, $from_name, $from);
        }

        return $send ? true: false;
    }


    /**
     * ОТПРАВИТЬ ПИСЬМО ЧЕРЕЗ СТАНДАРТНУЮ ФУНКЦИЮ mail
     * @param $email
     * @param $subject
     * @param $text
     * @param $from_name
     * @param $from
     * @return bool
     */
    public static function mailSender($email, $subject, $text, $from_name, $from) {
        $headers= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html;charset=utf-8 \r\n";
        $headers .= "From: $from_name <$from>\r\n";
        $headers .= "Reply-To: $from \r\n";

        return mail($email, $subject, $text, $headers);
    }


    /**
     * ЗАПИСЬ ОТПРАВЛЕННЫХ ЕМЕЙЛ СООБЩЕНИЙ В ЛОГ
     * @param $email
     * @param $letter
     * @param null $time
     * @param null $type
     * @param null $descript
     * @return bool
     */
    public static function WriteLog($email, $letter, $time = null, $type = null, $descript = null) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'log_email_send (email, letter, datetime, type, description ) 
                VALUES (:email, :letter, :datetime, :type, :descript)';

        $result = $db->prepare($sql);
        $result->bindParam(':letter', $letter, PDO::PARAM_STR);
        $result->bindParam(':datetime', $time, PDO::PARAM_INT);

        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':type', $type, PDO::PARAM_STR);
        $result->bindParam(':descript', $descript, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ИЗ ЛОГА Email сообщений
     * @param int $page
     * @param null $show_items
     * @param bool $pagination
     * @param bool $email
     * @param bool $start
     * @param bool $finish
     * @param bool $subject
     * @return bool
     */
    public static function getLog($page = 1, $show_items = null, $pagination = false, $email = false, $start = false, $finish = false, $subject = false) {
        $clauses = array();
        if ($start) {
            $clauses[] =  "datetime > $start";
        }
        if ($finish) {
            $clauses[]  = "datetime < $finish";
        }
        if ($email !== false) {// Поиск по email
            $clauses[] = "email LIKE '%$email%'";
        }
        if ($subject != false) {// Поиск по теме
            $clauses[] = "type LIKE '%$subject%'";
        }

        $where = !empty($clauses) ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        $limit = $pagination ? "LIMIT $show_items OFFSET " . ($page - 1) * $show_items : 'LIMIT 3000';
        $sql = "SELECT id, email, datetime, type FROM ".PREFICS."log_email_send $where ORDER BY id DESC $limit";

        $db = Db::getConnection();
        $result = $db->query($sql);
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ПИСЬМА ОТПАРВЛЕННЫЕ ЮЗЕРУ
     * @param $email
     * @return array|bool
     */
    public static function getLogByUser($email) {
        $db = Db::getConnection();
        $result = $db->query("SELECT id, email, type, datetime FROM ".PREFICS."log_email_send WHERE email = '$email' ORDER BY datetime DESC LIMIT 150");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ЗАПИСИ ЛОГА
     * @param $id
     * @return bool|mixed
     */
    public static function getLogData($id) {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."log_email_send WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОДСЧЁТ КОЛ-ВА ЗАПИСЕЙ ЛОГА ЕМЕЙЛ
     * @return mixed
     */
    public static function countLogs()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."log_email_send");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'log_email_send WHERE datetime < :datetime');
        $result->bindParam(':datetime', $date, PDO::PARAM_INT);

        return $result->execute();
    }
}