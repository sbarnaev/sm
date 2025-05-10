<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить категорию</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training">Тренинги</a></li>
        <li><a href="/admin/training/cats">Список категорий</a></li>
        <li>Изменить категорию</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="editcat" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/training/cats/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название:</label>
                        <input type="text" name="name" value="<?=$cat['name'];?>" placeholder="Название категории" required="required">
                    </p>

                    <div class="width-100"><label>Родительская категория:</label>
                        <div class="select-wrap">
                            <select name="parent_cat">
                                <option value="0">Нет</option>
                                <?php $cat_list = TrainingCategory::getCatList(false);
                                if($cat_list):
                                    foreach($cat_list as $category):
                                        if ($category['cat_id'] == $cat['cat_id']) {
                                            continue;
                                        }?>
                                        <option value="<?=$category['cat_id'];?>"<?php if($cat['parent_cat'] == $category['cat_id']) echo ' selected="selected"';?>><?=$category['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>
                    
                    
                    <p class="width-100"><label>Сортировка:</label>
                        <input type="text" value="<?=$cat['sort'];?>" name="sort">
                    </p>
                    
                    <div class="width-100"><label>Статус:</label>
                        <div class="select-wrap">
                            <select name="status">
                                <option value="1"<?php if($cat['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                                <option value="0"<?php if($cat['status'] == 0) echo ' selected="selected"';?>>Отключен</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="width-100"><label>Обложка:</label><input type="file" name="cover">
                        <input type="hidden" name="current_img" value="<?=$cat['cover'];?>">
                    </div>

                    <?php if(!empty($cat['cover'])):?>
                        <div class="del_img_wrap">
                        <img src="/images/training/category/<?=$cat['cover'];?>" alt="" width="150">
                        <span class="del_img_link">
                            <button type="submit" form="del_img" title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                        </div>
                    <?php endif;?>

                    <p><label>Alt:</label>
                        <input type="text" size="35" value="<?=$cat['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст">
                    </p>
                    
                    <div class="width-100"><label>Описание категории:</label>
                        <textarea name="cat_desc"><?=$cat['cat_desc'];?></textarea>
                    </div>
                    
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас:</label>
                        <input type="text" name="alias" value="<?=$cat['alias'];?>" placeholder="Алиас категории">
                    </p>

                    <p class="width-100"><label>Title:</label>
                        <input type="text" name="title" value="<?=$cat['title'];?>" placeholder="Title категории">
                    </p>

                    <p class="width-100"><label>Meta Description:</label>
                        <textarea name="meta_desc" rows="3" cols="40"><?=$cat['meta_desc'];?></textarea>
                    </p>

                    <p class="width-100"><label>Meta Keys:</label>
                        <textarea name="meta_keys" rows="3" cols="40"><?=$cat['meta_keys'];?></textarea>
                    </p>
                </div>
                
            </div>

        </div>
    </form>
    
    <form action="/admin/delimg/<?=$cat['cat_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/training/category/<?=$cat['cover'];?>">
        <input type="hidden" name="page" value="admin/training/editcat/<?=$cat['cat_id'];?>">
        <input type="hidden" name="table" value="training_cats">
        <input type="hidden" name="name" value="cover">
        <input type="hidden" name="where" value="cat_id">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>