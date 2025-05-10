<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать отзыв</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/products/">Продукты</a>
        </li>
        <li>
            <a href="/admin/reviews/">Отзывы</a>
        </li>
        <li>Редактировать отзыв</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Редактировать отзыв</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/reviews/">Закрыть</a></li>
            </ul>
        </div>
    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-2">
                <h4>Основное</h4>

                <div class="width-100">
                    <label>Статус: </label>
                    <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($review['status'] == 1) echo 'checked';?>><span>Опубликовано</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($review['status'] == 0) echo 'checked';?>><span>Неопубликовано</span></label>
                        </span>
                </div>
                <p class="width-100"><label>Имя пользователя: </label><input type="text" name="name" value="<?php echo $review['name'];?>" required="required"></p>
                <p class="width-100"><label>Email: </label><input type="text" name="email" value="<?php echo $review['email'];?>"></p>

                <div class="width-100">
                    <label>Оценка: </label>
                            <div class="select-wrap">
                        <select name="rate">
                            <option value="0"<?php if($review['rate'] == 0) echo ' selected="selected"';?>>0</option>
                            <option value="1"<?php if($review['rate'] == 1) echo ' selected="selected"';?>>1</option>
                            <option value="2"<?php if($review['rate'] == 2) echo ' selected="selected"';?>>2</option>
                            <option value="3"<?php if($review['rate'] == 3) echo ' selected="selected"';?>>3</option>
                            <option value="4"<?php if($review['rate'] == 4) echo ' selected="selected"';?>>4</option>
                            <option value="5"<?php if($review['rate'] == 5) echo ' selected="selected"';?>>5</option>
                        </select>
                </div>
                </div>

                <div class="width-100"><label>Продукт: </label>
                    <div class="select-wrap">
                        <select name="product_id">
                            <option value="">Нет</option>
                            <?php $list_select = Product::getProductListOnlySelect();
                        foreach ($list_select as $item):?>
                            <option value="<?php echo $item['product_id'];?>"<?php if($review['product_id'] == $item['product_id']) echo ' selected="selected"';?>><?php echo $item['product_name'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="width-100">
                    <label>Категория: </label>
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="0">Категория</option>
                        <?php $cat_list = Product::getReviewsCats();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id']?>"<?php if($cat['cat_id'] == $review['cat_id']) echo ' selected="selected"';?>><?php echo $cat['cat_name']?></option>
                        <?php endforeach;
                        endif;
                        ?>
                        </select>
                    </div>
                </div>
                
                
                <div class="width-100"><label>Метки: </label>
                    <select class="multiple-select" size="7" multiple="multiple" name="labels[]">
                        <?php $label_list = Product::getReviewsLabels();
                        if($label_list):
                        foreach($label_list as $label):?>
                        <option value="<?php echo $label['label_id'];?>"<?php if($label_map) if(in_array($label['label_id'], $label_map)) echo ' selected="selected"';?>><?php echo $label['label_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                </div>

            </div>
            <div class="col-1-2">
                <h4>Дополнительно</h4>
                <p class="width-100"><label>Дата: </label><?php echo $review['create_date'];?></p>
                <div class="width-100"><label>Фото:</label><?php if($review['attach'] != null){?>
                    <div class="del_img_wrap">
                        <img width="150" src="/images/reviews/<?php echo $review['attach'];?>" alt="">
                        <input type="hidden" name="current_img" value="<?php echo $review['attach'];?>">
                        <span class="del_img_link">
                        <button type="submit" form="del_img" title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                    </div>

                <?php } else {?>
                <input type="file" name="photo">
                <?php } ?>
                </div>
                <p class="width-100"><label>Сайт: </label><input type="text" name="site_url" value="<?php echo $review['site_url'];?>"></p>
                <p class="width-100"><label>Вконтакте: </label><input type="text" name="vk_url" value="<?php echo $review['vk_url'];?>"></p>
                <p class="width-100"><label>Facebook: </label><input type="text" name="fb_url" value="<?php echo $review['fb_url'];?>"></p>
                
                
            </div>
            <div class="col-1-1">
                <h4>Содержание</h4>
                <textarea class="editor" name="text"><?php echo $review['text'];?></textarea>
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>
        </div>
    </div>
    </form>
    
    <form action="/admin/delimg/<?php echo $review['id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/reviews/<?php echo $review['attach'];?>">
        <input type="hidden" name="page" value="admin/reviews/edit/<?php echo $review['id'];?>">
        <input type="hidden" name="table" value="reviews">
        <input type="hidden" name="name" value="attach">
        <input type="hidden" name="where" value="id">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>