<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать категорию</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/cats">Список категорий</a></li>
        <li>Создать категорию</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addcat" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/training/cats/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название:</label>
                        <input type="text" name="name" placeholder="Название категории" required="required">
                    </p>
                    
                    <div class="width-100"><label>Родительская категория:</label>
                        <div class="select-wrap">
                            <select name="parent_cat">
                                <option value="0">Нет</option>
                                <?php $cat_list = TrainingCategory::getCatList(false);
                                if($cat_list):
                                    foreach($cat_list as $cat):?>
                                        <option value="<?=$cat['cat_id'];?>"><?=$cat['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Сортировка:</label>
                        <input type="text" name="sort" value="<?=TrainingCategory::getFreeSort();?>">
                    </p>

                    <div class="width-100"><label>Статус:</label>
                        <div class="select-wrap">
                            <select name="status">
                                <option value="1">Включен</option>
                                <option value="0">Отключен</option>
                            </select>
                        </div>
                    </div>
                    
                    <p class="width-100"><label>Обложка:</label>
                        <input type="file" name="cover">
                    </p>
                    
                    <p class="width-100"><label>Alt:</label>
                        <input type="text" size="35" name="img_alt" placeholder="Альтернативный текст">
                    </p>
                    
                    
                    <div class="width-100"><label>Описание категории:</label>
                        <textarea name="cat_desc"></textarea>
                    </div>
                    
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас:</label>
                        <input type="text" name="alias" placeholder="Алиас категории">
                    </p>
                    
                    <p class="width-100"><label>Title:</label>
                        <input type="text" name="title" placeholder="Title категории">
                    </p>
                    
                    <p class="width-100"><label>Meta Description:</label>
                        <textarea name="meta_desc" rows="3" cols="40"></textarea>
                    </p>
                    
                    <p class="width-100"><label>Meta Keys:</label>
                        <textarea name="meta_keys" rows="3" cols="40"></textarea>
                    </p>
                </div>
            </div>
        </div>
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>