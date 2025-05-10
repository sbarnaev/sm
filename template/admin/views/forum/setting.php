<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки форума</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки форума</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки форума</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
        <div class="row-line">
            <div class="col-1-2">
                <h4 class="h4-border">Основное</h4>
                
                <p class="width-100"><label>Форум включен: </label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </p>
                <p class="width-100"><label>Ключ лицензии:</label><input type="text" size="40" name="forum[params][license]" value="<?php echo $params['params']['license']?>"></p>
                <div class="width-100"><label>Уведомлять о новых темах:</label>
                    <div class="select-wrap">
                    <select name="forum[params][topic_notif]">
                        <option value="0"<?php if($params['params']['topic_notif'] == 0) echo ' selected="selected"';?>>Никого</option>
                        <option value="1"<?php if($params['params']['topic_notif'] == 1) echo ' selected="selected"';?>>Админа + техподдержку</option>
                        <option value="2"<?php if($params['params']['topic_notif'] == 2) echo ' selected="selected"';?>>Только админа </option>
                        <option value="3"<?php if($params['params']['topic_notif'] == 3) echo ' selected="selected"';?>>Только техподдержку</option>
                    </select>
                    </div>
                </div>
                
                <div class="width-100"><label>Уведомлять о новых сообщениях:</label>
                    <div class="select-wrap">
                    <select name="forum[params][mess_notif]">
                        <option value="0"<?php if($params['params']['mess_notif'] == 0) echo ' selected="selected"';?>>Никого</option>
                        <option value="1"<?php if($params['params']['mess_notif'] == 1) echo ' selected="selected"';?>>Админа + техподдержку</option>
                        <option value="2"<?php if($params['params']['mess_notif'] == 2) echo ' selected="selected"';?>>Только админа </option>
                        <option value="3"<?php if($params['params']['mess_notif'] == 3) echo ' selected="selected"';?>>Только техподдержку</option>
                    </select>
                    </div>
                </div>
                
                <div class="width-100"><label>Модерация новых тем:</label>
                    <div class="select-wrap">
                    <select name="forum[params][topic_moder]">
                        <option value="0"<?php if($params['params']['topic_moder'] == 0) echo ' selected="selected"';?>>Нет</option>
                        <option value="1"<?php if($params['params']['topic_moder'] == 1) echo ' selected="selected"';?>>Да</option>
                    </select>
                    </div>
                </div>
                
                <div class="width-100"><label>Модерация новых сообщений:</label>
                    <div class="select-wrap">
                    <select name="forum[params][mess_moder]">
                        <option value="0"<?php if($params['params']['mess_moder'] == 0) echo ' selected="selected"';?>>Нет</option>
                        <option value="1"<?php if($params['params']['mess_moder'] == 1) echo ' selected="selected"';?>>Да</option>
                    </select>
                    </div>
                </div>
                
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>
            
            <div class="col-1-2">
                <h4 class="h4-border">SEO</h4>
                <p class="width-100"><label>Название форума:</label> <input type="text" name="forum[params][name]" value="<?php echo $params['params']['name'];?>" placeholder="название форума"></p>
                <p class="width-100"><label>Title форума:</label> <input type="text" name="forum[params][title]" value="<?php echo $params['params']['title'];?>" placeholder="title форума"></p>
                <p class="width-100"><label>Meta desc:</label><textarea name="forum[params][metadesc]" cols="45" rows="5"><?php echo $params['params']['metadesc'];?></textarea></p>
                <p class="width-100"><label>Meta keys:</label><textarea name="forum[params][metakeys]" cols="45" rows="5"><?php echo $params['params']['metakeys'];?></textarea></p>
            </div>
        </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>