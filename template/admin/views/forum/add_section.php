<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Создать раздел форума</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="add" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/forum/section">Закрыть</a></li>
        </ul>


    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название раздела" required="required"></p>

                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                    <option value="1">Включен</option>
                    <option value="0">Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас раздела"></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title раздела"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="section_desc"></textarea>
                </div>
</div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>