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
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
        <li>Изменить категорию</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="editcat" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/courses/cats/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
        <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $category['name'];?>" placeholder="Имя категории" required="required"></p>

                    <div class="width-100">
                        <label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($category['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($category['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>
                    <div class="width-100"><label>Обложка: </label><input type="file" name="cover">
                    <input type="hidden" name="current_img" value="<?php echo $category['cover'];?>">
                    </div>
                    <?php if(!empty($category['cover'])) {?>
                        <div class="del_img_wrap">
                        <img src="/images/course/category/<?php echo $category['cover'];?>" alt="" width="150">
                        <span class="del_img_link">
                            <button type="submit" form="del_img" title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                        </div>
                    <?php }?>
                    <p><label>Alt: </label><input type="text" size="35" value="<?php echo $category['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст"></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p><label>Алиас: </label><input type="text" name="alias" value="<?php echo $category['alias'];?>" placeholder="Алиас категории"></p>
                    <p><label>Title: </label><input type="text" name="title" value="<?php echo $category['title'];?>" placeholder="Title категории"></p>
                    <p>Meta Description:<br /><textarea name="meta_desc" rows="3" cols="40"><?php echo $category['meta_desc'];?></textarea></p>
                    <p>Meta Keys:<br /><textarea name="meta_keys" rows="3" cols="40"><?php echo $category['meta_keys'];?></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <label>Описание:</label>
                    <textarea class="editor" name="cat_desc"><?php echo $category['cat_desc'];?></textarea>
                </div>
        </div>

        </div>
    </form>
    
    <form action="/admin/delimg/<?php echo $category['cat_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/course/category/<?php echo $category['cover'];?>">
        <input type="hidden" name="page" value="admin/courses/cats/edit/<?php echo $category['cat_id'];?>">
        <input type="hidden" name="table" value="course_category">
        <input type="hidden" name="name" value="cover">
        <input type="hidden" name="where" value="cat_id">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>