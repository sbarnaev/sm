<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать группу каналов</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/channels/">Список каналов</a></li>
        <li><a href="/channels/group/">Группы каналов</a></li>
        <li>Редактировать группу каналов</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Редактировать группу каналов</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/channels/group/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" placeholder="Название" value="<?php echo $group['name'];?>" "required="required"></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
        </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>