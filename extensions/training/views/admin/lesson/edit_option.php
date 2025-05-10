<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить вариант ответа</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
    </div>
    </div>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить вариант ответа</h3>
            <ul class="nav_button">
                <li><input type="submit" name="editoption" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/training/editlesson/<?php echo $lesson['training_id'];?>/<?php echo $lesson_id;?>/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <!-- 1 вкладка -->

            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="title" value="<?php echo $option['title'];?>" required="required"></p>
                    <p class="width-100"><label>Значение: </label><input type="text" name="value" value="<?php echo $option['value'];?>" required="required"></p>
                    
                    
                    <p class="width-100"><label>Сортировка: </label><input type="text" value="<?php echo $option['sort'];?>" name="sort"></p>
                    <p class="width-100"><label>Баллы за ответ: </label><input type="text" name="points" value="<?php echo $option['points'];?>"></p>
                    <div class="width-100"><label>Правильный ответ: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="option_valid" type="radio" value="1"<?php if($option['valid'] == 1) echo ' checked="checked"';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="option_valid" type="radio" value="0"<?php if($option['valid'] == 0) echo ' checked="checked"';?>><span>Нет</span></label>
                        </span>
                    </div>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
            
                
            </div>

        </div>
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>