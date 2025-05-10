<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать страницу</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/statpages/">Статичные страницы</a>
        </li>
        <li>Создать страницу</li>
    </ul>
    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать страницу</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addpage" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/statpages/">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название страницы" required="required"></p>
                    
                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                    <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                </span>
                    </div>
					
					<p class="width-100"><label>Загрузить с URL: </label><input type="text" name="curl" placeholder="URL страницы"></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас страницы "></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title страницы"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <p class="width-100"><textarea class="editor" name="content"></textarea></p>
                    <div class="width-100"><label>Шаблон: </label>
                        <div class="select-wrap">
                        <select name="tmpl">
                        <option value="1">Стандарт</option>
                        <option value="0">Без шаблона</option>
                    </select>
                    </div>
                    </div>
                </div>
                
                <div class="col-1-2">
                    <h4>CSS и JS в head</h4>
                    <textarea name="in_head" rows="6" style="width: 96%"></textarea>
                </div>
                
                <div class="col-1-2">
                    <h4>CSS и JS перед /body</h4>
                    <textarea name="in_body" rows="6" style="width: 96%"></textarea>
                </div>

                <div class="col-1-1">
                    <p class="width-100">* CSS: <code>&lt;link rel="stylesheet" href="/template/css/style.css" type="text/css" /&gt;</code><br />
                    * JS: <code>&lt;script src="jquery-1.12.4.min.js"&gt;&lt;/script&gt;</code></p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>