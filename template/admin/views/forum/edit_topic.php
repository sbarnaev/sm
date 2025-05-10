<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Изменить тему (ID: <?php echo $topic['topic_id'];?>)</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="edit_topic" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/forum/topics">Закрыть</a></li>
        </ul>
    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $topic['topic_title'];?>" placeholder="Название темы" required="required"></p>

                    <div class="width-100"><label>Категория: </label>
                        <div class="select-wrap">
                        <select name="cat_id">
                    <option value="">-- Выберите --</option>
                    <?php $cat_list = Forum::getCatList();
                    if($cat_list):
                    foreach($cat_list as $cat):?>
                    <option value="<?php echo $cat['cat_id'];?>"<?php if($cat['cat_id'] == $topic['cat_id']) echo ' selected="selected"';?>><?php echo $cat['name'];?></option>
                    <?php endforeach;
                    endif; ?>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Обсуждения: </label>
                       <div class="select-wrap">
                        <select name="discuss">
                    <option value="1"<?php if($topic['discussion'] == 1) echo ' selected="selected"';?>>Открыты</option>
                    <option value="0"<?php if($topic['discussion'] == 0) echo ' selected="selected"';?>>Закрыты</option>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Статус: </label>
                      <div class="select-wrap">
                        <select name="status">
                    <option value="1"<?php if($topic['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                    <option value="0"<?php if($topic['status'] == 0) echo ' selected="selected"';?>>Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-1">
                    <h4>Сообщение:</h4>
                    <textarea class="editor" name="topic_message"><?php echo $topic['topic_message'];?></textarea>
                </div>
</div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>