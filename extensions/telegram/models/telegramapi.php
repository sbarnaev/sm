<?php defined('BILLINGMASTER') or die;

class TelegramApi {

    const BASE_API_URL = 'https://api.telegram.org/bot';

    private $token;

    public function __construct($token) {
        $this->token = $token;
    }


    /**
     * ОТПРАВИТЬ СООБЩЕНИЕ В ЧАТ
     * @param int $chat_id - ID чата, в который отправляем сообщение
     * @param String $message - текст сообщения
     * @param array $params - дом.параметры (опционально)
     * @return mixed
     */
    public function sendMessage($chat_id, $message, $params = []) {
        if (!is_array($params)) {
            $params = [];
        }

        $params['chat_id'] = $chat_id;
        $params['text'] = $message; // Telegram не понимает html-тегов
        //$params['parse_mode']='markdown';

        $url = $this->buildUrl('sendMessage').'?'.http_build_query($params);
        $data = $this->curl($url);

        return json_decode($data, true);
    }


    /**
     * УДАЛИТЬ УЧАСТНИКА ЧАТА
     * @param $user_id
     * @param $chat_id
     * @return mixed
     */
    public function removeMember($user_id, $chat_id) {
        $params['chat_id'] = $chat_id;
        $params['user_id'] = $user_id; // Telegram не понимает html-тегов

        $url = $this->buildUrl('kickChatMember').'?'.http_build_query($params);
        $data = $this->curl($url);
        $data = json_decode($data, true);

        return $data['ok'] ? true : false;
    }


    /**
     * УДАЛИТЬ УЧАСТНИКА ЧАТА ИЗ ЧС
     * @param $user_id
     * @param $chat_id
     * @return mixed
     */
    public function removeMemberFromBlacklist($user_id, $chat_id) {
        if ($this->isMember2Blacklist($user_id, $chat_id)) { // если пользователь состоит в чс, удаляем его из чс
            $params['chat_id'] = $chat_id;
            $params['user_id'] = $user_id; // Telegram не понимает html-тегов

            $url = $this->buildUrl('unbanChatMember').'?'.http_build_query($params);
            $data = $this->curl($url);
            $data = json_decode($data, true);

            return $data['ok'] ? true : false;
        }

        return false;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС О ПРИНАДЛЕЖНОСТИ ПОЛЬЗОВАТЕЛЯ К ЧАТУ/ГРУППЕ
     * @param $user_id
     * @param $chat_id
     * @return bool
     */
    public function isMember($user_id, $chat_id) {
        $data = $this->getMember($user_id, $chat_id);

        return $data['ok'] && $data['result']['status'] == 'member' ? true : false;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС О СОСТОЯНИИ ПОЛЬЗОВАТЕЛЯ В ЧС
     * @param $user_id
     * @param $chat_id
     * @return bool
     */
    public function isMember2Blacklist($user_id, $chat_id) {
        $data = $this->getMember($user_id, $chat_id);

        return $data['ok'] && $data['result']['status'] == 'kicked' ? true : false;
    }


    /**
     * ПОЛУЧИТЬ ИНФОРМАЦИЮ ОБ УЧАСТНИКЕ ЧАТА
     * @param $user_id
     * @param $chat_id
     * @return mixed
     */
    public function getMember($user_id, $chat_id) {
        $params['user_id'] = $user_id; // Telegram не понимает html-тегов
        $params['chat_id'] = $chat_id;

        $url = $this->buildUrl('getChatMember').'?'.http_build_query($params);
        $data = $this->curl($url);

        return json_decode($data, true);
    }


    /**
     * УСТАНОВИТЬ ВЕБХУКИ
     * @param String $hook_url - адрес на нашем сервере, куда будут приходить обновления
     * @return mixed|null
     */
    public function setWebHook($hook_url) {
        $data = $this->sendPost('setWebHook', ['url' => $hook_url]);

        return json_decode($data, true);
    }


    /**
     * УДАЛИТЬ ВЕБХУКИ
     * @return mixed
     */
    public function delWebHook() {
        $data = $this->curl($this->buildUrl('deleteWebhook'));

        return json_decode($data, true);
    }


    /**
     * ПОЛУЧИТЬ ОБНОВЛЕНИЯ
     * @return mixed
     */
    public function getUpdates() {
        $data = $this->curl($this->buildUrl('getUpdates'));

        return json_decode($data, true);
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ЧАТА
     * @return mixed
     */
    public function getChat($chat_id) {
        $params['chat_id'] = $chat_id;
        $data = $this->curl($this->buildUrl('getChat').'?'.http_build_query($params));

        return json_decode($data, true);
    }


     /**
      * ПОЛУЧИТЬ ОБНОВЛЕНИЯ ЧЕРЕХ ВЕБХУКИ
     * @return mixed
     */
    public function getUpdatePost() {
        $data = file_get_contents("php://input");

        return !empty($data) ? json_decode($data, true) : false;
    }


    /**
     * @return mixed
     */
    public function speak($last_message,$botname,$name_answer,$answers_array) {
        $message_text = mb_strtolower($last_message['text']);
        $speak = false;
        $answer = '';

        if ( mb_strpos($message_text, $botname)!==false) {
            $speak = true;
        }

        if (mb_substr($message_text,0,1,"UTF-8")=='/') {
            $speak = true;
        }

        if ($speak) {
            $answers = [];
            foreach ($answers_array as $frase => $frase_answer) {
                if (mb_strpos($message_text, $frase)!==false) {
                    $answers[] = $frase_answer;
                }
            }

            $answer = implode("\n\n", $answers);
            if (empty($answer)) {
                $answer = $name_answer;
            }
        }

        return $answer;
    }


    /**
     * getUsernameLink
     * @param array $member
     * @return string
     */
    public function getUsernameLink($member = []) {
        $member_link = '';

        if ($member) {
            $first_name = 'User';
            $last_name = '';
            $user_id = $member['id'];

            if (isset($member['first_name'])) {
                $first_name = $member['first_name'];
            }

            if (isset($member['last_name'])) {
                $last_name = ' '.$member['last_name'];
            }

            $member_link = '['.$first_name.$last_name.'](tg://user?id='.$user_id.')';
        }

        return $member_link;
    }


    /**
     * [messagePrepare]
     * @param $welcome_message
     * @param bool $new_chat_members
     * @return string|string[]|null
     */
    public function messagePrepare($welcome_message, $new_chat_members = false) {
        $message = '';
        if ($new_chat_members && is_array($new_chat_members)) {
            $patterns = [];
            $patterns[0] = '/full_name_link_mk/';
            $replacements = [];

            if (isset($new_chat_members['id'])) {
                $replacements[0] = $this->getUsernameLink($new_chat_members);
            } else{
                $new_chat_members_list = [];
                foreach ($new_chat_members as $new_chat_member) {
                    $new_chat_members_list[] = $this->getUsernameLink($new_chat_member);
                }
                $replacements[0] = implode(" и ", $new_chat_members_list);
            }

            $message = preg_replace($patterns, $replacements, $welcome_message);
        } else{
            $message = $welcome_message;
        }

        return $message;
    }


    /**
     * ОТПРАВИТЬ ЗАПРОС В TELEGRAM API
     * @param String $method_name - имя метода в API, который вызываем
     * @param array $data - параметры, которые передаем, необязательное поле
     * @return mixed|null
     */
    private function sendPost($method_name, $data = []) {
        $result = null;

        if (!empty($data)) {
            $result = $this->curl($this->buildUrl($method_name), $data);
        }

        return $result;
    }


    /**
     * @param String $method_name - имя метода в API, который вызываем
     * @return string - Софрмированный URL для отправки запроса
     */
    private function buildUrl($method_name) {
        return self::BASE_API_URL.$this->token.'/'.$method_name;
    }


    /**
     * @param $url
     * @param array $data
     * @return bool|string
     */
    public function curl($url, $data = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_NOBODY,false);
        curl_setopt($ch,CURLOPT_HEADER,false);

        if (!empty($data)) {
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}