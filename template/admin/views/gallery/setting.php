<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки галереи</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки галереи</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Сохранено!</div>
    <?php endif?>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/nastr-tren.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Настройки галереи</h3></div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="savegallery" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4 class="h4-border">Основное</h4>

                    <p><label>Галерея включена:</label>
                        <select name="status">
                            <option value="1"<?php if($enable == 1) echo ' selected="selected"';?>>Включен</option>
                            <option value="0"<?php if($enable == 0) echo ' selected="selected"';?>>Отключен</option>
                        </select>
                    </p>

                    <p><label>Комментарии:</label>
                        <select name="gallery[params][comments]">
                            <option value="1"<?php if($params['params']['comments'] == 1) echo ' selected="selected"';?>>Включены</option>
                            <option value="0"<?php if($params['params']['comments'] == 0) echo ' selected="selected"';?>>Отключены</option>
                        </select>
                    </p>

                    <p><label>Код комментариев (в body):</label>
                        <textarea rows="10" cols="45" name="gallery[params][commentcode]"><?=$params['params']['commentcode']?></textarea>
                    </p>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4 class="h4-border">SEO</h4>

                    <p><label>Title:</label>
                        <input type="text" name="gallery[params][title]" value="<?=$params['params']['title']?>">
                    </p>

                    <p><label>Заголовок H1:</label>
                        <input type="text" name="gallery[params][h1]" value="<?=$params['params']['h1']?>">
                    </p>

                    <p><label>Meta Desc:</label>
                        <textarea name="gallery[params][desc]"><?=$params['params']['desc']?></textarea>
                    </p>

                    <p><label>Meta Keys:</label>
                        <textarea name="gallery[params][keys]"><?=$params['params']['keys']?></textarea>
                    </p>
                </div>
            </div>

            <h4 class="h4-border mt-20">Внешний вид</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p><label>Ширина превьюшек, пиксели:</label>
                        <input type="text" size="4" name="gallery[params][thumb_w]" value="<?=$params['params']['thumb_w']?>">
                    </p>

                    <p><label>Ширина элемента галереи, пиксели:</label>
                        <input type="text" size="4" name="gallery[params][width]" value="<?=$params['params']['width']?>">
                    </p>

                    <p><label>Стиль галереи:</label>
                        <select name="gallery[params][style]">
                            <option value="columns"<?php if($params['params']['style'] == 'columns') echo ' selected="selected"';?>>Колонки</option>
                            <option value="justified"<?php if($params['params']['style'] == 'justified') echo ' selected="selected"';?>>Ряды</option>
                            <option value="grid"<?php if($params['params']['style'] == 'grid') echo ' selected="selected"';?>>Плитка</option>
                            <option value="slider"<?php if($params['params']['style'] == 'slider') echo ' selected="selected"';?>>Слайдер</option>
                            <option value="carousel"<?php if($params['params']['style'] == 'carousel') echo ' selected="selected"';?>>Карусель</option>
                        </select>
                    </p>
                </div>

                <div class="col-1-2">
                    <p><label>Качество превьюшек, %:</label>
                        <input type="text" size="4" name="gallery[params][thumb_q]" value="<?=$params['params']['thumb_q']?>">
                    </p>

                    <p><label>Высота элемента галереи, пиксели:</label>
                        <input type="text" size="4" name="gallery[params][height]" value="<?=$params['params']['height']?>">
                    </p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>