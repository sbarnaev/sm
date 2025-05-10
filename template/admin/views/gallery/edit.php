<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Редактировать изображение</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
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
            <li>Редактировать изображение</li>
        </ul>
        <div class="traning-top">
            <h3 class="traning-title">Редактировать изображение</h3>
            <ul class="nav_button">
                <li><input type="submit" name="editimg" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/gallery">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название</label><input type="text" name="title" value="<?php echo $img['title'];?>" placeholder="Название изображения"></p>

                    <div class="width-100"><label>Категория</label>
                        <div class="select-wrap">
                        <select name="cat_id">
                        <?php $cat_list = Gallery::getCatList();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id'];?>"<?php if($cat['cat_id'] == $img['cat_id']) echo ' selected="selected"';?>><?php echo $cat['cat_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>
                    <p class="width-100"><label>Файл</label><input type="file" name="image"></p>
                    <input type="hidden" name="current_img" value="<?php echo $img['file'];?>">

                    <?php if(!empty($img['file'])) {?>

                    <div class="del_img_wrap">
                        <img style="width:150px" src="/images/gallery/<?php echo $img['file'];?>">
                        <span class="del_img_link">
                            <button type="submit" form="del_img" value=" " title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                    </div>
                    <?php }?>

                    <div class="width-100 mt-20 mb-0"><label>Статус</label>
                        <div class="select-wrap">
                        <select name="status">
                        <option value="1"<?php if($img['status'] == 1) echo ' selected="selected"';?>>Включен</option>
                        <option value="0"<?php if($img['status'] == 0) echo ' selected="selected"';?>>Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>Дополнительно</h4>
                    <p class="width-100"><label>Alt</label><input type="text" value="<?php echo $img['alt'];?>" name="img_alt"></p>
                    <p class="width-100">Описание:<br /><textarea name="img_desc" rows="3" cols="40"><?php echo $img['item_desc'];?></textarea></p>
                    <p class="width-100"><label>Ссылка</label><input type="url" name="link" value="<?php echo $img['link'];?>"></p>
                </div>
            </div>
        </div>
    </form>
    
    <form action="/admin/delimg/<?php echo $img['id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/gallery/<?php echo $img['file'];?>">
        <input type="hidden" name="page" value="admin/gallery/edit/<?php echo $img['id'];?>">
        <input type="hidden" name="table" value="gallery_items">
        <input type="hidden" name="name" value="file">
        <input type="hidden" name="where" value="id">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>