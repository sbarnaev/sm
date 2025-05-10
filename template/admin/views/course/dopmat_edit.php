<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Изменить доп. материал</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <input type="submit" name="editdop" value="Сохранить" class="button save button-green-rounding">
        <a class="button-red-rounding" href="/admin/dopmat">Закрыть</a>
    </div>
        
        <div class="admin_form">

                <div class="box2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $dopmat['name'];?>" placeholder="Название" required="required"></p>
                    
                    <p><label>Категория: </label>
                    <?php $cat_list = Course::getDopmatCat(); ?>
                    <select name="cat_id">
                        <option value="">- Выбрать -</option>
                        <?php if($cat_list):
                        foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id'];?>"<?php if($dopmat['cat_id'] == $cat['cat_id']) echo ' selected="selected"';?>><?php echo $cat['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select></p>
                    
                    <p><label>Файл: </label><input type="file" name="file">
                    <input type="hidden" value="<?php echo $dopmat['file'];?>" name="current_file"></p>
                    <?php if(!empty($dopmat['file'])){?>
                        <p><?php echo $dopmat['file'];?>  
                        <br /><input type="submit" value="Удалить" form="delfile" name="delete_file">
                        
                        <br /><a target="_blank" href="/load/dopmat/<?php echo $dopmat['file'];?>">Открыть >></a></p>
                    <?php }?>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                

        </div>
    </form>
    
    <form action="" id="delfile" method="POST">
        <input type="hidden" name="file" value="<?php echo $dopmat['file'];?>">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>