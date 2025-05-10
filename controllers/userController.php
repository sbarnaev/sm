<?php defined('BILLINGMASTER') or die;


class userController {
    
    
    public function actionLogin()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0 && $setting['enable_aff'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $title = 'Вход на сайт';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
        $email = '';
        if (User::isAuth()) {
            header("Location: /");
        }
        
        if (isset($_POST['enter']) && !empty($_POST['email']) && !empty($_POST['pass'])) {
            $url = htmlentities($_SERVER['HTTP_REFERER']);
            $email = htmlentities(trim($_POST['email']));
            $pass = trim($_POST['pass']);
            $errors = false;
            $user = User::checkUserData($email, $pass);

            if ($user == false) {
                $errors[] = 'Неверный логин или пароль';
            } else {
                User::Auth($user['user_id'], $user['user_name']);

                if (isset($_SESSION['redirect_url'])) {
                    $url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    System::redirectUrl($url);
                } elseif ($setting['login_redirect'] == 1) {
                    System::redirectUrl('/lk');
                } elseif ($setting['login_redirect'] == 2) {
                    System::redirectUrl('/lk/orders');
                } elseif ($setting['login_redirect'] == 3 && System::CheckExtensension('courses', 1)) {
                    System::redirectUrl('/lk/mycourses');
                } elseif ($setting['login_redirect'] == 4 && System::CheckExtensension('training', 1)) {
                    System::redirectUrl('/lk/mytrainings');
                } elseif ($setting['login_redirect'] == 5) {
                    System::redirectUrl($setting['script_url']);
                } else {
                    System::redirectUrl($url);
                }
            }
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/users/login.php');
    }
    
    
    public function actionForgot ()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0 && $setting['enable_aff'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        if (!User::isAuth()) {
            $title = 'Вспомнить пароль';
            $noindex = 1;
            $meta_desc = '';
            $meta_keys = '';
            $use_css = 1;
            $is_page = 'lk';
            
            if (isset($_POST['forgot'])) {
                $email = trim(strtolower(htmlentities($_POST['email'])));
                
                // Найти юзера с данным емейлом
                $data = User::getUserDataByEmail($email);
                if ($data) {
                    // Отправить письмо со ссылкой для смены пароля
                    // ссылка/lostpass?email=[email]&key=[USER_ID]+[SECRET_KEY] 
                    $key = md5($data['user_id'].$setting['secret_key']);
					
					if (isset($_SESSION['lostpass'])) unset($_SESSION['lostpass']);
                    
                    $send = Email::LostYourPass($data['email'], System::Lang('LETTER_LOSTPASS'), $key);
                    if ($send) {
                        $mess = 'На ваш E-mail отправлено письмо с инструкциями для смены пароля';
                        header("Location: /forgot?mess=$mess");
                    }
                } else {
                    $mess = 'Пользователя с таким e-mail не существует';
                }
            }
            
            require_once (ROOT . '/template/'.$setting['template'].'/views/users/forgot.php');
        } else {
            header("Location: ".$setting['script_url']."/lk");
        }
    }
    
    
    
    public function actionChangepass()
    {
        
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0 && $setting['enable_aff'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        if (isset($_GET['email']) && isset($_GET['key'])) {
            $email = trim(strtolower(htmlentities($_GET['email'])));
            $key = $_GET['key'];
			
			if (isset($_SESSION['lostpass']) && $_SESSION['lostpass'] == $email){
                
                echo '<h2 style="text-align:center; padding:2em 0; color:#555">Вы уже восстановили пароль<br />проверьте вашу почту.</h2>';
                exit();
                
            } else $_SESSION['lostpass'] = $email;
            
            $data = User::getUserDataByEmail($email);
            if ($data) {
        
                $setting = System::getSetting();
                $title = 'Вспомнить пароль';
                $noindex = 1;
                $meta_desc = '';
                $meta_keys = '';
                $use_css = 1;
                $is_page = 'lk';
                
                // Создаём пароль клиенту
                $chars="abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ1234567890";
                $max=8;
                $size = strlen($chars)-1;
                $password = null; 
        		while($max--) 
        		$password.=$chars[mt_rand(0,$size)];
                
                // Пишем в базу новый пароль
                $change = User::ChangePass($data['user_id'], $password);
                
                $letter = System::Lang('LETTER_CHANGE_PASS');
                
                // Отсылаем юзеру $password
                Email::ChangePassOk($data['email'], $password, $letter);
                
                if ($change) {
                    require_once (ROOT . '/template/'.$setting['template'].'/views/users/forgot_ok.php');
                    return true;
                }
            } else {
                exit("Ошибка: Пользователь не найден.");
            }
        } else {
            header("Location: /");
        }
    }
    
    
    
    public function actionLogout()
    {
        unset($_SESSION['user_token']);
        unset($_SESSION['user']);
        unset($_SESSION['name']);
        $setting = System::getSetting();
        
        header("Location: /");
    }


    /**
     * REGISTRATION
     */
    public function actionRegistration() {
        $setting = System::getSetting();
        if ($setting['enable_registration'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }

        if (User::isAuth()) {
            System::redirectUrl('/');
        }

        $title = 'Регистрация';
        $meta_desc = $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
		$timer = $now = time();
        $cookie = $setting['cookie'];

        if (isset($_POST['save']) && isset($_COOKIE["$cookie"]) && isset($_POST['time']) && isset($_POST['sign']) && isset($_POST['email'])) {
			if (!empty($_POST['fio'])) {
			    exit('registration disabled');
            }
			
			$timer = intval($_POST['time']);
			$check = $now - $timer;
			
			if ($check < 2) {
			    exit('registration disabled');
            }

			$sign = md5(intval($_POST['time']).'+'.$setting['secret_key']);
			if ($sign != $_POST['sign']) {
			    exit('Error 899');
            }
			
			$email = trim(strtolower(htmlentities($_POST['email'])));
            $name = trim($_POST['name']);
			if (strpos($name, ':/')) {
			    exit('Error 898 SQL syntax is wrong');
            }

            $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';
            $phone = $_POST['phone'];
            $pass = $_POST['pass'];
            $confirm_pass = $_POST['confirm_pass'];

            if ($email && $name && $phone && $pass && $confirm_pass) {
                if ($pass == $confirm_pass) {
                    if (strlen($pass) >= 6) {
                        $user_exists = User::searchUser($email);
                        if ($user_exists) {
                            User::addError('Пользователь с данным e-mail уже существует');
                        } else {
                            $hash = password_hash($pass, PASSWORD_DEFAULT);
                            $reg_date = time();
                            $user_param = "$reg_date;0;;";

                            $user = User::AddNewClient($name, $email, $phone, null, null, null,
                                'user',  null, $reg_date, 'custom', $user_param, 0, $hash,
                                $pass, false, $setting['register_letter']
                            );

                            if ($user) {
                                if (isset($_SESSION['confirm_phone'])) {
                                    User::confirmPhone($user['user_id'], $phone);
                                }

                                Email::SendRegConfirm($name, $email, $user['reg_key'], $pass, $setting['reg_confirm_letter']);
                                $_SESSION['reg_status'] = 1;
                                System::redirectUrl('/lk/registration');
                            }
                        }
                    } else {
                        User::addError('Пароль должен содержать не меньше 6 символов');
                    }
                } else {
                    User::addError('Пароли не совпадают, попробуйте ввести еще раз');
                }
            }
        }

        require_once (ROOT . '/template/'.$setting['template'].'/views/users/registration.php');
    }


    /**
     * ПОДТВЕРЖДЕНИЕ РЕГИСТРАЦИИ
     * @param $req_key
     */
    public function actionRegistrationConfirm($req_key) {
        $user = User::getUserDataToRegkey($req_key);
        if ($user) {
            $res = User::updateUserStatus($user['user_id'], 1);
            if ($res) {
                $_SESSION['reg_status'] = 2;
                header("Location: /lk/registration");
            }
        } else {
            header("Location: /");
        }
    }
}