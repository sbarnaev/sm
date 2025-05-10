<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Просмотр отправки письма</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/emailog/">Лог писем</a></li>
        <li>Просмотр записи</li>
    </ul>

    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Просмотр факта отправки письма</h3>
            <ul class="nav_button">
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/emailog/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100">Тема: <?php echo $log['type']?></p>
                    <p class="width-100">Email: <?php echo $log['email']?></p>
                    
                    <p class="width-100">Время: <?php echo date("d-m-Y H:i:s", $log['datetime']);?></p>
                    
                </div>
                
                <div class="col-1-1">
                    <p><label>Текст письма: </label><textarea name="letter" class="editor" cols="55" rows="3"><?php echo $log['letter'];?></textarea></p>
                </div>
                
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>