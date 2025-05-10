<?php defined('BILLINGMASTER') or die;

class SMSC  {

    const SMSC_POST = true;
    const SMSC_HTTPS = false;
    const SMSC_CHARSET = 'utf-8';
    const SMTP_FROM = 'api@smsc.ru.';
    const SMSC_MESSAGE_TYPES = array('viber', 'mms', 'mail', 'soc');
    const SMSC_API_PATH = ROOT . '/lib/notifications/smsc/smsc_api.php';

    private static $settings = null;
    private static $login;
    private static $password;
    private static $sender;
    private static $smsc_debug;

    private static function setParameters()
    {
        if (empty(self::$settings)) {
            $sys_settings = System::getSetting();
            $settings = unserialize(base64_decode($sys_settings['smsc']));

            if (!$settings['login'] || !$settings['password']) {
                return false;
            }

            self::$settings = $settings;
            self::$login = self::$settings['login'];
            self::$password = self::$settings['password'];
            self::$sender = self::$settings['sender'];
            self::$smsc_debug = self::$settings['debug'];

            define("SMSC_LOGIN", self::$login);
            define("SMSC_PASSWORD", self::$password);
            define("SMSC_POST", self::SMSC_POST);
            define("SMSC_HTTPS", self::SMSC_HTTPS);
            define("SMSC_CHARSET", self::SMSC_CHARSET);
            define("SMSC_DEBUG", self::$smsc_debug);
            define("SMTP_FROM", self::SMTP_FROM);
        }

        return true;
    }

	public static function getBalance()
    {
        if (!self::setParameters()) {
            return false;
        }

		require_once (self::SMSC_API_PATH);

		return get_balance();
	}

	public static function sendSMS($phone, $message, $message_type = null, $translit = 0, $time = 0, $format = 0, $sender = false, $tinyurl = 1)
	{
        if (strlen(trim($message)) == 0 || !self::setParameters()) {
            return false;
        }

        require_once (self::SMSC_API_PATH);

        $query = '';

        if ($message_type && in_array($message_type, self::SMSC_MESSAGE_TYPES) !== false) {
            $query .= "$message_type=1";
        }

        if ($tinyurl) {
            $query .= ($query ? '&' : '')."tinyurl=1";
        }

		$sender = trim($sender) ?: self::$sender;
		$result = send_sms($phone, $message, $translit, $time, 0, $format, $sender, $query);

   		if ($result[1] > 0) {
   		    SMS::WriteLog($phone, $message);
            return true;
        } else {
            return false;
        }
    }
}
