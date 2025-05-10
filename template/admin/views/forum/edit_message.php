<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Изменить сообщение (ID: <?php echo $message['mess_id'];?>)</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="edit_mess" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/forum/viewtopic/<?php echo $topic_id;?>">Закрыть</a></li>
        </ul>
    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                    <option value="1"<?php if($message['status'] == 1) echo ' selected="selected"';?>>Опубликовано</option>
                    <option value="0"<?php if($message['status'] == 0) echo ' selected="selected"';?>>Скрыто</option>
                    </select>
                    </div>
                    </div>
                </div>
                
                <div class="col-1-2">
                    
                    <h4>Сообщение:</h4>
                    <textarea class="editor" name="message"><?php echo $message['text'];?></textarea>
                    <input type="hidden" name="mess_id" value="<?php echo $message['mess_id'];?>">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>