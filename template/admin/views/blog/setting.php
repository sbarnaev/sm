<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки блога</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки блога</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки блога</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="saveblog" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <h4 class="h4-border">Основное</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p><label>Заголовок H1:</label><input type="text" name="blog[params][h1]" value="<?php echo $params['params']['h1']?>"></p>

                    <p>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </p>
                    
                    <p><label>Комментарии: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="blog[params][comments]" type="radio" value="1" <?php if($params['params']['comments'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="blog[params][comments]" type="radio" value="0" <?php if($params['params']['comments'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>

                    </p>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                <div class="col-1-2">

                    <p><label>Код комментариев (в head):</label><textarea rows="10" cols="45" name="blog[params][commenthead]"><?php echo $params['params']['commenthead']?></textarea></p>
                    <p class="mb-0"><label>Код комментариев (в body):</label><textarea rows="10" cols="45" name="blog[params][commentcode]"><?php echo $params['params']['commentcode']?></textarea></p>

                </div>
            </div>
            <h4 class="h4-border mt-20">Внешний вид</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p><label>Показывать обложку записи в тексте: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="blog[params][show_cover]" type="radio" value="1" <?php if($params['params']['show_cover'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="blog[params][show_cover]" type="radio" value="0" <?php if($params['params']['show_cover'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </p>

                    <p><label>Показывать категорию: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="blog[params][show_cat]" type="radio" value="1" <?php if($params['params']['show_cat'] == 1) echo 'checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="blog[params][show_cat]" type="radio" value="0" <?php if($params['params']['show_cat'] == 0) echo 'checked';?>><span>Нет</span></label>
                    </span>
                    </p>

                    <div class="width-100"><label>Показывать дату создания: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="blog[params][show_create_date]" type="radio" value="1" <?php if($params['params']['show_create_date'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="blog[params][show_create_date]" type="radio" value="0" <?php if($params['params']['show_create_date'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </div>
                </div>
				
				<div class="col-1-2">
                    <div class="width-100"><label>Показывать дату публикации: </label>
                            <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="blog[params][show_start_date]" type="radio" value="1" <?php if(isset($params['params']['show_start_date']) && $params['params']['show_start_date'] == 1) echo 'checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="blog[params][show_start_date]" type="radio" value="0" <?php if(!isset($params['params']['show_start_date']) || $params['params']['show_start_date'] == 0) echo 'checked';?>><span>Откл</span></label>
                        </span>
                    </div>

                    <p><label>Записей на страницу:</label><input type="text" size="4" name="blog[params][postcount]" value="<?php echo $params['params']['postcount']?>"></p>
                    
                    <div class="width-100"><label>Сортировка:</label>
                        <div class="select-wrap">
                            <select name="blog[params][sort]">
                                <option value="post_id"<?php if(isset($params['params']['sort']) && $params['params']['sort'] == 'post_id') echo ' selected="selected"';?>>По ID записи</option>
                                <option value="sort"<?php if(isset($params['params']['sort']) && $params['params']['sort'] == 'sort') echo ' selected="selected"';?>>По порядку</option>
								<option value="start_date"<?php if(isset($params['params']['sort']) && $params['params']['sort'] == 'start_date') echo ' selected="selected"';?>>По дате публикации</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <h4 class="h4-border mt-20">Блок HERO</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <div class="width-100"><label>Фоновое изображение:</label>
                	    <div class="fon-wrap2">
                      <input id="fieldID" type="text" name="blog[params][hero]" value="<?php if(isset($params['params']['hero'])) echo $params['params']['hero'];?>" >
                	    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=fieldID&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                      </div>
                    </div>
    
                    <div class="width-100"><label>Позиция фона:</label>
                   <div class="select-wrap">
                    <select name="blog[params][position]">
                        <option value="center center"<?php if(isset($params['params']['position']) && $params['params']['position'] == 'center center') echo ' selected="selected"';?>>По центру</option>
                        <option value="center top"<?php if(isset($params['params']['position']) && $params['params']['position'] == 'center top') echo ' selected="selected"';?>>Сверху и по центру </option>
                    </select>
                        </div>
                    </div>
                    <p class="width-100"><label>Высота блока, px:</label><input type="text" name="blog[params][heroheigh]" value="<?php if(isset($params['params']['heroheigh'])) echo $params['params']['heroheigh']; else echo '';?>"></p>
                    <p class="width-100"><label>Высота блока на мобильных, px:</label><input type="text" name="blog[params][heromobileheigh]" value="<?php if(isset($params['params']['heromobileheigh'])) echo $params['params']['heromobileheigh']; else echo '';?>"></p>

                </div>
            <div class="col-1-2">
                <p class="width-100"><label>Оверлей цвет:</label><input type="color" name="blog[params][overlaycolor]" value="<?php if(isset($params['params']['overlaycolor'])) echo $params['params']['overlaycolor']; else echo '#000000'?>"></p>
                <div class="width-100"><label>Оверлей прозрачность:</label>
                    <div class="select-wrap">
                    <select name="blog[params][overlay]">
                    <option value="1.0"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '1.0') echo ' selected="selected"';?>>1.0</option>
                    <option value="0.9"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.9') echo ' selected="selected"';?>>0.9</option>
                    <option value="0.8"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.8') echo ' selected="selected"';?>>0.8</option>
                    <option value="0.7"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.7') echo ' selected="selected"';?>>0.7</option>
                    <option value="0.6"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.6') echo ' selected="selected"';?>>0.6</option>
                    <option value="0.5"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.5') echo ' selected="selected"';?>>0.5</option>
                    <option value="0.4"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.4') echo ' selected="selected"';?>>0.4</option>
                    <option value="0.3"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.3') echo ' selected="selected"';?>>0.3</option>
                    <option value="0.2"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.2') echo ' selected="selected"';?>>0.2</option>
                    <option value="0.1"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.1') echo ' selected="selected"';?>>0.1</option>
                    <option value="0.0"<?php if(isset($params['params']['overlay']) && $params['params']['overlay'] == '0.0') echo ' selected="selected"';?>>0.0</option>
                </select>
                </div>
                </div>
                
                
                <p class="width-100"><label>Заголовок блока:</label><input type="text" name="blog[params][heroheader]" value="<?php if(isset($params['params']['heroheader'])) echo $params['params']['heroheader']; else echo ''?>"></p>
                <p class="width-100"><label>Цвет заголовка:</label><input type="color" name="blog[params][color]" value="<?php if(isset($params['params']['color'])) echo $params['params']['color']; else echo '#000000'?>"></p>
                <p class="width-100"><label>Размер заголовка, px:</label><input type="text" name="blog[params][fontsize]" value="<?php if(isset($params['params']['fontsize'])) echo $params['params']['fontsize']; else echo '20';?>"></p>
                <p class="width-100"><label>Размер заголовка для мобильных, px:</label><input type="text" name="blog[params][fontsize_mobile]" value="<?php if(isset($params['params']['fontsize_mobile'])) echo $params['params']['fontsize_mobile']; else echo '20';?>"></p>

             </div>

            </div>
            
            
            <h4 class="h4-border mt-20">SEO</h4>
            <div class="col-1-2">
                <p><label>Title:</label><input type="text" name="blog[params][title]" value="<?php echo $params['params']['title']?>"></p>
                <p><label>Meta Desc:</label><textarea name="blog[params][desc]"><?php echo $params['params']['desc']?></textarea></p>
                <p><label>Meta Keys:</label><textarea name="blog[params][keys]"><?php echo $params['params']['keys']?></textarea></p>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>