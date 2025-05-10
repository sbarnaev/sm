<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Создать доп. материал</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="adddop" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/dopmat">Закрыть</a></li>
        </ul>
    </div>
        
        <div class="admin_form">

                <div class="box2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название" required="required"></p>
                    
                    <div class="width-100"><label>Категория: </label>
                    <?php $cat_list = Course::getDopmatCat(); ?>
                        <div class="select-wrap">
                        <select name="cat_id">
                            <option value="">- Выбрать -</option>
                            <?php if($cat_list):
                            foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id'];?>"><?php echo $cat['name'];?></option>
                            <?php endforeach;
                            endif;?>
                        </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Файл: </label><input type="file" name="file" required="required"></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>