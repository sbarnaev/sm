<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Просмотр сообщения</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/feedback/">Обратная связь</a>
        </li>
        <li>Просмотр сообщения</li>
    </ul>
    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Просмотр сообщения</h3>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/feedback/">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2 mb-20">
                    <p class="width-100"><label class="d-inline">ID: </label><?php echo $message['id'];?></p>
                    <p class="width-100"><label class="d-inline">Имя: </label><?php echo $message['name'];?></p>
                    <p class="width-100"><label class="d-inline">Email: </label><a href="mailto:<?php echo $message['email'];?>"><?php echo $message['email'];?></a></p>
                    
                    <div><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                        <option value="0"<?php if($message['status'] == 0) echo ' selected="selected"';?>>Не просмотрен</option>
                        <option value="1"<?php if($message['status'] == 1) echo ' selected="selected"';?>>Просмотрен</option>
                        <option value="2"<?php if($message['status'] == 2) echo ' selected="selected"';?>>Требует внимания</option>
                        <option value="9"<?php if($message['status'] == 9) echo ' selected="selected"';?>>Срочный</option>
                    </select>
                    </div>
                    </div>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2 mb-20">
                    <div class="round-block">
                    <p class="width-100"><label class="d-inline">Дата создания: </label><?php echo date("d-m-Y H:i:s", $message['create_date']);?></p>
                    </div>
                </div>

                <div class="col-1-1">
                    <h4>Сообщение</h4>
                    <div class="round-block"><?php echo $message['text'];?></div>
                </div>

        </div>
        
        <div class="row-line">
        <div class="col-1-1">
            <h4>Заметка</h4>
            <p><textarea class="editor" name="comment"><?php echo $message['comment'];?></textarea></p>
        </div>
        </div>
        
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>