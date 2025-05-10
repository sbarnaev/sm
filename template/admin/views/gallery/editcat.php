<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить категорию</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">

        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li>
                <a href="/admin/gallery">Галерея</a>
            </li>
            <li>Изменить категорию</li>
        </ul>
        <div class="traning-top">
            <h3 class="traning-title">Изменить категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="editcat" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/gallery/cats">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->

            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Имя категории" required="required" value="<?php echo $cat['cat_name'];?>"></p>
                    
                    <div class="width-100"><label>Родительская категория: </label>
                        <div class="select-wrap">
                        <select name="parent_id">
                        <option value="0">-- Выбрерите --</option>
                        <?php $cat_list = Gallery::getCatList();
                        if($cat_list):
                        foreach($cat_list as $category):?>
                        <option value="<?php echo $category['cat_id']?>"<?php if($category['cat_id'] == $cat['parent_id']) echo ' selected="selected"';?>><?php echo $category['cat_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>
                    
                    <p class="width-100"><label>Обложка: </label><input type="file" name="cover"></p>
                    <input type="hidden" name="current_img" value="<?php echo $cat['cat_cover'];?>"></p>
                    <?php if(!empty($cat['cat_cover'])) {?>
                        <img style="width:150px" src="/images/gallery/cats/<?php echo $cat['cat_cover'];?>">
                        
                        <span class="del_img_link">
                            <input type="submit" form="del_img" value=" " title="Удалить изображение с сервера?" name="del_img">
                        </span>
                    <?php }?>
                    
                    <p class="width-100"><label>Описание:</label><textarea name="cat_desc" rows="3" cols="40"><?php echo $cat['cat_desc'];?></textarea></p>
                    <p class="width-100"><label>Порядок: </label><input type="text" value="<?php echo $cat['sort'];?>" name="sort" style="width:100px"></p>
                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                            <option value="1"<?php if($cat['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                            <option value="0"<?php if($cat['status'] == 0) echo ' selected="selected"';?>>Отключен</option>
                        </select>
                        </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" value="<?php echo $cat['alias'];?>" placeholder="Алиас категории"></p>
                    <p class="width-100"><label>Title: </label><input type="text" value="<?php echo $cat['cat_title']?>" name="title" placeholder="Title категории"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $cat['meta_desc']?></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $cat['meta_keys']?></textarea></p>
                </div>
            </div>

        </div>
    </form>
    
    <form action="/admin/delimg/<?php echo $cat['cat_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/gallery/cats/<?php echo $cat['cat_cover'];?>">
        <input type="hidden" name="page" value="admin/gallery/editcat/<?php echo $cat['cat_id'];?>">
        <input type="hidden" name="table" value="gallery_cats">
        <input type="hidden" name="name" value="cat_cover">
        <input type="hidden" name="where" value="cat_id">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>