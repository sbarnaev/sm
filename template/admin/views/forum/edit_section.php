<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Редактировать раздел форума (ID: <?php echo $section['section_id'];?>)</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="edit" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/forum/section">Закрыть</a></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $section['name'];?>" placeholder="Название раздела" required="required"></p>
                    
                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                    <option value="1"<?php if($section['section_id'] == 1) echo ' selected="selected"';?>>Включен</option>
                    <option value="0"<?php if($section['section_id'] == 0) echo ' selected="selected"';?>>Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p><label>Алиас: </label><input type="text" name="alias" value="<?php echo $section['alias'];?>" placeholder="Алиас раздела"></p>
                    <p><label>Title: </label><input type="text" name="title" value="<?php echo $section['title'];?>" placeholder="Title раздела"></p>
                    <p><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $section['metadesc'];?></textarea></p>
                    <p><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $section['metakeys'];?></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="section_desc"><?php echo $section['section_desc'];?></textarea>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>