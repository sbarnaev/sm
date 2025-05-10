<?php define('BILLINGMASTER', 1);

session_start();
//unset($_SESSION['step']);
//$_SESSION['step'] = 2;

if(!isset($_SESSION['step'])) $_SESSION['step'] = 1;


if(isset($_POST['install']) && isset($_SESSION['step'])){

    $host = htmlentities(trim($_POST['host']));
    $dbname = htmlentities(trim($_POST['db']));
    $user = htmlentities(trim($_POST['user']));
    $pass = htmlentities(trim($_POST['pass']));
    $prefics = htmlentities(trim($_POST['prefics'])).'_';
    $license = htmlentities(trim($_POST['license']));

    $name = htmlentities($_POST['name']);
    $login = htmlentities(trim($_POST['login']));
    $password = htmlentities(trim($_POST['password']));
    $password = password_hash($password, PASSWORD_DEFAULT);
    $email = htmlentities(trim($_POST['email']));
    if(!empty($_POST['key'])) $key = htmlentities(trim($_POST['key']));
    else $key = '';
    $http = htmlentities($_POST['http']);

    $folder = $_SERVER["REQUEST_URI"];
    $folder = rtrim($folder, '/install/');

    // СОЗДАТЬ config.php
    $config = '<?php defined(\'BILLINGMASTER\') or die;
    $host = "'.$host.'";
    $dbname = "'.$dbname.'";
    $user = "'.$user.'";
    $password = "'.$pass.'";
    $prefics = "'.$prefics.'";
	$folder = "'.$folder.'";
    ?>';

    if(@file_put_contents(dirname(__FILE__)."/../config/config.php", $config)) {
        $errors = null;
    } else {
        $errors = "Невозможно создать config.php";
    }

    // ВОССТАНОВИТЬ ДАМП БД

    if(file_exists(dirname(__FILE__)."/dump.sql")) $dump = file_get_contents(dirname(__FILE__)."/dump.sql");
    else exit ('Dump is not found');

    $dump = str_replace("#PREFIX#", $prefics, $dump);
    $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $dump);

    require_once(dirname(__FILE__)."/dbo.php");

    foreach($queries as $query){
        $db = Dbo::getConnection($host, $dbname, $user, $pass);
        $result = $db->query($query);
    }


    // ВНЕСТИ НАСТРОЙКИ

    // Создаём ключ API
    $chars="abcdefghigklmnopqrstuvwxyz123456";
    $max=10;
    $size = strlen($chars)-1;
    $api_key = null;
    while($max--)
        $api_key.=$chars[mt_rand(0,$size)];

    // Создаём приватный ключ API
    $chars="abcdefghigklmnopqrstuvwxyz1234567890-";
    $max=15;
    $size = strlen($chars)-1;
    $private_key = null;
    while($max--)
        $private_key.=$chars[mt_rand(0,$size)];

    $script_url = $http .'://'.$_SERVER["HTTP_HOST"] .$_SERVER["REQUEST_URI"];
    $script_url = str_replace("/install/", "", $script_url );


    //$db = Dbo::getConnection();  
    $sql = 'UPDATE '.$prefics.'settings SET secret_key = :secret_key, license_key = :license_key, security_key = :security_key, script_url = :script_url, private_key = :private_key WHERE setting_id = 1';
    $result = $db->prepare($sql);
    $result->bindParam(':secret_key', $api_key, PDO::PARAM_STR);
    $result->bindParam(':security_key', $key, PDO::PARAM_STR);
    $result->bindParam(':script_url', $script_url, PDO::PARAM_STR);
    $result->bindParam(':license_key', $license, PDO::PARAM_STR);
    $result->bindParam(':private_key', $private_key, PDO::PARAM_STR);
    $result->execute();


    // СОЗДАТЬ АДМИНА

    $_SESSION['step'] = 2;
    $role = 'admin';
    $reg_key = md5(time());
    $status = 1;

    //$db = Dbo::getConnection();
    $sql = 'INSERT INTO '.$prefics.'users (user_name, login, email, pass, role, reg_key, status ) 
            VALUES (:user_name, :login, :email, :pass, :role, :reg_key, :status)';

    $result = $db->prepare($sql);
    $result->bindParam(':user_name', $name, PDO::PARAM_STR);
    $result->bindParam(':login', $login, PDO::PARAM_STR);
    $result->bindParam(':email', $email, PDO::PARAM_STR);
    $result->bindParam(':pass', $password, PDO::PARAM_STR);
    $result->bindParam(':role', $role, PDO::PARAM_STR);
    $result->bindParam(':reg_key', $reg_key, PDO::PARAM_STR);
    $result->bindParam(':status', $status, PDO::PARAM_INT);
    $result->execute();

    if (isset($_POST['demo-data']) && file_exists(dirname(__FILE__)."/demo/dump.sql")) {
        $dump = file_get_contents(dirname(__FILE__)."/demo/dump.sql");
        $dump = str_replace("#PREFIX#", "{$prefics}", $dump);
        $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $dump);

        foreach ($queries as $query) {
            $result = $db->query($query);
        }

        require_once __DIR__.' /demo/params.php';
        $file_errors = [];
        if ($folders) { // Создать папки
            foreach($folders as $folder) {
                $path = __DIR__."/..{$folder[0]}";
                if (!is_dir($path)) {
                    if (!mkdir($path)) {
                        $file_errors[] = $path;
                    }
                }
            }
        }

        foreach ($files as $file) {
            $source_file = __DIR__."/demo/files{$file[0]}";
            $dest_file = __DIR__."/../{$file[1]}";
            if (!copy($source_file, $dest_file)) {
                $file_errors[] = $dest_file;
            }
        }

        if (!empty($file_errors)) {
            $error_message = 'Не удалось скопировать следующие файлы:<br>' . implode(', ', $file_errors);
            die($error_message);
        }
    }
}



if($_SESSION['step'] == 1):

// Создаём префикс таблиц
    $chars="abcdefghigklmnopqrstuvwxyz";
    $max=3;
    $size = strlen($chars)-1;
    $prefics = null;
    while($max--)
        $prefics.=$chars[mt_rand(0,$size)];
    ?>
    <html>
    <head>
        <title>Install School Master</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
    <div id="header">
        <div class="layout">
            <div class="header-inner">
                <h1>Установка School Master</h1>
                <img src="img/info.png" alt="">
            </div>
        </div>
    </div>
    <div class="container">
        <div class="layout white">
            <h2>1. &nbsp;&nbsp;Проверка системных требований</h2>
            <?php if (version_compare(PHP_VERSION, '7.1.1', '>')) {
                $php_status = 1;
                $php_mess = 'Версия PHP - актуальна';
                $go = 1;
            } else {
                $php_status = 0;
                $php_mess = 'Версия PHP - устарела';
                $go = 0;
            }?>
            <p class="check-item">
  <span class="status-wrap">
  <?php if($php_status == 1) echo '<span class="stat-yes"><i class="icon-stat-yes"></i></span>';
  else echo '<span class="stat-no"></span>';?>
  </span>
                <span><?php echo $php_mess;?></span></p>


            <?php if(extension_loaded('ionCube Loader')) {
                $ion_status = 1;
                $ion_mess = 'IonCube установлен';
                $go = 1;
            }
            else {
                $ion_status = 0;
                $ion_mess = 'Нужно установить IonCube (решается через техподдержку хостинга)';
                $go = 0;
            }
            ?>
            <p class="check-item">
    <span class="status-wrap">
  <?php if($ion_status == 1) echo '<span class="stat-yes"><i class="icon-stat-yes"></i></span>';
  else '<span class="stat-no"></span>';?>
  </span>
                <span><?php echo $ion_mess;?></span></p>


            <?php if(function_exists('zip_open') && function_exists('zip_read')){

                //extension_loaded('zlib')
                $zip_status = 1;
                $zip_mess = 'Поддержка ZIP работает';
                $go = 1;
            }
            else {
                $zip_status = 0;
                $zip_mess = 'Нужно включить поддержку ZIP (через техподдержку хостинга)';
                $go = 1;
            }?>
            <p class="check-item">
  <span class="status-wrap">
  <?php if($zip_status == 1) echo '<span class="stat-yes"><i class="icon-stat-yes"></i></span>';
  else '<span class="stat-no"></span>';?>
  </span>
                <span><?php echo $zip_mess;?></span></p>



            <?php $file = dirname(__FILE__)."/../config/routes.php";

            if(is_writable($file)) {
                $wr_status = 1;
                $wr_mess = 'Файл конфигурации доступен для записи';
                $go = 1;
            } else {
                $wr_status = 0;
                $wr_mess = 'Файл конфигурации не доступен для записи';
                $go = 0;
            }
            ?>

            <p class="check-item">
    <span class="status-wrap">
  <?php if($wr_status == 1) echo '<span class="stat-yes"><i class="icon-stat-yes"></i></span>';
  else '<span class="stat-no"></span>';?>
  </span>
                <span><?php echo $wr_mess;?></span></p>
        </div>


        <?php if($go == 1):?>
            <form action="" method="POST">
                <div class="layout white">
                    <h2>2. &nbsp;&nbsp;Подключение к БД</h2>
                    <p><input type="text" name="host" placeholder="Имя хоста" required="required"> Имя хоста (обычно localhost)</p>
                    <p><input type="text" name="db" placeholder="Имя базы данных" required="required"> Имя базы данных</p>
                    <p><input type="text" name="user" placeholder="Имя пользователя БД" autocomplete="off" required="required"> Имя пользователя БД</p>
                    <p><input type="text" name="pass" autocomplete="off" placeholder="Пароль пользователя БД"> Пароль пользователя БД</p>
                    <p><input type="text" name="prefics" value="<?php echo $prefics;?>" placeholder="Префикс таблиц" required="required"> Префикс таблиц</p>
                </div>

                <div class="layout white">
                    <h2>3. &nbsp;&nbsp;Данные админа</h2>
                    <p><input type="text" name="name" placeholder="Ваше имя" autocomplete="off" required="required"> Ваше имя</p>
                    <p><input type="text" name="login" placeholder="Логин" autocomplete="off" required="required"> Логин</p>
                    <p><input type="text" name="password" placeholder="Пароль" autocomplete="off" required="required"> Пароль (можно использовать символы: A-z, 0-9)</p>
                    <p><input type="email" name="email" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" placeholder="E-mail" autocomplete="off" required="required"> Email</p>
                    <p><input type="text" name="key" autocomplete="off" placeholder="Ключ админки"> Ключ админки</p>
                    <p>
<span class="select-wrap">
  <select name="http">
<option value="http">http</option>
<option value="https">https</option>
</select>
  </span> Протокол</p>
                </div>

                <div class="layout white">
                    <h2>4. &nbsp;&nbsp;Лицензия</h2>
                    <p><input type="text" name="license" autocomplete="off" placeholder="Ключ лицензии" required="required"> <a href="https://lk.school-master.ru/getlicense?ext=bm" target="_blank">Получить ключ</a></p>
                </div>

                <div class="layout white">
                    <h2>5. &nbsp;&nbsp;Дополнительно</h2>
                    <p>
                        <label class="custom-chekbox-wrap" style="display:flex;align-items: center">
                            <input type="checkbox" name="demo-data" style="width: auto;min-width: 31px;">
                            <span class="custom-chekbox"></span>Установить демо-данные
                        </label>
                    </p>
                </div>

                <div class="layout">
                    <p><input type="submit" name="install" value="Установить" class="button button-install"></p>
                </div>
            </form>
        <?php endif;?>
    </div>
    <footer id="footer">
        <div class="layout">
            <p>School Master. <?php echo date ( 'Y' ) ; ?>. Приложение распространияется по лицензии</a>. <a target="_blank" href="https://school-master.ru">Сайт</a></p>
        </div>
    </footer>
    </body>
    </html>
<?php endif; ?>

<?php if(isset($_SESSION['step']) && $_SESSION['step'] == 2):?>

    <html>
    <head>
        <title>Install School Master</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>
    <div id="header">
        <div class="layout">
            <h1>Установка School Master</h1>
            <img src="img/info.png" alt="">
        </div>
    </div>

    <div class="layout white">
        <form action="" method="POST">
            <h2>Установка завершена</h2>
            <h2 class="warning">Обязательно удалите вручную папку install</h2>
            <p>Перейти на <a href="<?php echo $script_url;?>">сайт</a>,</p>
            <?php if(!empty($_POST['key'])){?>
                <p>или перейти в <a href="<?php echo $script_url;?>/admin?key=<?php echo $key;?>">админ панель</a></p>
                <p>Запишите адрес вашей админки:<br /><?php echo $script_url.'/admin?key='. $key;?></p>
            <?php } else {?>
                <p>или перейти в <a href="<?php echo $script_url;?>/admin">админ панель</a></p>
            <?php } ?>
        </form>
    </div>
    <footer id="footer">
        <div class="layout">
            <p>School Master. <?php echo date ( 'Y' ) ; ?>. Приложение распространияется по лицензии</a>. <a target="_blank" href="https://school-master.ru">Сайт</a></p>
        </div>
    </footer>
    </body>
    </html>

<?php endif;
unset($_SESSION['step']);?>