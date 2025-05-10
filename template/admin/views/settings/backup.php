<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Резервное копирование</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Резервное копирование</li>
    </ul>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    
    <div class="admin_form admin_form--margin-top">
        <div class="row-line">
        <div class="col-1-2">
            <h4>Создать копию БД</h4>
            
            <?php 
            if(!function_exists('exec')) echo '<p style="color:red">Функция exec отключена, обратитесь в техподдержку хостинга</p>';
            else {
                if(!exec('echo EXEC') == 'EXEC') echo '<p style="color:red">Функция exec не работает, обратитесь в техподдержку хостинга</p>';   
            }
            
            ?>
            
            <form action="" method="POST">
                <p><input type="submit" class="button button-green-border-rounding" name="backup" value="Сделать резервную копию">
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>"></p>
            </form>

            <p class="small">* Копия создаётся в gz архиве, для распаковки используйте программу 7Zip или подобную</p>

        </div>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>