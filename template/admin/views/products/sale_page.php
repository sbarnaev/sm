<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить страницу акций</h1>
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
            <a href="/admin/sales/">Акции</a>
        </li>
        <li>Изменить страницу акций</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Ошибка при сохранении!</div>'?>

    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div>
                <img src="/template/admin/images/icons/sale-1.svg" alt="">
            </div>
            <div>
                <h3 class="traning-title mb-0">Изменить страницу акций</h3>
            </div>
        </div>
        <ul class="nav_button">
            <li><input type="submit" name="save_page" value="Сохранить" class="button save button-green-rounding"></li>
            <li class="nav_button__last"><a class="button button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/sales/">Закрыть</a></li>
        </ul>
    </div>

    <div class="admin_form">
       <div class="row-line">
        <div class="col-1-2">
            <p><label>Заголовок H1</label><input type="text" value="<?php if($param != null) echo $param['h1'];?>" name="sale_page[h1]" placeholder=""></p>
        
            <div class="width-100">
                <label>Статус</label>
            <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="sale_page[enable_page]" type="radio" value="1" <?php if($param != null && $param['enable_page'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="sale_page[enable_page]" type="radio" value="0" <?php if($param == null || $param['enable_page'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
            </div>
            <!-- <p><label>Meta keys:</label><textarea name="sale_page[meta_keys]"><?php if($param != null) echo $param['meta_keys'];?></textarea></p> -->
            
           
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        </div>
        <div class="col-1-2">
            <p><label>Заголовок Title</label><input type="text" name="sale_page[title]" placeholder="Заголовок страницы" value="<?php if($param != null) echo $param['title'];?>" required="required"></p>
        
            <p><label>Description</label><input type="text" name="sale_page[meta_desc]"><?php if($param != null) echo $param['meta_desc'];?></p>
        </div>
        
        <div class="col-1-1">
        <h4 class="h4-border mt-20">Блок HERO</h4>
        </div>
        
        <div class="col-1-2">
            <div class="width-100"><label>Фоновой изображение:</label>
            <div class="fon-wrap2">
            <input id="fieldID" type="text" name="sale_page[params][hero]" value="<?php if(isset($param['params']['hero'])) echo $param['params']['hero'];?>" >
            <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=fieldID&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="">Выбрать изображение</a>
            </div>
            </div>
            
            <div class="width-100"><label>Позиция фона:</label>
            <div class="select-wrap">
            <select name="sale_page[params][position]">
            <option value="center center"<?php if(isset($param['params']['position']) && $param['params']['position'] == 'center center') echo ' selected="selected"';?>>По центру</option>
            <option value="center top"<?php if(isset($param['params']['position']) && $param['params']['position'] == 'center top') echo ' selected="selected"';?>>Сверху и по центру </option>
            </select>
            </div>
            </div>
            <p class="width-100"><label>Высота блока, px:</label><input type="text" name="sale_page[params][heroheigh]" value="<?php if(isset($param['params']['heroheigh'])) echo $param['params']['heroheigh']; else echo '';?>"></p>
            <p class="width-100"><label>Высота блока на мобильных, px:</label><input type="text" name="sale_page[params][heromobileheigh]" value="<?php if(isset($param['params']['heromobileheigh'])) echo $param['params']['heromobileheigh']; else echo '';?>"></p>
        
        </div>
        
        
        <div class="col-1-2">
            <p class="width-100"><label>Оверлей цвет:</label><input type="color" name="sale_page[params][overlaycolor]" value="<?php if(isset($param['params']['overlaycolor'])) echo $param['params']['overlaycolor']; else echo '#000000'?>"></p>
            <div class="width-100"><label>Оверлей прозрачность:</label>
            <div class="select-wrap">
            <select name="sale_page[params][overlay]">
            <option value="1.0"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '1.0') echo ' selected="selected"';?>>1.0</option>
            <option value="0.9"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.9') echo ' selected="selected"';?>>0.9</option>
            <option value="0.8"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.8') echo ' selected="selected"';?>>0.8</option>
            <option value="0.7"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.7') echo ' selected="selected"';?>>0.7</option>
            <option value="0.6"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.6') echo ' selected="selected"';?>>0.6</option>
            <option value="0.5"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.5') echo ' selected="selected"';?>>0.5</option>
            <option value="0.4"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.4') echo ' selected="selected"';?>>0.4</option>
            <option value="0.3"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.3') echo ' selected="selected"';?>>0.3</option>
            <option value="0.2"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.2') echo ' selected="selected"';?>>0.2</option>
            <option value="0.1"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.1') echo ' selected="selected"';?>>0.1</option>
            <option value="0.0"<?php if(isset($param['params']['overlay']) && $param['params']['overlay'] == '0.0') echo ' selected="selected"';?>>0.0</option>
            </select>
            </div>
            </div>
            
            
            <p class="width-100"><label>Цвет заголовка:</label><input type="color" name="sale_page[params][color]" value="<?php if(isset($param['params']['color'])) echo $param['params']['color']; else echo '#000000'?>"></p>
            <p class="width-100"><label>Размер заголовка, px:</label><input type="text" name="sale_page[params][fontsize]" value="<?php if(isset($param['params']['fontsize'])) echo $param['params']['fontsize']; else echo '20';?>"></p>
            <p class="width-100"><label>Размер заголовка для мобильных, px:</label><input type="text" name="sale_page[params][fontsize_mobile]" value="<?php if(isset($param['params']['fontsize_mobile'])) echo $param['params']['fontsize_mobile']; else echo '20';?>"></p>
        
        </div>
        
        
        
       <div class="col-1-1">
            <h4 class="h4-border mt-20">Содержание страницы</h4>
           <p><textarea class="editor" rows="4" cols="45" name="content"><?php echo $page['page_text'];?></textarea></p>
           <p><label>HTML код на странице:</label><textarea rows="4" cols="45" name="code"><?php echo $page['page_code'];?></textarea></p>
       </div>
       </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>