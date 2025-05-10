<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить редирект</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/redirect/">Редиректы</a></li>
        <li>Изменить редирект</li>
    </ul>
    <form action="" method="POST">

        <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать редирект</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/redirect/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><strong>ID: <?php echo $redirect['id'];?></strong></p>
                    <p class="width-100"><?php echo $setting['script_url'].'/rdr/'.$redirect['id'];?></p>
                    <p class="width-100"><label>Название: </label><input type="text" name="title" placeholder="Название редиректа" required="required" value="<?php echo $redirect['title']?>"></p>

                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($redirect['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($redirect['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Категория: </label>
                        <div class="select-wrap">
                        <select name="cat_id">
                    <?php $cat_list = Redirect::getRdrCatList();
                    foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id'];?>"<?php if($cat['cat_id'] == $redirect['cat_id']) echo ' selected="selected"';?>><?php echo $cat['name'];?></option>
                        <?php endforeach;?>
                    </select>
                        </div>
                    </div>
                    <p class="width-100"><label>URL: </label><input type="text" name="url" placeholder="URL адрес" value="<?php echo $redirect['url']?>" required="required"></p>
                    <p class="width-100">Описание:<br /><textarea name="rdr_desc" rows="3" cols="40"><?php echo $redirect['rdr_desc']?></textarea></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>Ограничения</h4>
                    <p class="width-100"><label class="d-inline">Переходов: </label><?php echo $redirect['hits'];?></p>
                    <p class="width-100"><label>Лимит переходов: </label><input type="text" name="limit" value="<?php echo $redirect['limit_hits']?>" placeholder="кол-во переходов"></p>
                    <p class="width-100"><label>Дата завершения</label><input type="text" class="datetimepicker" value="<?php echo date("d.m.Y H:i", $redirect['end_date']);?>" name="end" autocomplete="off"></p>
                    <p class="width-100" title="Куда направлять после окончания действия"><label>URL куда направлять после окончания срока действия: </label><input type="text" name="alt_url" value="<?php echo $redirect['alt_url'];?>" placeholder="URL адрес"></p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>